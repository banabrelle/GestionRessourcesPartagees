<?php

namespace App\Http\Controllers;

use App\Models\{Reservation,Ressource,Personnel,HistoriqueEmprunt,AiSuggestion};
use App\Services\{ConflictDetectionService,AiSuggestionService,NotificationService};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
       public function __construct(
        private ConflictDetectionService $conflictSvc,
        private AiSuggestionService      $aiSvc,
        private NotificationService      $notifSvc,
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $perso = $user->personnel;
        $query = Reservation::with(['personnel.user','ressource'])->orderByDesc('created_at');

        if ($user->hasRole('personnel') && $perso) {
            $query->where('personnel_id',$perso->id);
        } elseif ($user->hasRole('chef_division') && $perso) {
            $query->whereHas('personnel',fn($q)=>$q->where('division_id',$perso->division_id));
        }
        if ($request->filled('statut')) $query->where('statut',$request->statut);
        if ($request->filled('ressource_id')) $query->where('ressource_id',$request->ressource_id);

        $reservations = $query->paginate(15)->withQueryString();
        $ressources   = Ressource::orderBy('nom')->get();
        return view('reservations.index',compact('reservations','ressources'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['personnel.user','ressource','validePar.user',
                            'valideParAdmin.user','historiqueEmprunts','aiSuggestions']);
        return view('reservations.show',compact('reservation'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'ressource_id' => 'required|exists:ressources,id',
            'date_debut'   => 'required|date|after:now',
            'date_fin'     => 'required|date|after:date_debut',
            'motif'        => 'required|string|min:10|max:500',
        ],[
            'date_debut.after' => 'La date de début doit être dans le futur.',
            'date_fin.after'   => 'La date de fin doit être après le début.',
            'motif.min'        => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        $personnel = auth()->user()->personnel;
        if (!$personnel) return back()->withErrors(['error'=>'Profil personnel introuvable.']);

        $ressource = Ressource::findOrFail($v['ressource_id']);

        // ── Détection conflit (contrainte UML {no overlap}) ──
        $conflit = $this->conflictSvc->detecter($ressource,$v['date_debut'],$v['date_fin']);

        if ($conflit) {
            // Générer suggestions 
            $suggestions = $this->aiSvc->suggerer($ressource,$v['date_debut'],$v['date_fin'],$personnel);
            return back()->with('conflit',true)
                         ->with('suggestions',$suggestions)
                         ->withInput();
        }

        // ── Vérifier contrainte logicielle {nombre_licence >= réservations actives}
        if ($ressource->type_ressource === 'logiciel' && $ressource->ficheLogicielle) {
            if (!$ressource->ficheLogicielle->hasClesDisponibles()) {
                return back()->withErrors(['error'=>'Toutes les licences sont utilisées.'])->withInput();
            }
        }

        $reservation = Reservation::create([
            ...$v,
            'personnel_id' => $personnel->id,
            'statut'       => 'en_attente',
        ]);

        $ressource->update(['etat'=>'en_attente_validation']);

        // Notifier le chef de division
        if ($personnel->division?->chef) {
            $this->notifSvc->envoyer(
                $personnel->division->chef->user,
                'reservation_soumise',
                'Nouvelle demande de réservation',
                "{$personnel->nom_complet} demande '{$ressource->nom}'.",
                ['reservation_id'=>$reservation->id]
            );
        }

        return redirect()->route('reservations.show',$reservation)
            ->with('success','Demande soumise. En attente de validation du chef.');
    }

    public function validerChef(Request $request, Reservation $reservation)
    {
        abort_unless(auth()->user()->hasRole('chef_division'),403);
        abort_unless($reservation->statut === 'en_attente',400,'Statut invalide.');

        $chef = auth()->user()->personnel;
        $reservation->update([
            'statut'          => 'valide_chef',
            'valide_par'      => $chef?->id,
            'date_val_chef'   => now(),
            'commentaire_chef'=> $request->commentaire,
        ]);

        // Notifier les admins
        \App\Models\User::role('administrateur')->each(fn($u)=>
            $this->notifSvc->envoyer($u,'valide_chef','Réservation à finaliser',
                "Réservation #{$reservation->id} validée par le chef — en attente admin.",
                ['reservation_id'=>$reservation->id])
        );
        // Notifier le demandeur
        $this->notifSvc->envoyer(
            $reservation->personnel->user,'valide_chef',
            'Votre demande avance !',
            "La réservation pour '{$reservation->ressource->nom}' est validée par le chef.",
            ['reservation_id'=>$reservation->id]
        );

        return back()->with('success','Réservation validée. Transmise à l\'administrateur.');
    }

    public function validerAdmin(Request $request, Reservation $reservation)
    {
        abort_unless(auth()->user()->hasRole('administrateur'),403);
        abort_unless($reservation->statut === 'valide_chef',400,'Statut invalide.');

        $admin = auth()->user()->personnel;
        $reservation->update([
            'statut'           => 'valide_admin',
            'valide_par_admin' => $admin?->id,
            'date_val_admin'   => now(),
            'commentaire_admin'=> $request->commentaire,
        ]);

        // Marquer ressource comme occupée
        $reservation->ressource->update(['etat'=>'occupe']);

        // Générer bon d'emprunt
        $bon = HistoriqueEmprunt::create([
            'reservation_id'     => $reservation->id,
            'numero_bon'         => HistoriqueEmprunt::genererNumeroBon(),
            'date_retour_prevue' => $reservation->date_fin,
            'etat_retour'        => 'non_rendu',
        ]);
        // Générer PDF du bon
        app(\App\Services\BonEmpruntService::class)->genererPdf($bon);

        // Notifier le demandeur
        $this->notifSvc->envoyer(
            $reservation->personnel->user,'valide_admin',
            'Réservation approuvée !',
            "Votre réservation pour '{$reservation->ressource->nom}' est confirmée. Bon N° {$bon->numero_bon} disponible.",
            ['reservation_id'=>$reservation->id,'bon_id'=>$bon->id]
        );

        return back()->with('success','Réservation validée. Bon d\'emprunt généré.');
    }

    public function rejeterChef(Request $request, Reservation $reservation)
    {
        $request->validate(['commentaire'=>'required|string|min:5']);
        abort_unless(auth()->user()->hasRole('chef_division'),403);
        $reservation->update(['statut'=>'rejete_chef','commentaire_chef'=>$request->commentaire]);
        $reservation->ressource->update(['etat'=>'disponible']);
        $this->notifSvc->envoyer($reservation->personnel->user,'rejete','Demande rejetée',
            "Votre demande pour '{$reservation->ressource->nom}' a été rejetée. Motif : {$request->commentaire}.",
            ['reservation_id'=>$reservation->id]);
        return back()->with('success','Réservation rejetée.');
    }

    public function rejeterAdmin(Request $request, Reservation $reservation)
    {
        $request->validate(['commentaire'=>'required|string|min:5']);
        abort_unless(auth()->user()->hasRole('administrateur'),403);
        $reservation->update(['statut'=>'rejete_admin','commentaire_admin'=>$request->commentaire]);
        $reservation->ressource->update(['etat'=>'disponible']);
        $this->notifSvc->envoyer($reservation->personnel->user,'rejete','Demande rejetée par l\'admin',
            "Votre demande a été rejetée. Motif : {$request->commentaire}.",
            ['reservation_id'=>$reservation->id]);
        return back()->with('success','Réservation rejetée.');
    }

    public function annuler(Reservation $reservation)
    {
        $personnel = auth()->user()->personnel;
        abort_unless($reservation->personnel_id === $personnel?->id,403);
        abort_unless($reservation->statut === 'en_attente',400,'Impossible d\'annuler.');
        $reservation->update(['statut'=>'annule']);
        $reservation->ressource->update(['etat'=>'disponible']);
        return back()->with('success','Demande annulée.');
    }

}
