<?php
namespace App\Http\Controllers;
use App\Models\{Ressource,Reservation,Personnel,Division,RessourceLogicielle,AiSuggestion};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    /** Tableau de bord global des statistiques */
    public function index()
    {
        return view('statistiques.index',[
            'stats_ressources'   => $this->statsRessources(),
            'stats_reservations' => $this->statsReservations(),
            'stats_utilisateurs' => $this->statsUtilisateurs(),
            'stats_globales'     => $this->statsGlobales(),
            'stats_ia'           => $this->statsIa(),
        ]);
    }

    /** Statistiques d'utilisation des ressources */
    public function ressources()
    {
        // Taux d'occupation (heures utilisées / heures disponibles)
        $plusUtilisees = DB::table('reservations')
            ->join('ressources','reservations.ressource_id','=','ressources.id')
            ->where('reservations.statut','valide_admin')
            ->select(
                'ressources.id','ressources.nom','ressources.type_ressource',
                DB::raw('COUNT(*) as nb_reservations'),
                DB::raw('SUM(TIMESTAMPDIFF(HOUR,date_debut,date_fin)) as heures_total')
            )->groupBy('ressources.id','ressources.nom','ressources.type_ressource')
            ->orderByDesc('nb_reservations')->limit(10)->get();

        // Ressources dormantes (aucune réservation depuis 30 jours)
        $dormantes = Ressource::whereDoesntHave('reservations',
            fn($q)=>$q->where('created_at','>=',now()->subDays(30)))->get();

        // Taux d'occupation par ressource (en %)
        $tauxOccupation = Ressource::withCount([
            'reservations as nb_total',
            'reservations as nb_validees'=>fn($q)=>$q->where('statut','valide_admin'),
        ])->paginate(20);

        return view('statistiques.ressources',compact('plusUtilisees','dormantes','tauxOccupation'));
    }

    /** Statistiques par division */
    public function divisions()
    {
        $statsDiv = Division::all()->map(function($div) {
            $nb  = Reservation::whereHas('personnel',fn($q)=>$q->where('division_id',$div->id))->count();
            $val = Reservation::whereHas('personnel',fn($q)=>$q->where('division_id',$div->id))->where('statut','valide_admin')->count();
            $div->nb_reservations = $nb;
            $div->nb_validees     = $val;
            $div->taux_validation = $nb > 0 ? round($val/$nb*100) : 0;
            $div->nb_personnels   = Personnel::where('division_id',$div->id)->count();
            return $div;
        })->sortByDesc('nb_reservations');

        $repartition = DB::table('reservations')
            ->join('personnels','reservations.personnel_id','=','personnels.id')
            ->join('divisions','personnels.division_id','=','divisions.id')
            ->select('divisions.nom','divisions.code',DB::raw('COUNT(*) as total'))
            ->groupBy('divisions.id','divisions.nom','divisions.code')
            ->orderByDesc('total')->get();

        return view('statistiques.divisions',compact('statsDiv','repartition'));
    }

    /** Statistiques sur les réservations */
    public function reservations()
    {
        $total    = Reservation::count();
        $validees = Reservation::where('statut','valide_admin')->count();
        $rejetees = Reservation::whereIn('statut',['rejete_chef','rejete_admin'])->count();
        $attente  = Reservation::where('statut','en_attente')->count();
        $conflits = Reservation::where('conflit_detecte',true)->count();
        $tempsVal = DB::table('reservations')->whereNotNull('date_val_admin')
            ->avg(DB::raw('TIMESTAMPDIFF(HOUR,created_at,date_val_admin)'));
        return view('statistiques.reservations',compact('total','validees','rejetees','attente','conflits','tempsVal'));
    }

    /** Statistiques temporelles */
    public function temporelles(Request $request)
    {
        $annee   = $request->get('annee', now()->year);
        $parMois = DB::table('reservations')->whereYear('created_at',$annee)
            ->select(DB::raw('MONTH(created_at) as mois'),DB::raw('COUNT(*) as total'))
            ->groupBy('mois')->orderBy('mois')->get()->keyBy('mois');
        $parHeure = DB::table('reservations')
            ->select(DB::raw('HOUR(date_debut) as heure'),DB::raw('COUNT(*) as total'))
            ->groupBy('heure')->orderBy('heure')->get();
        $parJour  = DB::table('reservations')
            ->select(DB::raw('DAYOFWEEK(date_debut) as jour'),DB::raw('COUNT(*) as total'))
            ->groupBy('jour')->orderBy('jour')->get();
        return view('statistiques.temporelles',compact('parMois','parHeure','parJour','annee'));
    }

    /** Statistiques utilisateurs */
    public function utilisateurs()
    {
        $actifs = Personnel::whereHas('reservations',
            fn($q)=>$q->where('created_at','>=',now()->subDays(30)))->count();
        $plusActifs = Personnel::withCount('reservations')->with('user')
            ->orderByDesc('reservations_count')->paginate(10);
        $historiques = Personnel::with(['user','reservations.ressource'])
            ->withCount('reservations')->orderByDesc('reservations_count')->paginate(15);
        return view('statistiques.utilisateurs',compact('actifs','plusActifs','historiques'));
    }

    /** Statistiques licences */
    public function licences()
    {
        $licences = RessourceLogicielle::with('ressource')->get()->map(fn($l)=>[
            'id'=>$l->id,'nom'=>$l->ressource->nom,'editeur'=>$l->editeur,
            'total'=>$l->nombre_licence,'utilises'=>$l->cles_utilisees,
            'taux'=>$l->taux_utilisation,'expire'=>$l->date_fin_licence,
            'bientot'=>$l->bientot_expire,
        ]);
        $bientotExpirees = $licences->filter(fn($l)=>$l['bientot']);
        return view('statistiques.licences',compact('licences','bientotExpirees'));
    }

    /** Statistiques IA */
    public function ia()
    {
        $total     = AiSuggestion::count();
        $acceptees = AiSuggestion::where('acceptee',true)->count();
        $ignorees  = AiSuggestion::where('ignoree',true)->count();
        $taux      = $total > 0 ? round($acceptees/$total*100) : 0;
        $tempsTotal= AiSuggestion::sum('temps_gagne_minutes');
        $parType   = AiSuggestion::select('type_suggestion',DB::raw('COUNT(*) as n'))
            ->groupBy('type_suggestion')->get();
        return view('statistiques.ia',compact('total','acceptees','ignorees','taux','tempsTotal','parType'));
    }

    // ── Méthodes privées ──────────────────────────────────────
    private function statsGlobales(): array {
        return [
            'total_ressources'   => Ressource::count(),
            'total_reservations' => Reservation::count(),
            'total_personnels'   => Personnel::count(),
            'conflits'           => Reservation::where('conflit_detecte',true)->count(),
            'taux_global'        => Ressource::count() > 0
                ? round(Ressource::occupe()->count()/Ressource::count()*100) : 0,
        ];
    }
    private function statsRessources(): array {
        return [
            'total'         => Ressource::count(),
            'disponibles'   => Ressource::disponible()->count(),
            'occupees'      => Ressource::occupe()->count(),
            'maintenance'   => Ressource::enMaintenance()->count(),
            'par_type'      => Ressource::select('type_ressource',DB::raw('count(*) as n'))->groupBy('type_ressource')->pluck('n','type_ressource'),
        ];
    }
    private function statsReservations(): array {
        return [
            'total'    => Reservation::count(),
            'validees' => Reservation::valides()->count(),
            'rejetees' => Reservation::rejetes()->count(),
            'attente'  => Reservation::enAttente()->count(),
            'conflits' => Reservation::where('conflit_detecte',true)->count(),
        ];
    }
    private function statsUtilisateurs(): array {
        return [
            'actifs' => Personnel::whereHas('reservations',fn($q)=>$q->where('created_at','>=',now()->subDays(30)))->count(),
            'plus_actifs' => Personnel::withCount('reservations')->with('user')->orderByDesc('reservations_count')->take(5)->get(),
        ];
    }
    private function statsIa(): array {
        $total = AiSuggestion::count();
        return ['total'=>$total,'acceptees'=>AiSuggestion::where('acceptee',true)->count(),'taux'=>$total>0?round(AiSuggestion::where('acceptee',true)->count()/$total*100):0];
    }
}

