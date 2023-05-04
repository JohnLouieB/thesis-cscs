<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');

        $users = User::query()
            ->whereNotNull('name')
            ->get();
            
        $user = Auth::user();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'user' => $user
        ]);
    }

    public function getUsers()
    {
        $users = User::query()
            ->whereNotNull('name')
            ->get();

        return response()->json($users);
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');  
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'role' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('users.index');
    }

    public function getCurrentUser() 
    {
        $user = Auth::user();

        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$request->id,
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password)
        ]);

        return Redirect::route('users.index');
    }

    public function destroy(User $user)
    {
        abort_if(Auth::user()->role != 'admin', 404, 'Unauthorized');
        
        $user->delete();
        
        return Redirect::route('users.index');
    }
}
