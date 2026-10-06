@extends('layouts.app') @section('content')<div class="max-w-7xl mx-auto px-4 py-10"><div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-center"><h1 class="text-3xl font-extrabold text-center md:text-left">Mes produits</h1><div class="flex items-center justify-center gap-3"><a href="{{ route('vendor.dashboard') }}" class="border border-slate-300 bg-white text-slate-700 px-5 py-3 rounded-xl font-semibold hover:bg-slate-50">← Dashboard</a><a href="{{ route('vendor.products.create') }}" class="bg-indigo-600 text-white px-5 py-3 rounded-xl font-semibold hover:bg-indigo-700">+ Ajouter</a></div></div><div class="bg-white border rounded-2xl mt-7 overflow-hidden">
    @forelse($products as $p)
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5 p-5 border-b last:border-b-0">

            <div class="flex items-center gap-4 min-w-0">

                <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-100 border flex items-center justify-center flex-shrink-0">
                    @if($p->image)
                        <img
                            src="{{ asset('storage/' . $p->image) }}"
                            alt="{{ $p->name }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <span class="text-4xl">🛍️</span>
                    @endif
                </div>

                <div class="min-w-0">
                    <b class="text-lg text-slate-900">{{ $p->name }}</b>
                    <p class="text-slate-600 mt-1">
                        {{ number_format($p->price,0,',',' ') }} FCFA · Stock {{ $p->stock }}
                    </p>
                </div>

            </div>

            <div class="flex justify-center gap-4 md:flex-shrink-0">
                <a
                    class="text-indigo-600 font-semibold hover:text-indigo-800"
                    href="{{ route('vendor.products.edit',$p) }}"
                >
                    Modifier
                </a>

                <form method="POST" action="{{ route('vendor.products.destroy',$p) }}">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 font-semibold hover:text-red-800">
                        Supprimer
                    </button>
                </form>
            </div>

        </div>
    @empty
        <div class="p-8 text-center">
            <div class="text-5xl mb-3">🛍️</div>
            <p class="text-slate-500">Aucun produit.</p>
        </div>
    @endforelse
</div></div>@endsection
