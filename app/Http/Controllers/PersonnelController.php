<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\User;
use App\Models\Division;
use App\Models\Service;
use App\Models\Bureau;
use App\Models\TypePersonnel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;      // si tu utilises DB::
use Illuminate\Support\Facades\Storage;

class PersonnelController extends Controller
{
        public function index(Request $request)
    {
        $query = Personnel::with(['user','division','service','bureau','typePersonnel'])->orderBy('nom');
        if ($request->filled('division_id')) $query->where('division_id',$request->division_id);
        if ($request->filled('search')) {
            $query->where(fn($q)=>
                $q->where('nom','like','%'.$request->search.'%')
                  ->orWhere('prenom','like','%'.$request->search.'%')
                  ->orWhereHas('user',fn($u)=>$u->where('matricule','like','%'.$request->search.'%'))
            );
        }
        $personnels   = $query->paginate(20)->withQueryString();
        $divisions    = Division::orderBy('nom')->get();
        $services     = Service::orderBy('nom')->get();
        $bureaux      = Bureau::orderBy('nom')->get();
        $types        = TypePersonnel::all();
        return view('personnels.index',compact('personnels','divisions','services','bureaux','types'));
    }

    public function show(Personnel $personnel)
    {
        $personnel->load(['user','division','service','bureau','typePersonnel','reservations.ressource']);
        $divisions = Division::all();
        return view('personnels.show',compact('personnel','divisions'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users',
            'matricule'         => 'required|string|max:50|unique:users',
            'password'          => 'required|string|min:8',
            'role'              => 'required|in:personnel,chef_division,administrateur',
            'type_personnel_id' => 'nullable|exists:type_personnels,id',
            'division_id'       => 'nullable|exists:divisions,id',
            'service_id'        => 'nullable|exists:services,id',
            'bureau_id'         => 'nullable|exists:bureaus,id',
            'nom'               => 'required|string|max:150',
            'prenom'            => 'required|string|max:150',
            
        ]);

        $user = User::create([
            'name'      => $v['name'],
            'email'     => $v['email'],
            'matricule' => strtoupper($v['matricule']),
            'password'  => Hash::make($v['password']),
            'must_change_password' => true,
        ]);
        $user->assignRole($v['role']);

        Personnel::create([
            'id_user'          => $user->id,
            'type_personnel_id'=> $v['type_personnel_id'] ?? null,
            'division_id'      => $v['division_id'] ?? null,
            'service_id'       => $v['service_id'] ?? null,
            'bureau_id'        => $v['bureau_id'] ?? null,
            'nom'              => strtoupper($v['nom']),
            'prenom'           => $v['prenom'],
            
        ]);

        return back()->with('success','Personnel créé avec succès.');
    }

    public function update(Request $request, Personnel $personnel)
    {
        $v = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,'.$personnel->id_user,
            'matricule'    => 'required|string|unique:users,matricule,'.$personnel->id_user,
            'role'         => 'required|in:personnel,chef_division,administrateur',
            'division_id'  => 'nullable|exists:divisions,id',
            'service_id'   => 'nullable|exists:services,id',
            'bureau_id'    => 'nullable|exists:bureaus,id',
            'nom'          => 'required|string|max:150',
            'prenom'       => 'required|string|max:150',
           
        ]);

        $personnel->user->update(['name'=>$v['name'],'email'=>$v['email'],'matricule'=>strtoupper($v['matricule'])]);
        $personnel->user->syncRoles([$v['role']]);
        $personnel->update([
            'division_id'=>$v['division_id']??null,'service_id'=>$v['service_id']??null,
            'bureau_id'=>$v['bureau_id']??null,'nom'=>strtoupper($v['nom']),
            'prenom'=>$v['prenom'],
        ]);
        return back()->with('success','Personnel mis à jour.');
    }

    public function toggleStatut(Personnel $personnel)
    {
        $personnel->user->update(['is_active'=>!$personnel->user->is_active]);
        $msg = $personnel->user->is_active ? 'Compte activé.' : 'Compte désactivé.';
        return back()->with('success',$msg);
    }

    public function destroy(Personnel $personnel)
    {
        $personnel->user->delete();
        return redirect()->route('personnels.index')->with('success','Personnel supprimé.');
    }

}
