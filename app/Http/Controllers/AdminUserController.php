<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->search);
        $roleId = $request->role_id;
        $status = $request->status ?? 'active';

        $users = User::withTrashed()
            ->with('role')
            ->when($status === 'active', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->when($status === 'deactivated', function ($query) {
                $query->onlyTrashed();
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleId, function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.adminuser', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'in:1,2,3,4'],
        ], [
            'email.unique' => 'This email is already used by another account.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $existingDeletedUser = User::onlyTrashed()
            ->where('email', $request->email)
            ->first();

        if ($existingDeletedUser) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This email belongs to a deactivated account. Please restore that account or use a different email.');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User added successfully');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'in:1,2,3,4'],
        ], [
            'email.unique' => 'This email is already used by another account.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $existingDeletedUser = User::onlyTrashed()
            ->where('email', $request->email)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingDeletedUser) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This email belongs to a deactivated account. Please restore that account or use a different email.');
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deactivated successfully');
    }
}