<?php
namespace App\Http\Controllers;
use App\Models\{RessourceLogicielle,Ressource};
use Illuminate\Http\Request;

class LicenceController extends Controller
{
    public function index(Request $request)
    {
        $query = RessourceLogicielle::with('ressource')->orderBy('date_fin_licence');
        if ($request->filled('expiration')) {
            $query->whereNotNull('date_fin_licence')
                  ->where('date_fin_licence','<=',now()->addDays(30));
        }
        if ($request->filled('search')) {
            $query->whereHas('ressource',fn($q)=>$q->where('nom','like','%'.$request->search.'%')
                ->orWhere('numero_serie','like','%'.$request->search.'%'));
        }
        $licences = $query->paginate(15)->withQueryString();
        $ressourcesLogicielles = Ressource::logiciel()->doesntHave('ficheLogicielle')->get();
        return view('licences.index',compact('licences','ressourcesLogicielles'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'ressource_id'       => 'required|exists:ressources,id|unique:ressources_logicielles',
            'categorie'          => 'nullable|string',
            'type_licence'       => 'required|in:proprietaire,libre,freemium,abonnement',
            'nombre_licence'     => 'required|integer|min:1',
            'editeur'            => 'nullable|string',
            'version'            => 'nullable|string',
            'date_debut_licence' => 'nullable|date',
            'date_fin_licence'   => 'nullable|date|after:date_debut_licence',
        ]);
        RessourceLogicielle::create($v);
        return back()->with('success','Fiche licence créée.');
    }

    public function update(Request $request, RessourceLogicielle $licence)
    {
        $v = $request->validate([
            'nombre_licence'   => 'required|integer|min:1',
            'cles_utilisees'   => 'required|integer|min:0',
            'date_fin_licence' => 'nullable|date',
            'editeur'          => 'nullable|string',
            'version'          => 'nullable|string',
        ]);
        $licence->update($v);
        return back()->with('success','Licence mise à jour.');
    }

    public function allouer(RessourceLogicielle $licence)
    {
        if (!$licence->hasClesDisponibles()) {
            return back()->withErrors(['error'=>'Aucune clé disponible.']);
        }
        $licence->increment('cles_utilisees');
        return back()->with('success','Clé allouée. Restantes : '.$licence->fresh()->cles_restantes);
    }

    public function liberer(RessourceLogicielle $licence)
    {
        if ($licence->cles_utilisees > 0) {
            $licence->decrement('cles_utilisees');
        }
        return back()->with('success','Clé libérée.');
    }

    public function destroy(RessourceLogicielle $licence)
    {
        $licence->delete();
        return back()->with('success','Fiche licence supprimée.');
    }
}

