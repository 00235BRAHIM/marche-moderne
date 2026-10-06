<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function users(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.super-admin.users', compact('users', 'search'));
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|in:customer,vendor,admin,super_admin',
        ]);

        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'role' => 'Vous ne pouvez pas modifier votre propre rôle.'
            ]);
        }

        if (
            $user->role === 'super_admin' &&
            $data['role'] !== 'super_admin' &&
            User::where('role', 'super_admin')->count() <= 1
        ) {
            return back()->withErrors([
                'role' => 'Impossible de retirer le dernier Super Admin.'
            ]);
        }

        $user->update([
            'role' => $data['role'],
        ]);

        return back()->with('success', 'Rôle de l’utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'user' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ]);
        }

        if (
            $user->role === 'super_admin' &&
            User::where('role', 'super_admin')->count() <= 1
        ) {
            return back()->withErrors([
                'user' => 'Impossible de supprimer le dernier Super Admin.'
            ]);
        }

        $user->delete();

        return back()->with('success', 'Utilisateur supprimé avec succès.');
    }
}
