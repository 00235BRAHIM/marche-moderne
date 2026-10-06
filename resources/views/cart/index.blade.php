@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto px-4 py-10">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <h1 class="text-3xl font-extrabold text-center sm:text-left">
            🛒 Mon panier
        </h1>

        <a
            href="{{ session('public_shop_slug') ? route('shop.public', session('public_shop_slug')) : route('products') }}"
            class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold hover:bg-slate-100 transition"
        >
            ← Retour à {{ session('public_shop_name') ?: 'la boutique' }}
        </a>

    </div>

    @php
        $total = 0;
    @endphp

    <div class="space-y-3 mt-7">

        @forelse($cart->items as $i)

            @php
                $line = $i->unit_price * $i->quantity;
                $total += $line;
            @endphp

            <div class="bg-white border rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center gap-4">

                {{-- IMAGE DU PRODUIT --}}
                <div class="w-16 h-16 bg-slate-100 rounded-xl overflow-hidden flex items-center justify-center flex-shrink-0">

                    @if($i->product && $i->product->image)

                        <img
                            src="{{ asset('storage/' . $i->product->image) }}"
                            alt="{{ $i->product->name }}"
                            class="w-full h-full object-cover"
                        >

                    @else

                        <span class="text-2xl">🛍️</span>

                    @endif

                </div>

                {{-- INFORMATIONS DU PRODUIT --}}
                <div class="flex-1 w-full sm:w-auto text-center sm:text-left">

                    <b>{{ $i->product->name }}</b>

                    <p>
                        {{ number_format($i->unit_price, 0, ',', ' ') }} FCFA
                    </p>

                </div>

                {{-- QUANTITÉ --}}
                <form
                    method="POST"
                    action="{{ route('cart.update', $i) }}"
                    class="flex justify-center sm:justify-start w-full sm:w-auto"
                >

                    @csrf
                    @method('PATCH')

                    <input
                        name="quantity"
                        type="number"
                        min="1"
                        value="{{ $i->quantity }}"
                        class="w-20 border rounded-l-xl p-2"
                    >

                    <button class="border rounded-r-xl px-3">
                        OK
                    </button>

                </form>

                {{-- SUPPRIMER --}}
                <form
                    method="POST"
                    action="{{ route('cart.remove', $i) }}"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        class="text-red-600 text-xl font-bold self-end sm:self-center"
                    >
                        ×
                    </button>

                </form>

            </div>

        @empty

            <div class="bg-white border rounded-2xl p-10 text-center">
                Panier vide.
            </div>

        @endforelse

    </div>

    @if($cart->items->count())

        <div class="mt-7 bg-white border rounded-2xl p-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-center">

            <b class="text-2xl">
                {{ number_format($total, 0, ',', ' ') }} FCFA
            </b>

            <a
                href="{{ route('checkout') }}"
                class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold w-full sm:w-auto"
            >
                Commander
            </a>

        </div>

    @endif

</div>

@endsection