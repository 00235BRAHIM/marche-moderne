@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="text-center mb-10">
        <p class="text-indigo-600 font-bold uppercase tracking-wide">
            🏪 Marche Moderne
        </p>

        <h1 class="text-4xl font-black text-slate-900 mt-2">
            Découvrez nos boutiques
        </h1>

        <p class="text-slate-500 mt-3 max-w-2xl mx-auto">
            Découvrez les boutiques de nos vendeurs et accédez directement
            à leurs produits.
        </p>
    </div>

    @if($shops->count())

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($shops as $shop)

                <article class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition">

                    <div class="p-6">

                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 text-center sm:text-left">

                            {{-- LOGO --}}
                            <div class="w-20 h-20 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">

                                @if($shop->shop_logo)
                                    <img
                                        src="{{ asset('storage/' . $shop->shop_logo) }}"
                                        alt="{{ $shop->shop_name ?: $shop->name }}"
                                        class="w-full h-full object-cover"
                                    >
                                @else
                                    <span class="text-3xl">🏪</span>
                                @endif

                            </div>

                            <div class="min-w-0 w-full">

                                <h2 class="text-xl font-black text-slate-900 truncate text-center sm:text-left">
                                    {{ $shop->shop_name ?: $shop->name }}
                                </h2>

                                {{-- CERTIFICATION --}}
                                @if($shop->isCertifiedVendor())

                                    <span class="inline-flex items-center gap-1 mt-2 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                        ✓ Certifié
                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1 mt-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">
                                        Boutique
                                    </span>

                                @endif

                            </div>

                        </div>

                        {{-- DESCRIPTION --}}
                        @if($shop->shop_description)

                            <p class="text-slate-600 mt-5 line-clamp-3 text-center sm:text-left">
                                {{ $shop->shop_description }}
                            </p>

                        @else

                            <p class="text-slate-400 mt-5 text-center sm:text-left">
                                Découvrez les produits de cette boutique.
                            </p>

                        @endif

                        {{-- INFORMATIONS --}}
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-6 pt-5 border-t border-slate-100 text-center sm:text-left">

                            <div>
                                <p class="text-sm text-slate-500">
                                    Produits disponibles
                                </p>

                                <p class="text-lg font-black text-slate-900">
                                    {{ $shop->products_count }}
                                </p>
                            </div>

                            @if($shop->isCertifiedVendor())
                                <div class="text-green-600 text-sm font-bold text-center">
                                    🛡️ Vendeur certifié
                                </div>
                            @endif

                        </div>

                        {{-- BOUTON --}}
                        <a
                            href="{{ route('shop.public', $shop->shop_slug) }}"
                            class="mt-6 w-full inline-flex items-center justify-center px-5 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition"
                        >
                            Voir la boutique →
                        </a>

                    </div>

                </article>

            @endforeach

        </div>

        <div class="mt-10">
            {{ $shops->links() }}
        </div>

    @else

        <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center">
            <div class="text-5xl">🏪</div>

            <h2 class="text-2xl font-black text-slate-900 mt-4">
                Aucune boutique disponible
            </h2>

            <p class="text-slate-500 mt-2">
                Les boutiques apparaîtront ici lorsqu'elles seront créées.
            </p>
        </div>

    @endif

</div>

@endsection
