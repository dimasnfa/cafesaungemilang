<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;


class AdminDashboardController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.dashboard', compact('users'));
    }
}
