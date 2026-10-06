@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-8 sm:py-12 grid md:grid-cols-2 gap-8 lg:gap-10">

    {{-- IMAGE PRODUIT --}}
    <div class="w-full aspect-square bg-white border rounded-3xl overflow-hidden flex items-center justify-center">
        @if($product->image)
            <img
                src="{{ asset('storage/'.$product->image) }}"
                alt="{{ $product->name }}"
                class="w-full h-full object-cover"
            >
        @else
            <span class="text-8xl sm:text-9xl">🛍️</span>
        @endif
    </div>

    {{-- INFORMATIONS PRODUIT --}}
    <div class="flex flex-col justify-center text-center md:text-left">

        <p class="text-indigo-600 font-bold text-sm sm:text-base">
            {{ $product->category->name }}
        </p>

        <h1 class="text-3xl sm:text-4xl font-extrabold mt-2 leading-tight">
            {{ $product->name }}
        </h1>

        <p class="text-2xl sm:text-3xl font-extrabold mt-5">
            {{ number_format($product->price,0,',',' ') }} FCFA
        </p>

        <p class="text-slate-600 leading-7 mt-5">
            {{ $product->description }}
        </p>

        <p class="mt-5">
            Stock :
            <b>{{ $product->stock }}</b>
        </p>

        @auth

            @if($product->stock)

                <form
                    method="POST"
                    action="{{ route('cart.add',$product) }}"
                    class="flex flex-col sm:flex-row gap-3 mt-7 w-full"
                >
                    @csrf

                    <input
                        name="quantity"
                        type="number"
                        min="1"
                        max="{{ $product->stock }}"
                        value="1"
                        class="w-full sm:w-24 border rounded-xl px-3 py-3 text-center"
                    >

                    <button
                        class="w-full sm:flex-1 bg-indigo-600 text-white rounded-xl font-bold py-3 hover:bg-indigo-700 transition"
                    >
                        🛒 Ajouter au panier
                    </button>
                </form>

            @endif

        @else

            <a
                href="{{ route('login') }}"
                class="inline-flex items-center justify-center mt-7 w-full sm:w-auto bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 transition"
            >
                🔐 Connectez-vous pour acheter
            </a>

        @endauth

    </div>

</div>

@endsection
