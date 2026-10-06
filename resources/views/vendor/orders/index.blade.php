@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 text-center">

        <div class="w-full md:w-auto">
            <p class="text-indigo-600 font-bold uppercase text-sm">
                Espace vendeur
            </p>

            <h1 class="text-3xl font-extrabold text-gray-900 text-center">
                Mes commandes
            </h1>

            <p class="text-gray-600 mt-2 text-center">
                Consultez les commandes de vos clients et vérifiez les preuves de transfert.
            </p>
        </div>

        <a href="{{ route('vendor.dashboard') }}"
           class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 w-full md:w-auto">
            ← Retour au tableau de bord
        </a>

    </div>


    <form method="GET" action="{{ route('vendor.orders.index') }}" class="mb-8">
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg">
                        🔎
                    </span>

                    <input
                        type="text"
                        name="q"
                        value="{{ $search ?? request('q') }}"
                        placeholder="Rechercher une commande, un client ou un produit..."
                        class="w-full border border-gray-200 rounded-xl pl-11 pr-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>

                <button
                    type="submit"
                    class="px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition">
                    Rechercher
                </button>

                @if(!empty($search))
                    <a
                        href="{{ route('vendor.orders.index') }}"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-xl border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 transition">
                        Réinitialiser
                    </a>
                @endif
            </div>

            @if(!empty($search))
                <p class="text-sm text-gray-500 mt-3">
                    Résultats pour :
                    <strong class="text-gray-900">"{{ $search }}"</strong>
                </p>
            @endif
        </div>
    </form>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-800 px-5 py-4">
            {{ session('success') }}
        </div>
    @endif


    @if($orders->count() === 0)

        <div class="bg-white border border-gray-200 rounded-2xl p-10 text-center shadow-sm">

            <div class="text-5xl mb-4">
                📦
            </div>

            <h2 class="text-xl font-bold text-gray-900">
                Aucune commande
            </h2>

            <p class="text-gray-500 mt-2">
                Vous n'avez pas encore reçu de commande.
            </p>

        </div>

    @else

        <div class="space-y-5">

            @foreach($orders as $vendorOrder)

                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

                    <div class="p-5 border-b border-gray-100">

                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                            <div>

                                <p class="text-sm text-gray-500">
                                    Commande vendeur
                                </p>

                                <h2 class="text-xl font-bold text-gray-900">
                                    {{ $vendorOrder->order_number }}
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Client :
                                    <strong class="text-gray-800">
                                        {{ $vendorOrder->order->user->name ?? 'Client' }}
                                    </strong>
                                </p>

                            </div>


                            <div class="flex flex-wrap items-center gap-3">

                                @if($vendorOrder->payment_status === 'paid')

                                    <span class="px-3 py-2 rounded-full bg-green-100 text-green-700 text-sm font-bold">
                                        ✓ Paiement accepté
                                    </span>

                                @else

                                    <span class="px-3 py-2 rounded-full bg-yellow-100 text-yellow-700 text-sm font-bold">
                                        ⏳ Paiement en attente
                                    </span>

                                @endif


                                <a href="{{ route('vendor.orders.show', $vendorOrder) }}"
                                   class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                                    Voir la commande
                                </a>

                            </div>

                        </div>

                    </div>


                    <div class="p-5">

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                            <div class="bg-gray-50 rounded-xl p-4">

                                <p class="text-xs text-gray-500 uppercase">
                                    Produits
                                </p>

                                <p class="text-lg font-bold text-gray-900 mt-1">
                                    {{ $vendorOrder->items->sum('quantity') }}
                                </p>

                            </div>


                            <div class="bg-gray-50 rounded-xl p-4">

                                <p class="text-xs text-gray-500 uppercase">
                                    Total
                                </p>

                                <p class="text-lg font-bold text-gray-900 mt-1">
                                    {{ number_format($vendorOrder->total, 0, ',', ' ') }} FCFA
                                </p>

                            </div>


                            <div class="bg-gray-50 rounded-xl p-4">

                                <p class="text-xs text-gray-500 uppercase">
                                    Statut
                                </p>

                                <p class="text-lg font-bold text-gray-900 mt-1">
                                    {{ ucfirst($vendorOrder->status) }}
                                </p>

                            </div>


                            <div class="bg-gray-50 rounded-xl p-4">

                                <p class="text-xs text-gray-500 uppercase">
                                    Date
                                </p>

                                <p class="text-lg font-bold text-gray-900 mt-1">
                                    {{ $vendorOrder->created_at->format('d/m/Y') }}
                                </p>

                            </div>

                        </div>


                        <div class="mt-5">

                            <p class="font-bold text-gray-900 mb-3">
                                Produits commandés
                            </p>

                            <div class="space-y-2">

                                @foreach($vendorOrder->items as $item)

                                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">

                                        <div>

                                            <span class="font-medium text-gray-800">
                                                {{ $item->product_name }}
                                            </span>

                                            <span class="text-gray-500">
                                                × {{ $item->quantity }}
                                            </span>

                                        </div>

                                        <span class="font-semibold text-gray-900">
                                            {{ number_format($item->total, 0, ',', ' ') }} FCFA
                                        </span>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        <div class="mt-8">
            {{ $orders->links() }}
        </div>

    @endif

</div>

@endsection
