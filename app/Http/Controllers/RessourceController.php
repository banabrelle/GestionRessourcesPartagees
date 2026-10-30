<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ressource, RessourceMaterielle, RessourceLogicielle, Division, Tag};

class RessourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Ressource::with(['division', 'tags', 'ficheMaterielle', 'ficheLogicielle'])
                          ->orderBy('nom');

        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }
        if ($request->filled('type_ressource')) {
            $query->where('type_ressource', $request->type_ressource);
        }
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }
        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('numero_serie', 'like', '%' . $request->search . '%')
            );
        }

        $ressources = $query->paginate(20)->withQueryString();
        $divisions  = Division::orderBy('code')->get();
        $tags       = Tag::orderBy('libelle')->get();

        $compteurs = [
            'tous'           => Ressource::count(),
            'disponible'     => Ressource::where('etat', 'disponible')->count(),
            'occupe'         => Ressource::where('etat', 'occupe')->count(),
            'en_attente'     => Ressource::where('etat', 'en_attente_validation')->count(),
            'en_maintenance' => Ressource::where('etat', 'en_maintenance')->count(),
        ];

        return view('ressources.index', compact('ressources', 'divisions', 'tags', 'compteurs'));
    }

    public function show(Ressource $ressource)
    {
        $ressource->load(['division', 'tags', 'ficheMaterielle', 'ficheLogicielle',
                          'reservations.personnel.user']);
        $reservationsActives = $ressource->reservations()
            ->whereIn('statut', ['en_attente', 'valide_chef', 'valide_admin'])
            ->with('personnel.user')
            ->orderBy('date_debut')
            ->get();

        return view('ressources.show', compact('ressource', 'reservationsActives'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'nom'            => 'required|string|max:255',
            'description'    => 'nullable|string',
            'type_ressource' => 'required|in:materiel,logiciel',
            'etat'           => 'nullable|in:disponible,occupe,en_attente_validation,en_maintenance,expire',
            'mode_partage'   => 'required|in:exclusif,partage,reserve',
            'division_id'    => 'nullable|exists:divisions,id',
            'numero_serie'   => 'nullable|string|max:100|unique:ressources',
            'tags'           => 'nullable|array',
            'tags.*'         => 'exists:tags,id',
            // Matériel
            'marque'           => 'nullable|string|max:100',
            'modele'           => 'nullable|string|max:100',
            'date_acquisition' => 'nullable|date',
            'capacite'         => 'nullable|string|max:100',
            'valeur_achat'     => 'nullable|numeric|min:0',
            // Logiciel
            'categorie'          => 'nullable|string|max:150',
            'type_licence'       => 'nullable|in:proprietaire,libre,freemium,abonnement',
            'nombre_licence'     => 'nullable|integer|min:1',
            'editeur'            => 'nullable|string|max:150',
            'version'            => 'nullable|string|max:50',
            'date_debut_licence' => 'nullable|date',
            'date_fin_licence'   => 'nullable|date|after:date_debut_licence',
        ]);

        // Valeur par défaut pour etat
        $v['etat'] = $v['etat'] ?? 'disponible';

        $ressource = Ressource::create([
            'nom'            => $v['nom'],
            'description'    => $v['description'] ?? null,
            'type_ressource' => $v['type_ressource'],
            'etat'           => $v['etat'],
            'mode_partage'   => $v['mode_partage'],
            'division_id'    => $v['division_id'] ?? null,
            'numero_serie'   => $v['numero_serie'] ?? null,
        ]);

        // Tags
        if (!empty($v['tags'])) {
            $ressource->tags()->sync($v['tags']);
        }

        // Fiche matérielle
        if ($v['type_ressource'] === 'materiel') {
            RessourceMaterielle::create([
                'ressource_id'     => $ressource->id,
                'marque'           => $v['marque'] ?? null,
                'modele'           => $v['modele'] ?? null,
                'date_acquisition' => $v['date_acquisition'] ?? null,
                'capacite'         => $v['capacite'] ?? null,
                'valeur_achat'     => $v['valeur_achat'] ?? null,
            ]);
        }

        // Fiche logicielle
        if ($v['type_ressource'] === 'logiciel') {
            RessourceLogicielle::create([
                'ressource_id'       => $ressource->id,
                'categorie'          => $v['categorie'] ?? null,
                'type_licence'       => $v['type_licence'] ?? 'proprietaire',
                'nombre_licence'     => $v['nombre_licence'] ?? 1,
                'editeur'            => $v['editeur'] ?? null,
                'version'            => $v['version'] ?? null,
                'date_debut_licence' => $v['date_debut_licence'] ?? null,
                'date_fin_licence'   => $v['date_fin_licence'] ?? null,
            ]);
        }

        return back()->with('success', 'Ressource "' . $ressource->nom . '" créée avec succès.');
    }

    public function update(Request $request, Ressource $ressource)
    {
        $v = $request->validate([
            'nom'          => 'required|string|max:255',
            'description'  => 'nullable|string',
            'etat'         => 'required|in:disponible,occupe,en_attente_validation,en_maintenance,expire',
            'mode_partage' => 'required|in:exclusif,partage,reserve',
            'division_id'  => 'nullable|exists:divisions,id',
            'numero_serie' => 'nullable|string|max:100|unique:ressources,numero_serie,' . $ressource->id,
            'tags'         => 'nullable|array',
            'tags.*'       => 'exists:tags,id',
            // Matériel
            'marque'           => 'nullable|string|max:100',
            'modele'           => 'nullable|string|max:100',
            'date_acquisition' => 'nullable|date',
            'valeur_achat'     => 'nullable|numeric|min:0',
            // Logiciel
            'nombre_licence'   => 'nullable|integer|min:1',
            'date_fin_licence' => 'nullable|date',
        ]);

        $ressource->update([
            'nom'          => $v['nom'],
            'description'  => $v['description'] ?? null,
            'etat'         => $v['etat'],
            'mode_partage' => $v['mode_partage'],
            'division_id'  => $v['division_id'] ?? null,
            'numero_serie' => $v['numero_serie'] ?? null,
        ]);

        if (!empty($v['tags'])) {
            $ressource->tags()->sync($v['tags']);
        }

        if ($ressource->type_ressource === 'materiel' && $ressource->ficheMaterielle) {
            $ressource->ficheMaterielle->update([
                'marque'           => $v['marque'] ?? null,
                'modele'           => $v['modele'] ?? null,
                'date_acquisition' => $v['date_acquisition'] ?? null,
                'valeur_achat'     => $v['valeur_achat'] ?? null,
            ]);
        }

        if ($ressource->type_ressource === 'logiciel' && $ressource->ficheLogicielle) {
            $ressource->ficheLogicielle->update([
                'nombre_licence'   => $v['nombre_licence'] ?? null,
                'date_fin_licence' => $v['date_fin_licence'] ?? null,
            ]);
        }

        return back()->with('success', 'Ressource "' . $ressource->nom . '" modifiée avec succès.');
    }

    public function updateEtat(Request $request, Ressource $ressource)
    {
        $request->validate(['etat' => 'required|in:disponible,occupe,en_attente_validation,en_maintenance,expire']);
        $ressource->update(['etat' => $request->etat]);
        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(Ressource $ressource)
    {
        $ressource->delete();
        return redirect()->route('ressources.index')->with('success', 'Ressource supprimée.');
    }
}