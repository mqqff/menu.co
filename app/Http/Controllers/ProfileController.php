<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        $user->load('preferences');

        $created_recipes = $user->recipes()
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($recipe) {
                $recipe->cook_time = $this->formatCookTime($recipe->cook_time);
                return $recipe;
            });

        $saved_recipes = collect();

        if ($user->id === Auth::id()) {
            $saved_recipes = $user->bookmarks()
                ->latest()
                ->take(15)
                ->get()
                ->map(function ($bookmark) {
                    $bookmark->recipe->cook_time = $this->formatCookTime($bookmark->recipe->cook_time);
                    return $bookmark->recipe;
                });
        }

        return view('profile.show', compact('user', 'created_recipes', 'saved_recipes'));
    }

    public function edit(): View
    {
        $user = Auth::user();
        $user->load('preferences');
        $categories = Category::all();

        return view('profile.edit', compact('user', 'categories'));
    }

    public function updateProfile(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255',
            'preferences' => 'nullable|array',
            'preferences.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->withFragment('profile');
        }

        $user->update([
            'username' => $request->username,
            'name' => $request->name,
        ]);

        $user->preferences()->sync($request->preferences ?? []);

        return redirect()
            ->route('profile.settings')
            ->withFragment('profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function updateAccount(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'old_password' => 'required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'required_if:password,filled|same:password',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->withFragment('account');
        }

        $user->email = $request->email;

        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withFragment('account')->withErrors(['old_password' => 'Incorrect current password.']);
            }

            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile.settings')->withFragment('account')->with('success', 'Account updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        $user->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function createdRecipes(User $user): View
    {
        $recipes = $user->recipes()
            ->latest()
            ->paginate(75);

        $recipes->getCollection()->transform(function ($recipe) {
            $recipe->cook_time = $this->formatCookTime($recipe->cook_time);
            return $recipe;
        });

        return view('profile.created_recipes', compact('user', 'recipes'));
    }

    public function bookmarks(): View
    {
        $recipes = Auth::user()->bookmarks()
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($bookmark) {
                $bookmark->recipe->cook_time = $this->formatCookTime($bookmark->recipe->cook_time);
                return $bookmark->recipe;
            });

        return view('profile.bookmarks', compact('recipes'));
    }
}
