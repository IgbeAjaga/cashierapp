<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        abort(403);
    }

    public function superAdmin()
    {
        return view('superadmin.dashboard');
    }

    public function admin()
    {
        return view('admin.dashboard');
    }
}

