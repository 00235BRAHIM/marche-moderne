@extends('layouts.app') @section('content')<div class="min-h-[70vh] flex items-center justify-center px-4"><form method="POST" action="{{ route('login') }}" class="bg-white border rounded-3xl p-8 w-full max-w-md">@csrf
@if(request('shop'))
    <input type="hidden" name="shop" value="{{ request('shop') }}">
@endif
@if(request('buy'))
    <input type="hidden" name="buy" value="{{ request('buy') }}">
@endif<h1 class="text-3xl font-extrabold text-center">Connexion</h1><input name="email" type="email" required placeholder="Email" class="w-full mt-7 border rounded-xl p-3"><input name="password" type="password" required placeholder="Mot de passe" class="w-full mt-3 border rounded-xl p-3"><p class="text-left mt-2"><a class="text-sm text-indigo-600 hover:underline" href="{{ route("password.request") }}">Mot de passe oublié ?</a></p><button class="w-full mt-6 bg-indigo-600 text-white rounded-xl py-3 font-bold">Se connecter</button><p class="text-center mt-5 text-sm">Pas de compte ? <a class="text-indigo-600" href="{{ route('register') }}">Créer un compte</a></p></form></div>@endsection
