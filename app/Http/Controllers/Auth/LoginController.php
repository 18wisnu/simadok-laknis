<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Note: CheckUserStatus middleware will handle is_active check automatically
            return redirect()->intended('dashboard');
        }

        return back()->with('error', 'Email atau kata sandi yang Anda masukkan salah.');
    }
}
