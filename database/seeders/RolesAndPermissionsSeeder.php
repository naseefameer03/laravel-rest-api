<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create articles']);
        Permission::create(['name' => 'edit articles']);
        Permission::create(['name' => 'delete articles']);
        Permission::create(['name' => 'publish articles']);

        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']); // This is for soft-delete

        // create roles and assign created permissions

        // this can be done as separate statements
        $role = Role::create(['name' => 'subscriber']);
        // A subscriber can't do much

        $role = Role::create(['name' => 'author']);
        $role->givePermissionTo(['create articles', 'edit articles', 'delete articles']);

        $role = Role::create(['name' => 'editor']);
        $role->givePermissionTo(['create articles', 'edit articles', 'delete articles', 'publish articles']);

        // or may be done by chaining
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
    }
}
