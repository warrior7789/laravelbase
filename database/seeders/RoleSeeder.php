<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $role = Role::create(['name' => 'administrator']);
        $role->givePermissionTo('users_manage');

        $role = Role::create(['name' => 'Super Admin']);
        $role->givePermissionTo('all');

        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo('front');
    }
}
