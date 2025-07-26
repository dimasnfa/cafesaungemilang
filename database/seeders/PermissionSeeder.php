<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus role "user" karena pelanggan tidak membutuhkan login
        Role::where('name', 'user')->delete();

        // Membuat roles admin & kasir jika belum ada
        $role_admin = Role::firstOrCreate(['name' => 'admin']);
        $role_kasir = Role::firstOrCreate(['name' => 'kasir']);

        // Daftar permissions umum
        $permissions = [
            'admin/dashboard',
            'admin/kategori',
            'admin/menu',
            'admin/menu/create',
            'admin/menu/store',
            'admin/menu/edit',
            'admin/menu/update',
            'admin/menu/delete',
            'admin/meja',
            'admin/pesanan',
            'admin/detailpesanan',
            'admin/booking',
            'admin/laporan',
            'admin/pembayaran',
            'admin/user',
        ];

        // Simpan semua permissions ke database
        foreach ($permissions as $permission_name) {
            Permission::firstOrCreate(['name' => $permission_name]);
        }

        // Permissions khusus kasir
        $permissions_kasir = [
            'admin/dashboard',
            'admin/menu',
            'admin/pesanan',
            'admin/detailpesanan',
            'admin/booking',
            'admin/laporan',
            'admin/pembayaran',
        ];

        // Beri permissions ke role masing-masing
        $role_admin->syncPermissions(Permission::pluck('name')); // Admin mendapat semua permissions
        $role_kasir->syncPermissions($permissions_kasir); // Kasir hanya yang diperlukan

        // Menambahkan role ke user tertentu
        $user_admin = User::find(1);
        if ($user_admin) {
            $user_admin->assignRole('admin');
            $this->command->info("✅ Role 'admin' diberikan ke User ID: 1");
        }

        $user_kasir = User::find(2);
        if ($user_kasir) {
            $user_kasir->assignRole('kasir');
            $this->command->info("✅ Role 'kasir' diberikan ke User ID: 2");
        }

        $this->command->info("✅ Seeder Permission berhasil dijalankan!");
    }
}
