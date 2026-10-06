@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-indigo-600">👑 Super Administration</p>
            <h1 class="text-3xl font-extrabold">Gestion des utilisateurs</h1>
            <p class="text-gray-500 mt-1">
                Gérer les comptes, les rôles et les accès à Marche Moderne 235.
            </p>
        </div>

        <a
            href="{{ route('admin.dashboard') }}"
            class="inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-800 px-5 py-3 rounded-xl font-semibold"
        >
            ← Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="mt-6 bg-green-50 border border-green-200 text-green-700 rounded-xl p-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mt-6 bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.super-admin.users') }}" class="mt-7 bg-white border rounded-2xl p-4">
        <div class="flex flex-col md:flex-row gap-3">
            <input
                type="text"
                name="q"
                value="{{ $search }}"
                placeholder="Rechercher par nom, email, téléphone ou rôle..."
                class="flex-1 border rounded-xl px-4 py-3"
            >

            <button
                type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold"
            >
                🔎 Rechercher
            </button>

            @if($search)
                <a
                    href="{{ route('admin.super-admin.users') }}"
                    class="bg-gray-100 hover:bg-gray-200 px-6 py-3 rounded-xl font-semibold text-center"
                >
                    Réinitialiser
                </a>
            @endif
        </div>
    </form>

    <div class="bg-white border rounded-2xl overflow-hidden mt-7">

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-5 py-4 text-sm font-bold">Utilisateur</th>
                        <th class="px-5 py-4 text-sm font-bold">Téléphone</th>
                        <th class="px-5 py-4 text-sm font-bold">Rôle</th>
                        <th class="px-5 py-4 text-sm font-bold">Modifier le rôle</th>
                        <th class="px-5 py-4 text-sm font-bold">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">

                            <td class="px-5 py-5">
                                <div class="font-bold">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>

                                @if($user->id === auth()->id())
                                    <span class="inline-block mt-1 text-xs font-bold text-indigo-600">
                                        👑 Votre compte
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-5 text-sm">
                                {{ $user->phone ?: '—' }}
                            </td>

                            <td class="px-5 py-5">
                                @php
                                    $roleLabels = [
                                        'customer' => 'Client',
                                        'vendor' => 'Vendeur',
                                        'admin' => 'Admin',
                                        'super_admin' => 'Super Admin',
                                    ];
                                @endphp

                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                                    {{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $user->role === 'vendor' ? 'bg-orange-100 text-orange-700' : '' }}
                                    {{ $user->role === 'customer' ? 'bg-green-100 text-green-700' : '' }}
                                ">
                                    {{ $roleLabels[$user->role] ?? $user->role }}
                                </span>
                            </td>

                            <td class="px-5 py-5">
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.super-admin.users.role', $user) }}" class="flex gap-2">
                                        @csrf
                                        @method('PUT')

                                        <select name="role" class="border rounded-lg px-3 py-2 text-sm">
                                            @foreach($roleLabels as $value => $label)
                                                <option value="{{ $value }}" @selected($user->role === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <button
                                            type="submit"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold"
                                        >
                                            Enregistrer
                                        </button>
                                    </form>
                                @else
                                    <span class="text-sm text-gray-400">
                                        Votre rôle est protégé
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-5">
                                @if($user->id !== auth()->id())
                                    <form
                                        method="POST"
                                        action="{{ route('admin.super-admin.users.destroy', $user) }}"
                                        onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ? Cette action est irréversible.');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-bold"
                                        >
                                            🗑️ Supprimer
                                        </button>
                                    </form>
                                @else
                                    <span class="text-sm text-gray-400">
                                        Protégé
                                    </span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-5 border-t">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
