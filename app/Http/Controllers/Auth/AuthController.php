<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Module d'Authentification - Développé par SADOU MBALLO
     * Responsable du projet GeSchool
     */

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Redirection selon le rôle
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'professeur':
                    return redirect()->route('professeur.dashboard');
                case 'etudiant':
                    return redirect()->route('etudiant.dashboard');
                case 'parent':
                    return redirect()->route('parent.dashboard');
                default:
                    return redirect()->route('home');
            }
        }

        return back()->withErrors(['email' => 'Identifiants incorrects.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Déconnexion réussie');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,professeur,etudiant,parent',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'actif',
        ]);

        Auth::login($user);
        
        return redirect()->route('dashboard')->with('success', 'Inscription réussie');
    }
}