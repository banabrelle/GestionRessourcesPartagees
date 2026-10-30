<?php

namespace App\Http\Controllers;

use App\Models\Bureau;
use App\Models\Service;
use App\Models\Division;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class BureauController extends Controller
{
    /**
     * Liste de tous les bureaux
     * Liste avec défilement
     */
    public function index(Request $request)
    {
        $query = Bureau::with(['service.division'])
                       ->withCount('service');

        // Filtre par service
        if ($request->filled('service')) {
            $query->where('id_service', $request->service);
        }

        // Filtre par division (via service)
        if ($request->filled('division')) {
            $query->whereHas('service', function ($q) use ($request) {
                $q->where('id_division', $request->division);
            });
        }

        // Filtre statut
        // if ($request->filled('statut')) {
        //     $query->where('is_active', $request->statut === 'actif');
        // }

        // Recherche
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        $bureaux   = $query->orderBy('nom')->paginate(20)->withQueryString();
        $divisions = Division::where('nom', 'like', "%{$request->search}%")->orderBy('nom')->get();
        $services  = Service::where('nom', 'like', "%{$request->search}%")->orderBy('nom')->get();

        return redirect()->route('parametres.index', ['tab' => 'bureaux']);
    }

    /**
     * Créer un bureau via MODALE - retourne JSON
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_service'   => 'required|exists:services,id',
            'code'         => 'required|string|max:20|unique:bureaux,code',
            'nom'          => 'required|string|max:255',
            'description'  => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
        ]);

        $bureau = Bureau::create($validated);

        AuditLog::enregistrer(
            'bureau.create',
            'Bureau',
            $bureau->id,
            null,
            $bureau->toArray(),
            "Creation bureau: {$bureau->nom}"
        );

        return response()->json([
            'success' => true,
            'message' => "Bureau {$bureau->nom} créé avec succès.",
            'bureau'  => $bureau->load('service.division'),
        ]);
    }

    /**
     * Modifier un bureau via MODALE - retourne JSON
     */
    public function update(Request $request, Bureau $bureau)
    {
        $validated = $request->validate([
            'id_service'   => 'required|exists:services,id',
            'nom'          => 'required|string|max:255',
            'description'  => 'nullable|string',
            
        ]);

        $ancienne = $bureau->toArray();
        $bureau->update($validated);

        AuditLog::enregistrer(
            'bureau.update',
            'Bureau',
            $bureau->id,
            $ancienne,
            $bureau->fresh()->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Bureau mis à jour avec succès.',
            'bureau'  => $bureau->load('service.division'),
        ]);
    }

    /**
     * Supprimer un bureau avec vérification des dépendances
     */
    public function destroy(Bureau $bureau)
    {
        if ($bureau->personnel()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible : du personnel est rattaché à ce bureau.',
            ], 422);
        }

        AuditLog::enregistrer(
            'bureau.delete',
            'Bureau',
            $bureau->id,
            $bureau->toArray()
        );

        $bureau->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bureau supprimé.'
        ]);
    }

    /**
     * AJAX : Retourne les bureaux d'un service
     */
    public function parService(Service $service)
    {
        $bureaux = $service->bureaux()
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return response()->json($bureaux);
    }
}