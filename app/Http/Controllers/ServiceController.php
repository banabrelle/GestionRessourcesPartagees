<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Division;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['division', 'bureaux'])
                        ->withCount(['bureaux']);
                        

        // Filtre par division
        if ($request->filled('division')) {
            $query->where('id_division', $request->division);
        }


        // Recherche par nom ou code
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%");
            });
        }

        $services  = $query->orderBy('nom')->paginate(20)->withQueryString();
        $divisions = Division::orderBy('nom')->get();

        return redirect()->route('parametres.index', ['tab' => 'services']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_division' => 'required|exists:divisions,id',
            'code'        => 'required|string|max:20|unique:services,code',
            'nom'         => 'required|string|max:255',
            'description' => 'nullable|string',
            'responsable' => 'nullable|string|max:255',
        ]);

        $service = Service::create($validated);

        AuditLog::enregistrer(
            'service.create',
            'Service',
            $service->id,
            null,
            $service->toArray(),
            "Creation service: {$service->nom}"
        );

        return response()->json([
            'success' => true,
            'message' => "Service {$service->nom} cree avec succes.",
            'service' => $service->load('division'),
        ]);
    }

    /**
     * Modifier un service via MODALE - retourne JSON
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'id_division' => 'required|exists:divisions,id',
            'code'        => 'required|string|max:20|unique:services,code,' . $service->id,
            'nom'         => 'required|string|max:255',
            'description' => 'nullable|string',
            'responsable' => 'nullable|string|max:255',
        ]);

        $ancienne = $service->toArray();
        $service->update($validated);

        AuditLog::enregistrer(
            'service.update',
            'Service',
            $service->id,
            $ancienne,
            $service->fresh()->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Service mis a jour avec succes.',
            'service' => $service->load('division'),
        ]);
    }

    /**
     * Supprimer un service - avec verification des dependances
     */
    public function destroy(Service $service)
    {
        // Vérifier si le service a des bureaux
        if ($service->bureaux()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Impossible : ce service contient des bureaux. Supprimez-les d'abord.",
            ], 422);
        }

        // Vérifier si le service a du personnel
        if ($service->personnel()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible : du personnel est rattache a ce service.',
            ], 422);
        }

        AuditLog::enregistrer(
            'service.delete',
            'Service',
            $service->id,
            $service->toArray()
        );

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service supprime.',
        ]);
    }

    /**
     * AJAX : Retourne les services d'une division
     */
    public function parDivision(Division $division)
    {
        $services = $division->services()
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'nom']);

        return response()->json($services);
    }
}