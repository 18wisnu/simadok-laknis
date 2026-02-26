<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Only superadmin can manage users
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:staff,admin,superadmin',
            'whatsapp_notifications' => 'boolean',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'whatsapp_notifications' => $request->boolean('whatsapp_notifications'),
            'is_active' => true, // Manually created users are active by default
        ]);

        return redirect()->back()->with('success', "User {$validated['name']} berhasil dibuat.");
    }

    public function toggleStatus(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        // Superadmin cannot deactivate themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "User {$user->name} berhasil {$status}.");
    }

    public function updateRole(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'role' => 'required|in:staff,admin,superadmin',
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', "Role {$user->name} berhasil diperbarui.");
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'whatsapp_notifications' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'whatsapp_notifications' => $request->boolean('whatsapp_notifications'),
        ]);

        return redirect()->back()->with('success', "Data user {$user->name} berhasil diperbarui.");
    }

    public function resetPassword(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return redirect()->back()->with('success', "Kata sandi untuk {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        // Check if user is trying to delete themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "User {$name} berhasil dihapus.");
    }
}
