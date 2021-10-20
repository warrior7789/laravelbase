<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminuserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
       

        $user = User::create([
           'name' => 'Admin',
           'email' => 'admin@admin.com',
           'password' => Hash::make('lloyd@321'),
           'role' => 'admin'
       ]);
       $user->assignRole('administrator');
    }
}
