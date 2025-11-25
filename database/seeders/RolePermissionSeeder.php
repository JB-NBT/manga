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
            
            // Permissions modérateur
            'edit any manga',           // Modifier n'importe quel manga
            'approve publications',      // Approuver les publications
            'republish expired manga',   // Republier les mangas expirés
            
            // Permissions admin
            'manage users',
            'delete any manga',          // Supprimer n'importe quel manga
            'delete any avis',
            'view admin panel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ========================================
        // CRÉATION DES RÔLES
        // ========================================

        // 🔴 RÔLE ADMIN (gestion complète)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view own mangas',
            'create manga',
            'edit own manga',
            'delete own manga',
            'view public library',
            'request publication',
            'create avis',
            'edit own avis',
            'delete own avis',
            'edit any manga',
            'approve publications',
            'republish expired manga',
            'manage users',
            'delete any manga',
            'delete any avis',
            'view admin panel',
        ]);

        // 🟡 RÔLE MODÉRATEUR (gestion contenu)
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $moderatorRole->givePermissionTo([
            'view own mangas',
            'create manga',
            'edit own manga',
            'delete own manga',
            'view public library',
            'request publication',
            'create avis',
            'edit own avis',
            'delete own avis',
            'edit any manga',           // ✅ Modifier n'importe quel manga
            'approve publications',      // ✅ Valider les publications
            'republish expired manga',   // ✅ Republier les mangas expirés
            'delete any avis',
        ]);

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
        $admin->syncRoles(['admin']);

        // 🟡 Compte MODÉRATEUR
        $moderator = User::firstOrCreate(
            ['email' => 'moderator@manga.local'],
            [
                'name' => 'Modérateur',
                'password' => Hash::make('password123'),
            ]
        );
        $moderator->syncRoles(['moderator']);

        // 🟢 Compte USER 1
        $user1 = User::firstOrCreate(
            ['email' => 'user@manga.local'],
            [
                'name' => 'User Test',
                'password' => Hash::make('password123'),
            ]
        );
        $user1->syncRoles(['user']);

        // 🟢 Compte USER 2
        $user2 = User::firstOrCreate(
            ['email' => 'user2@manga.local'],
            [
                'name' => 'User Deux',
                'password' => Hash::make('password123'),
            ]
        );
        $user2->syncRoles(['user']);

        // ========================================
        // MESSAGES DE CONFIRMATION
        // ========================================

        echo "\n✅ Permissions créées : " . count($permissions);
        echo "\n✅ Rôles créés : Admin, Modérateur, User";
        echo "\n\n📋 COMPTES DE TEST CRÉÉS :\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🔴 ADMIN:\n";
        echo "   Email    : admin@manga.local\n";
        echo "   Password : password123\n";
        echo "   Accès    : Gestion complète + Suppression mangas\n\n";
        
        echo "🟡 MODÉRATEUR:\n";
        echo "   Email    : moderator@manga.local\n";
        echo "   Password : password123\n";
        echo "   Accès    : Modification tous mangas + Validation publications + Republication\n\n";
        
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
