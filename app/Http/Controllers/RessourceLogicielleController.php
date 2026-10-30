<?php

namespace App\Http\Controllers;

use App\Models\{RessourceLogicielle, Ressource};
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RessourceLogicielleController extends Controller
{
    public function index(Request $request)
    {
        $query = RessourceLogicielle::with('ressource.division')
                                      ->orderBy('date_fin_licence');

        // Filtre expiration imminente
        if ($request->filled('expiration')) {
            $query->whereNotNull('date_fin_licence')
                  ->where('date_fin_licence','>=',now())
                  ->where('date_fin_licence','<=',now()->addDays((int)$request->expiration));
        }

        // Filtre type licence
        if ($request->filled('type_licence')) {
            $query->where('type_licence', $request->type_licence);
        }

        // Recherche
        if ($request->filled('search')) {
            $query->whereHas('ressource', fn($q) =>
                $q->where('nom','like','%'.$request->search.'%')
                  ->orWhere('numero_serie','like','%'.$request->search.'%')
            )->orWhere('editeur','like','%'.$request->search.'%');
        }

        $licences = $query->paginate(15)->withQueryString();

        // Ressources logicielles sans fiche licence
        $sansFiche = Ressource::logiciel()
                              ->doesntHave('ficheLogicielle')
                              ->orderBy('nom')
                              ->get();

        // Compteurs pour onglets
        $compteurs = [
            'toutes'   => RessourceLogicielle::count(),

            'expir30'  => RessourceLogicielle::whereNotNull('date_fin_licence')
                            ->where('date_fin_licence','>=',now())
                            ->where('date_fin_licence','<=',now()->addDays(30))
                            ->count(),

            'expir7'   => RessourceLogicielle::whereNotNull('date_fin_licence')
                            ->where('date_fin_licence','>=',now())
                            ->where('date_fin_licence','<=',now()->addDays(7))
                            ->count(),

            'saturees' => RessourceLogicielle::whereColumn('cles_utilisees','>=','nombre_licence')
                            ->count(),
        ];

        return view('licences.index', compact('licences','sansFiche','compteurs'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'ressource_id'       => 'required|exists:ressources,id|unique:ressources_logicielles,ressource_id',
            'categorie'          => 'nullable|string|max:150',
            'type_licence'       => 'required|in:proprietaire,libre,freemium,abonnement',
            'nombre_licence'     => 'required|integer|min:1',
            'editeur'            => 'nullable|string|max:150',
            'version'            => 'nullable|string|max:50',
            'cle_produit'        => 'nullable|string',
            'date_debut_licence' => 'nullable|date',
            'date_fin_licence'   => 'nullable|date|after_or_equal:date_debut_licence',
        ],[
            'ressource_id.unique' => 'Cette ressource a déjà une fiche licence.',
        ]);

        RessourceLogicielle::create($v);

        return back()->with('success','Fiche licence créée avec succès.');
    }

    public function update(Request $request, RessourceLogicielle $licence)
    {
        $v = $request->validate([
            'categorie'          => 'nullable|string|max:150',
            'type_licence'       => 'required|in:proprietaire,libre,freemium,abonnement',
            'nombre_licence'     => 'required|integer|min:1',
            'cles_utilisees'     => 'required|integer|min:0|lte:nombre_licence',
            'editeur'            => 'nullable|string|max:150',
            'version'            => 'nullable|string|max:50',
            'date_debut_licence' => 'nullable|date',
            'date_fin_licence'   => 'nullable|date|after_or_equal:date_debut_licence',
        ],[
            'cles_utilisees.lte' => 'Les clés utilisées ne peuvent dépasser le nombre total.',
        ]);

        // Réinitialiser l'alerte si date modifiée
        if ($request->filled('date_fin_licence') &&
            $licence->date_fin_licence?->format('Y-m-d') !== $request->date_fin_licence) {
            $v['alerte_envoyee'] = false;
        }

        $licence->update($v);

        return back()->with('success','Licence mise à jour.');
    }

    public function allouer(RessourceLogicielle $licence)
    {
        if (!$licence->hasClesDisponibles()) {
            return back()->withErrors(['error' => 'Toutes les licences sont utilisées.']);
        }

        $licence->increment('cles_utilisees');

        return back()->with('success',
            'Clé allouée. Restantes : '.$licence->fresh()->cles_restantes.' / '.$licence->nombre_licence
        );
    }

    public function liberer(RessourceLogicielle $licence)
    {
        if ($licence->cles_utilisees <= 0) {
            return back()->withErrors(['error' => 'Aucune clé à libérer.']);
        }

        $licence->decrement('cles_utilisees');

        return back()->with('success','Clé libérée.');
    }

    public function destroy(RessourceLogicielle $licence)
    {
        $licence->delete();

        return redirect()->route('licences.index')
                         ->with('success','Fiche licence supprimée.');
    }
}