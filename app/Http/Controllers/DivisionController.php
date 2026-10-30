<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Liste de toutes les divisions avec compteurs
     * Accessible depuis le module Parametres (admin seulement)
     */
    public function index(Request $request)
    {
        $query = Division::withCount(['chef', 'ressources', 'services']);

        // // Filtre par statut actif/inactif
        // if ($request->filled('statut')) {
        //     $query->where('is_active', $request->statut === 'actif');
        // }


        // Recherche par nom ou code
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%");
            });
        }

        $divisions     = $query->orderBy('code')->paginate(20)->withQueryString();
        return redirect()->route('parametres.index', ['tab' => 'divisions']);
    }

    /**
     * Creer une division via MODALE - retourne JSON
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'         => 'required|string|max:10|unique:divisions,code',
            'nom'          => 'required|string|max:255',
            'description'  => 'nullable|string',
        ]);

        $division = Division::create($validated);

        AuditLog::enregistrer(
            'division.create',
            'Division', $division->id,
            null, $division->toArray(),
            "Creation division: {$division->code} - {$division->nom}"
        );

        return response()->json([
            'success'  => true,
            'message'  => "Division {$division->code} creee avec succes.",
            'division' => $division,
        ]);
    }

    /**
     * Modifier une division via MODALE - retourne JSON
     */
    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'code'         => 'required|string|max:10|unique:divisions,code,' . $division->id,
            'nom'          => 'required|string|max:255',
            'description'  => 'nullable|string',
            'localisation' => 'nullable|string|max:100',
            'is_active'    => 'boolean',
        ]);

        $ancienne = $division->toArray();
        $division->update($validated);

        AuditLog::enregistrer(
            'division.update', 'Division', $division->id,
            $ancienne, $division->fresh()->toArray(),
            "Modification division: {$division->code}"
        );

        return response()->json([
            'success'  => true,
            'message'  => "Division {$division->code} mise a jour.",
            'division' => $division->fresh(),
        ]);
    }

    /**
     * Supprimer une division - verifie les dependances
     */
    public function destroy(Division $division)
    {
        // Verifier si la division a du personnel
        if ($division->personnel()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible : du personnel est rattache a cette division.',
            ], 422);
        }

        // Verifier si la division a des ressources
        if ($division->ressources()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible : des ressources appartiennent a cette division.',
            ], 422);
        }

        // Verifier si la division a des services
        if ($division->services()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible : des services sont rattaches a cette division.',
            ], 422);
        }

        AuditLog::enregistrer(
            'division.delete', 'Division', $division->id, $division->toArray()
        );
        $division->delete();

        return response()->json(['success' => true, 'message' => 'Division supprimee.']);
    }

    // /**
    //  * Activer / Desactiver une division (toggle AJAX)
    //  */
    // public function toggleActif(Division $division)
    // {
    //     $division->update(['is_active' => !$division->is_active]);
    //     $etat = $division->fresh()->is_active ? 'activee' : 'desactivee';
    //     AuditLog::enregistrer('division.toggle', 'Division', $division->id);
    //     return response()->json([
    //         'success'   => true,
    //         'message'   => "Division {$etat} avec succes.",
    //     ]);
    // }
}
