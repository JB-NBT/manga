<?php

namespace Tests\Feature\Concerns;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait SetsUpRoles
{
    protected function setUpRoles(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'create manga', 'edit own manga', 'delete own manga',
            'request publication', 'create avis', 'edit own avis', 'delete own avis',
            'view public library', 'moderate avis', 'approve publications',
            'edit any manga', 'manage users', 'delete any manga',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        Role::firstOrCreate(['name' => 'user'])->syncPermissions([
            'create manga', 'edit own manga', 'delete own manga',
            'request publication', 'create avis', 'edit own avis', 'delete own avis',
        ]);

        Role::firstOrCreate(['name' => 'moderator'])->syncPermissions([
            'create manga', 'edit own manga', 'delete own manga',
            'request publication', 'create avis', 'edit own avis', 'delete own avis',
            'moderate avis', 'approve publications', 'edit any manga',
        ]);

        Role::firstOrCreate(['name' => 'admin'])->syncPermissions([
            'create manga', 'edit own manga', 'delete own manga',
            'request publication', 'create avis', 'edit own avis', 'delete own avis',
            'moderate avis', 'approve publications', 'manage users', 'delete any manga',
        ]);
    }

    protected function createUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        return $user;
    }

    protected function createModerator(): User
    {
        $mod = User::factory()->create();
        $mod->assignRole('moderator');
        return $mod;
    }

    protected function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        return $admin;
    }
}
