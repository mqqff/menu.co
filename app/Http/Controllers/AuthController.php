<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['avatar'] = 'user_avatars/default.jpg';

        User::create($data);

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->with('error', 'Incorrect email or password');
    }

    public function profile(User $user): View
    {
        $user->load('preferences');

        $recipes = $this->getJson('recipes.json')->recipes ?? [];
        $created_recipes = [];

        foreach ($recipes as $recipe) {
            if ($recipe->author->id == $user->id) {
                $created_recipes[] = $recipe;
            }
        }

        $saved_recipes = [];

        if ($user->id === Auth::id()) {
            $saved_recipes = collect($recipes)->where('author.id', '!=', $user->id)->random(3)->all();
        }

        return view('auth.profile', compact('user', 'created_recipes', 'saved_recipes'));
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
