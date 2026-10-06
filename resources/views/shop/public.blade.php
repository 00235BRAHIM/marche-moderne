@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50">

    {{-- COUVERTURE DE LA BOUTIQUE --}}
    <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-purple-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            <div class="flex flex-col md:flex-row md:items-center gap-8 text-center md:text-left">

                {{-- LOGO --}}
                <div class="w-32 h-32 mx-auto md:mx-0 rounded-3xl bg-white shadow-xl overflow-hidden flex items-center justify-center border-4 border-white shrink-0">
                    @if($vendor->shop_logo)
                        <img
                            src="{{ asset('storage/' . $vendor->shop_logo) }}"
                            alt="{{ $vendor->shop_name }}"
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="text-5xl">🏪</div>
                    @endif
                </div>

                {{-- INFORMATIONS --}}
                <div class="text-white flex-1 text-center md:text-left">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">

                        <h1 class="text-3xl md:text-4xl font-black text-center md:text-left">
                            {{ $vendor->shop_name ?: $vendor->name }}
                        </h1>

                        @if($vendor->isCertifiedVendor())
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white text-indigo-700 text-sm font-bold">
                                ✓ Certifié
                            </span>
                        @endif

                    </div>

                    <p class="mt-2 text-indigo-100 text-lg text-center md:text-left">
                        {{ $vendor->name }}
                    </p>

                    @if($vendor->shop_description)
                        <p class="mt-4 max-w-2xl mx-auto md:mx-0 text-indigo-100 text-center md:text-left">
                            {{ $vendor->shop_description }}
                        </p>
                    @endif

                    @if($vendor->phone)
                        <p class="mt-4 text-white font-semibold text-center md:text-left">
                            📞 {{ $vendor->phone }}
                        </p>
                    @endif
                </div>

                {{-- PARTAGER --}}
                <div>
                    <button
                        type="button"
                        onclick="shareShop()"
                        class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-white text-indigo-700 font-bold hover:bg-indigo-50 transition shadow-lg"
                    >
                        📤 Partager la boutique
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- INFORMATIONS --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            {{-- SIDEBAR --}}
            <aside class="lg:col-span-1 space-y-6">

                {{-- PAIEMENT --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h2 class="font-black text-lg text-slate-900 text-center md:text-left">
                        💳 Paiement
                    </h2>

                    <div class="mt-4 space-y-3">

                        @if(isset($paymentMethods['airtel_money']))
                            <div class="rounded-xl bg-red-50 border border-red-100 p-4 text-center">
                                <p class="text-sm font-bold text-red-700">
                                    🔴 Airtel Money
                                </p>

                                <p class="mt-1 font-black text-slate-900">
                                    {{ $paymentMethods['airtel_money']->transfer_number }}
                                </p>

                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $paymentMethods['airtel_money']->account_name }}
                                </p>
                            </div>
                        @endif

                        @if(isset($paymentMethods['moov_money']))
                            <div class="rounded-xl bg-green-50 border border-green-100 p-4 text-center">
                                <p class="text-sm font-bold text-green-700">
                                    🟢 Moov Money
                                </p>

                                <p class="mt-1 font-black text-slate-900">
                                    {{ $paymentMethods['moov_money']->transfer_number }}
                                </p>

                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $paymentMethods['moov_money']->account_name }}
                                </p>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- RÉSEAUX --}}
                @if($vendor->facebook_url || $vendor->youtube_url || $vendor->whatsapp_url)
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                        <h2 class="font-black text-lg text-slate-900 text-center md:text-left">
                            🌐 Nous suivre
                        </h2>

                        <div class="mt-4 space-y-2">

                            @if($vendor->facebook_url)
                                <a
                                    href="{{ $vendor->facebook_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="block px-4 py-3 rounded-xl bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 text-center"
                                >
                                    📘 Facebook
                                </a>
                            @endif

                            @if($vendor->youtube_url)
                                <a
                                    href="{{ $vendor->youtube_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="block px-4 py-3 rounded-xl bg-red-50 text-red-700 font-bold hover:bg-red-100 text-center"
                                >
                                    ▶️ YouTube
                                </a>
                            @endif

                            @if($vendor->whatsapp_url)
                                <a
                                    href="{{ $vendor->whatsapp_url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="block px-4 py-3 rounded-xl bg-green-50 text-green-700 font-bold hover:bg-green-100 text-center"
                                >
                                    💬 WhatsApp
                                </a>
                            @endif

                        </div>
                    </div>
                @endif

            </aside>

            {{-- PRODUITS --}}
            <main class="lg:col-span-3">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 text-center sm:text-left">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 text-center sm:text-left">
                            Produits de {{ $vendor->shop_name ?: $vendor->name }}
                        </h2>

                        <p class="text-slate-500 mt-1 text-center sm:text-left">
                            {{ $products->total() }} produit(s) disponible(s)
                        </p>
                    </div>
                </div>

                @if($products->count())

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

                        @foreach($products as $product)

                            <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition">

                                <a href="{{ route('product.show', $product) }}">
                                    <div class="aspect-square bg-slate-100 overflow-hidden">

                                        @if($product->image)
                                            <img
                                                src="{{ asset('storage/' . $product->image) }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover hover:scale-105 transition duration-300"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-5xl">
                                                🛍️
                                            </div>
                                        @endif

                                    </div>
                                </a>

                                <div class="p-4">

                                    @if($product->category)
                                        <p class="text-xs font-bold text-indigo-600 uppercase">
                                            {{ $product->category->name }}
                                        </p>
                                    @endif

                                    <h3 class="mt-1 font-black text-slate-900 line-clamp-2">
                                        {{ $product->name }}
                                    </h3>

                                    <p class="mt-3 text-xl font-black text-indigo-600">
                                        {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                    </p>

                                    @if($product->stock > 0)
                                        <p class="mt-1 text-xs text-green-600 font-semibold">
                                            ✓ Disponible
                                        </p>
                                    @else
                                        <p class="mt-1 text-xs text-red-600 font-semibold">
                                            Rupture de stock
                                        </p>
                                    @endif

                                    <a
                                        href="{{ route('product.show', $product) }}"
                                        class="mt-4 block text-center px-4 py-3 rounded-xl bg-white border border-indigo-200 text-indigo-700 font-bold hover:bg-indigo-50"
                                    >
                                        Voir le produit
                                    </a>

                                    @auth
                                        <form
                                            method="POST"
                                            action="{{ route('cart.add', $product) }}"
                                            class="mt-2"
                                        >
                                            @csrf

                                            <input
                                                type="hidden"
                                                name="quantity"
                                                value="1"
                                            >

                                            <button
                                                type="submit"
                                                class="w-full px-4 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition"
                                            >
                                                🛒 Ajouter au panier
                                            </button>
                                        </form>
                                    @else
                                        <a
                                            href="{{ route('login', ['shop' => $vendor->shop_slug, 'buy' => $product->id]) }}"
                                            class="mt-2 block text-center px-4 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition"
                                        >
                                            🔐 Se connecter pour commander
                                        </a>
                                    @endauth

                                </div>
                            </article>

                        @endforeach

                    </div>

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>

                @else

                    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                        <div class="text-6xl">🛍️</div>

                        <h3 class="mt-4 text-xl font-black text-slate-900">
                            Aucun produit disponible
                        </h3>

                        <p class="mt-2 text-slate-500">
                            Cette boutique n'a pas encore publié de produit.
                        </p>
                    </div>

                @endif

            </main>
        </div>
    </div>
</div>

<script>
async function shareShop() {
    const url = window.location.href;
    const title = @json($vendor->shop_name ?: $vendor->name);

    if (navigator.share) {
        await navigator.share({
            title: title,
            text: 'Découvrez cette boutique sur Marche Moderne.',
            url: url
        });
    } else {
        await navigator.clipboard.writeText(url);
        alert('Lien de la boutique copié.');
    }
}
</script>
@endsection
