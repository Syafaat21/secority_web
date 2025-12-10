<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        return view('dashboard');
    }

    public function users(){
        // Mengambil data pengguna dari database
        $users = \App\Models\User::all();
        return view('menu.user', compact('users'));
    }
}