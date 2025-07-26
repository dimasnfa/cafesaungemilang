<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get(); // Ambil semua user dengan role
        return view('admin.pages.user.index', compact('users'));
    }

    // Form tambah user
    public function create()
    {
        $roles = Role::all(); // Ambil semua role
        return view('admin.pages.user.create', compact('roles'));
    }

    // Menyimpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role); // Tetapkan role ke user

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan!');
    }

    // Menampilkan form edit user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all(); // Ambil semua role
        return view('admin.pages.user.edit', compact('user', 'roles'));
    }

    // Mengupdate data user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'nullable|string|max:100',
            'email' => 'nullable|string|email|max:100|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->nama ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        $user->syncRoles([$request->role]); // Update role user

        return redirect()->route('admin.user.index')->with('success', 'User berhasil diperbarui!');
    }

    // Menghapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus!');
    }
}
