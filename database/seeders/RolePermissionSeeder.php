<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Réinitialiser les caches de permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // CRÉATION DES PERMISSIONS
        // ========================================
        
        $permissions = [
            // Permissions mangas collection privée
            'view own mangas',
            'create manga',
            'edit own manga',
            'delete own manga',
            
            // Permissions bibliothèque publique
            'view public library',
            'request publication',
            
            // Permissions avis
            'create avis',
            'edit own avis',
            'delete own avis',
            
            // Permissions admin
            'manage users',
            'manage all mangas',
            'approve publications',
            'delete any avis',
            'view admin panel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ========================================
        // CRÉATION DES RÔLES
        // ========================================

        // 🔴 RÔLE ADMIN (accès total)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all()); // Toutes les permissions

        // 🟢 RÔLE USER (utilisateur enregistré)
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo([
            'view own mangas',
            'create manga',
            'edit own manga',
            'delete own manga',
            'view public library',
            'request publication',
            'create avis',
            'edit own avis',
            'delete own avis',
        ]);

        // 🔵 RÔLE VISITEUR (pas de compte, lecture seule)
        // Note : Le rôle "visiteur" n'est pas assigné aux users
        // C'est juste pour la logique métier (guest = visiteur)

        // ========================================
        // CRÉATION DES COMPTES DE TEST
        // ========================================

        // 🔴 Compte ADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@manga.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
            ]
        );
        $admin->assignRole('admin');

        // 🟢 Compte USER 1
        $user1 = User::firstOrCreate(
            ['email' => 'user@manga.local'],
            [
                'name' => 'User Test',
                'password' => Hash::make('password123'),
            ]
        );
        $user1->assignRole('user');

        // 🟢 Compte USER 2
        $user2 = User::firstOrCreate(
            ['email' => 'user2@manga.local'],
            [
                'name' => 'User Deux',
                'password' => Hash::make('password123'),
            ]
        );
        $user2->assignRole('user');

        // ========================================
        // MESSAGES DE CONFIRMATION
        // ========================================

        echo "\n✅ Permissions créées : " . count($permissions);
        echo "\n✅ Rôles créés : Admin, User";
        echo "\n\n📋 COMPTES DE TEST CRÉÉS :\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🔴 ADMIN:\n";
        echo "   Email    : admin@manga.local\n";
        echo "   Password : password123\n";
        echo "   Accès    : Total (gestion complète)\n\n";
        
        echo "🟢 USER 1:\n";
        echo "   Email    : user@manga.local\n";
        echo "   Password : password123\n";
        echo "   Accès    : Collection privée + demandes de publication\n\n";
        
        echo "🟢 USER 2:\n";
        echo "   Email    : user2@manga.local\n";
        echo "   Password : password123\n";
        echo "   Accès    : Collection privée + demandes de publication\n\n";
        
        echo "🔵 VISITEUR (non connecté):\n";
        echo "   Accès    : Bibliothèque publique en lecture seule\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
