<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role, Permission};
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Toujours vider le cache Spatie avant de modifier les permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Définir TOUTES les permissions ──────────────────
        $permissions = [
            // Ressources
            'voir ressources',
            'creer ressources',
            'modifier ressources',
            'supprimer ressources',
            'gerer ressources',

            // Réservations
            'voir reservations',
            'creer reservations',
            'annuler reservations',
            'valider chef reservations',
            'valider admin reservations',
            'rejeter reservations',

            // Personnel / Utilisateurs
            'voir personnel',
            'creer personnel',
            'modifier personnel',
            'supprimer personnel',

            // Organisation
            'voir divisions',
            'gerer divisions',

            // Statistiques
            'voir statistiques',
            'voir statistiques division',

            // Exports
            'exporter rapports',

            // Licences
            'gerer licences',

            // Tags
            'gerer tags',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web'
            ]);
        }

        // ── Rôle: personnel ─────────────────────────────────
        $rPersonnel = Role::firstOrCreate([
            'name' => 'personnel',
            'guard_name' => 'web'
        ]);

        $rPersonnel->syncPermissions([
            'voir ressources',
            'creer reservations',
            'annuler reservations',
            'voir reservations',
        ]);

        // ── Rôle: chef_division ──────────────────────────────
        $rChef = Role::firstOrCreate([
            'name' => 'chef_division',
            'guard_name' => 'web'
        ]);

        $rChef->syncPermissions([
            'voir ressources',
            'voir reservations',
            'creer reservations',
            'annuler reservations',
            'valider chef reservations',
            'rejeter reservations',
            'voir statistiques division',
            'voir personnel',
        ]);

        // ── Rôle: administrateur ─────────────────────────────
        $rAdmin = Role::firstOrCreate([
            'name' => 'administrateur',
            'guard_name' => 'web'
        ]);

        $rAdmin->syncPermissions(
            Permission::where('guard_name','web')->get()
        );
    }
}