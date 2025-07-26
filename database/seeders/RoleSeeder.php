<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Buat roles hanya untuk admin dan kasir
        $adminRole = Role::create(['name' => 'admin']);
        $kasirRole = Role::create(['name' => 'kasir']);

        // Buat admin default
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'gemilangcafesaung75@gmail.com',
            'password' => bcrypt('admin123'),
        ]);
        $admin->assignRole($adminRole);

        // Buat kasir default
        $kasir = User::create([
            'name' => 'Kasir',
            'email' => 'kasir@gmail.com',
            'password' => bcrypt('kasir123'),
        ]);
        $kasir->assignRole(roles: $kasirRole);
    }
}
