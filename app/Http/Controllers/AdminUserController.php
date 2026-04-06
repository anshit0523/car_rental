<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(15);
        return view('admin.adminuser', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|in:1,2,3',
        ]);

        $existingUser = User::withTrashed()
            ->where('email', $request->email)
            ->first();

        if ($existingUser) {
            if ($existingUser->trashed()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This email belongs to a deleted account. Please restore that account or use a different email.');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'This email is already used by an active account.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User added successfully');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role_id' => 'required|in:1,2,3',
        ]);

        $existingUser = User::withTrashed()
            ->where('email', $request->email)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingUser) {
            if ($existingUser->trashed()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This email belongs to a deleted account. Please restore that account or use a different email.');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'This email is already used by an active account.');
        }

        $user->update($request->only('name', 'email', 'role_id'));

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
}