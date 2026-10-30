<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    /**
     * Afficher la page de changement de mot de passe obligatoire
     */
    public function show()
    {
        if (!auth()->user()->must_change_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.change-password');
    }

    /**
     * Traiter le nouveau mot de passe
     */
    public function update(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
        ]);

        // Vérifier que le nouveau MDP est différent de l'ancien
        if (Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors([
                'password' => 'Le nouveau mot de passe doit être différent de l\'ancien.',
            ]);
        }

        // Mettre à jour et désactiver le flag
        auth()->user()->update([
            'password'             => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')
                         ->with('success', 'Mot de passe mis à jour avec succès. Bienvenue !');
    }
}