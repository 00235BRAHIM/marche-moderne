@extends('layouts.app') @section('content')<div class="max-w-7xl mx-auto px-4 py-10"><div class="flex flex-col items-center text-center gap-4 mb-6">
    <h1 class="text-3xl font-extrabold">Bonjour {{ auth()->user()->name }} 👋</h1>

    @if(session('public_shop_slug'))
        <a
            href="{{ route('shop.public', session('public_shop_slug')) }}"
            class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold hover:bg-slate-100 transition"
        >
            ← Retour à {{ session('public_shop_name') ?: 'la boutique' }}
        </a>
    @endif
</div><div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 mt-8 text-center"><a href="{{ route('products') }}" class="bg-indigo-600 text-white rounded-2xl p-6">Continuer mes achats</a><a href="{{ route('orders') }}" class="bg-white border rounded-2xl p-6">Mes commandes</a><a href="{{ route('cart') }}" class="bg-white border rounded-2xl p-6">Mon panier</a></div></div>@endsection
