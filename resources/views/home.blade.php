@extends('layouts.app')

@section('content')

<!-- HERO -->
<section class="bg-slate-950 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-20 grid md:grid-cols-2 gap-8 sm:gap-10 items-center">

        <div class="text-center md:text-left">
            <span class="text-indigo-300 font-bold text-sm sm:text-base">
                Bienvenue sur Marche Moderne
            </span>

            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold leading-tight mt-4">
                Achetez mieux.<br>
                <span class="text-indigo-400">Vivez mieux.</span>
            </h1>

            <p class="text-slate-300 mt-4 sm:mt-5 max-w-xl mx-auto md:mx-0 text-base sm:text-lg">
                Une marketplace moderne pour découvrir des produits et commander simplement.
            </p>

            <a
                href="{{ route('products') }}"
                class="inline-flex items-center justify-center w-full sm:w-auto mt-6 sm:mt-7 bg-white text-slate-950 px-6 py-3.5 rounded-xl font-bold hover:bg-slate-100 transition"
            >
                Explorer la boutique →
            </a>
        </div>

        <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-700 p-5 sm:p-10 text-center shadow-xl">
            <div class="text-6xl sm:text-8xl">
                🛍️
            </div>

            <b class="text-xl sm:text-2xl block mt-3">
                Tout ce qu'il vous faut.
            </b>
        </div>

    </div>
</section>


<!-- CATEGORIES + PRODUITS -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-14">

    <h2 class="text-2xl sm:text-3xl font-extrabold mb-6 sm:mb-7 text-center sm:text-left">
        Catégories
    </h2>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">

        @foreach($categories as $c)

            <a
                href="{{ route('products',['category'=>$c->id]) }}"
                class="bg-white border rounded-2xl p-4 sm:p-5 hover:shadow-lg transition text-center"
            >
                <div class="text-3xl mb-2">
                    📦
                </div>

                <b class="block text-sm sm:text-base">
                    {{ $c->name }}
                </b>

                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    {{ $c->products_count }} produits
                </p>
            </a>

        @endforeach

    </div>


    <!-- PRODUITS POPULAIRES -->

    <h2 class="text-2xl sm:text-3xl font-extrabold mt-12 sm:mt-14 mb-6 sm:mb-7 text-center sm:text-left">
        Produits populaires
    </h2>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-5">

        @foreach($featured as $p)

            <a
                href="{{ route('product.show',$p) }}"
                class="bg-white border rounded-2xl overflow-hidden hover:shadow-xl transition"
            >

                <div class="aspect-square bg-slate-100 overflow-hidden flex items-center justify-center">

                    @if($p->image)

                        <img
                            src="{{ asset('storage/'.$p->image) }}"
                            alt="{{ $p->name }}"
                            class="w-full h-full object-cover"
                        >

                    @else

                        <span class="text-5xl sm:text-6xl">
                            🛍️
                        </span>

                    @endif

                </div>

                <div class="p-3 sm:p-4">

                    <small class="text-slate-400 text-xs block truncate">
                        {{ $p->category->name }}
                    </small>

                    <h3 class="font-bold text-sm sm:text-base mt-1 line-clamp-2">
                        {{ $p->name }}
                    </h3>

                    <b class="text-sm sm:text-base block mt-1">
                        {{ number_format($p->price,0,',',' ') }} FCFA
                    </b>

                </div>

            </a>

        @endforeach

    </div>

</section>

@endsection
