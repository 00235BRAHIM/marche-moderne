@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <form method="POST" action="{{ route('password.update') }}" class="bg-white border rounded-3xl p-8 w-full max-w-md shadow-sm">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="text-center">
            <div class="text-5xl mb-4">🔑</div>
            <h1 class="text-3xl font-extrabold">Nouveau mot de passe</h1>
            <p class="text-gray-500 mt-2">
                Choisissez un nouveau mot de passe sécurisé.
            </p>
        </div>

        @if ($errors->any())
            <div class="mt-5 bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <label class="block mt-6 text-sm font-semibold">Adresse e-mail</label>

        <input
            name="email"
            type="email"
            value="{{ old('email', $email) }}"
            required
            class="w-full mt-2 border rounded-xl p-3 focus:ring-2 focus:ring-indigo-500"
        >

        <label class="block mt-4 text-sm font-semibold">Nouveau mot de passe</label>

        <input
            name="password"
            type="password"
            required
            minlength="8"
            placeholder="Minimum 8 caractères"
            class="w-full mt-2 border rounded-xl p-3 focus:ring-2 focus:ring-indigo-500"
        >

        <label class="block mt-4 text-sm font-semibold">Confirmer le mot de passe</label>

        <input
            name="password_confirmation"
            type="password"
            required
            minlength="8"
            placeholder="Confirmez votre mot de passe"
            class="w-full mt-2 border rounded-xl p-3 focus:ring-2 focus:ring-indigo-500"
        >

        <button
            type="submit"
            class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl py-3 font-bold"
        >
            Réinitialiser le mot de passe
        </button>

        <p class="text-center mt-5 text-sm">
            <a class="text-indigo-600 hover:underline" href="{{ route('login') }}">
                ← Retour à la connexion
            </a>
        </p>
    </form>
</div>
@endsection
