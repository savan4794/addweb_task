<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'Admin']);
        $author = Role::create(['name' => 'Author']);

        Permission::create(['name' => 'manage posts']);
        Permission::create(['name' => 'manage users']);

        $admin->givePermissionTo(['manage posts', 'manage users']);
        $author->givePermissionTo('manage posts');
    }
}
