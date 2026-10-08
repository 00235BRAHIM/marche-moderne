@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto px-4 py-10">

    <div class="mb-8 text-center">
        <p class="text-indigo-600 font-bold text-center">
            Espace client
        </p>

        <h1 class="text-3xl font-extrabold text-slate-900 text-center">
            Mes commandes
        </h1>

        <p class="text-slate-500 mt-2 text-center">
            Consultez vos commandes, paiements et leur état d'avancement.
        </p>
    </div>

    <div class="space-y-4">

        @forelse($orders as $o)

            <a
                href="{{ route('order.show', $o) }}"
                class="block bg-white border rounded-2xl p-5 hover:shadow-lg transition"
            >

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    {{-- INFORMATIONS COMMANDE --}}
                    <div>

                        <div class="flex items-center gap-3 flex-wrap">

                            <b class="text-lg text-slate-900">
                                {{ $o->order_number }}
                            </b>

                            {{-- STATUT PAIEMENT --}}
                            @if($o->payment_status === 'paid')

                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">
                                    ✓ PAYÉ
                                </span>

                            @elseif($o->payment_status === 'pending')

                                <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">
                                    ⏳ PAIEMENT EN ATTENTE
                                </span>

                            @else

                                <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">
                                    ✕ NON PAYÉ
                                </span>

                            @endif

                        </div>

                        <p class="text-sm text-slate-500 mt-1">
                            {{ $o->created_at->format('d/m/Y H:i') }}
                        </p>

                        {{-- VENDEURS DE LA COMMANDE --}}
                        <div class="mt-5 space-y-3">

                            @foreach($o->vendorOrders as $vendorOrder)

                                @php
                                    $vendor = $vendorOrder->vendor;

                                    $airtel = $vendor?->paymentMethods
                                        ->where('provider', 'airtel_money')
                                        ->where('is_active', true)
                                        ->first();

                                    $moov = $vendor?->paymentMethods
                                        ->where('provider', 'moov_money')
                                        ->where('is_active', true)
                                        ->first();
                                @endphp

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                                    {{-- VENDEUR --}}
                                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                                        <div class="flex items-start gap-3">

                                            <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-xl">
                                                🏪
                                            </div>

                                            <div>

                                                <p class="font-extrabold text-slate-900">
                                                    Boutique de {{ $vendor?->name ?? 'Vendeur' }}
                                                </p>

                                                <p class="text-sm text-slate-600 mt-1">
                                                    👤 {{ $vendor?->name ?? 'Vendeur' }}
                                                </p>

                                                @if($vendor?->isCertifiedVendor())
                                                    <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                                        ✓ Vendeur certifié
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-1 rounded-full bg-slate-200 text-slate-600 text-xs font-semibold">
                                                        Vendeur non certifié
                                                    </span>
                                                @endif

                                            </div>

                                        </div>

                                        <div class="text-left md:text-right">

                                            <p class="text-xs text-slate-500">
                                                Total vendeur
                                            </p>

                                            <p class="font-extrabold text-slate-900">
                                                {{ number_format($vendorOrder->total, 0, ',', ' ') }} FCFA
                                            </p>

                                        </div>

                                    </div>

                                    {{-- CONTACT --}}
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-2">

                                        @if($vendor?->phone)
                                            <div class="bg-white border border-slate-200 rounded-xl px-3 py-2">
                                                <p class="text-[11px] text-slate-400 font-semibold">
                                                    Téléphone vendeur
                                                </p>
                                                <p class="text-sm font-bold text-slate-700">
                                                    📞 {{ $vendor->phone }}
                                                </p>
                                            </div>
                                        @endif

                                        @if($airtel)
                                            <div class="bg-white border border-slate-200 rounded-xl px-3 py-2">
                                                <p class="text-[11px] text-slate-400 font-semibold">
                                                    Airtel Money
                                                </p>
                                                <p class="text-sm font-bold text-red-600">
                                                    📱 {{ $airtel->transfer_number }}
                                                </p>
                                            </div>
                                        @endif

                                        @if($moov)
                                            <div class="bg-white border border-slate-200 rounded-xl px-3 py-2">
                                                <p class="text-[11px] text-slate-400 font-semibold">
                                                    Moov Money
                                                </p>
                                                <p class="text-sm font-bold text-blue-600">
                                                    📱 {{ $moov->transfer_number }}
                                                </p>
                                            </div>
                                        @endif

                                    </div>

                                    {{-- PRODUITS DU VENDEUR --}}
                                    @if($vendorOrder->items->count())
                                        <div class="mt-4">

                                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">
                                                Produits de cette boutique
                                            </p>

                                            <div class="space-y-2">

                                                @foreach($vendorOrder->items as $item)

                                                    <div class="flex items-center justify-between gap-3 bg-white border border-slate-200 rounded-xl p-3">

                                                        <div class="flex items-center gap-3 min-w-0">

                                                            @if($item->product?->image)
                                                                <img
                                                                    src="{{ asset('storage/' . $item->product->image) }}"
                                                                    alt="{{ $item->product_name }}"
                                                                    class="w-12 h-12 rounded-lg object-cover border border-slate-200 flex-shrink-0"
                                                                    onerror="this.style.display='none'"
                                                                >
                                                            @else
                                                                <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                                                    🛍️
                                                                </div>
                                                            @endif

                                                            <div class="min-w-0">
                                                                <p class="font-bold text-sm text-slate-800 truncate">
                                                                    {{ $item->product_name }}
                                                                </p>

                                                                <p class="text-xs text-slate-500 mt-1">
                                                                    Quantité : {{ $item->quantity }}
                                                                </p>
                                                            </div>

                                                        </div>

                                                        <p class="font-extrabold text-sm text-slate-900 whitespace-nowrap">
                                                            {{ number_format($item->total, 0, ',', ' ') }} FCFA
                                                        </p>

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>
                                    @endif

                                </div>

                            

                                {{-- CLANDO DE LIVRAISON --}}
                                @if($vendorOrder->clando_name || $vendorOrder->clando_phone)

                                    <div class="mt-4 rounded-2xl border border-indigo-200 bg-indigo-50 p-4">

                                        <div class="flex items-center gap-3 mb-4">

                                            <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center text-2xl">
                                                🛵
                                            </div>

                                            <div>
                                                <p class="font-extrabold text-indigo-900">
                                                    Clando chargé de la livraison
                                                </p>

                                                <p class="text-xs text-indigo-700 mt-1">
                                                    Livreur affecté à cette boutique
                                                </p>
                                            </div>

                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                                            <div class="bg-white border border-indigo-100 rounded-xl px-4 py-3">

                                                <p class="text-[11px] text-slate-400 font-semibold uppercase">
                                                    Nom du Clando
                                                </p>

                                                <p class="text-sm font-extrabold text-slate-800 mt-1">
                                                    {{ $vendorOrder->clando_name ?? 'Non renseigné' }}
                                                </p>

                                            </div>

                                            <div class="bg-white border border-indigo-100 rounded-xl px-4 py-3">

                                                <p class="text-[11px] text-slate-400 font-semibold uppercase">
                                                    Numéro du Clando
                                                </p>

                                                <p class="text-sm font-extrabold text-indigo-700 mt-1">
                                                    📞 {{ $vendorOrder->clando_phone ?? 'Non renseigné' }}
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                @endif

@endforeach

                        </div>


                        {{-- STATUT COMMANDE --}}
                        <div class="mt-3 text-sm">

                            <span class="text-slate-500">
                                État de la commande :
                            </span>

                            @if($o->status === 'processing')

                                <span class="font-bold text-blue-600">
                                    En préparation
                                </span>

                            @elseif($o->status === 'shipped')

                                <span class="font-bold text-indigo-600">
                                    Expédiée
                                </span>

                            @elseif($o->status === 'delivered')

                                <span class="font-bold text-green-600">
                                    Livrée
                                </span>

                            @elseif($o->status === 'cancelled')

                                <span class="font-bold text-red-600">
                                    Annulée
                                </span>

                            @else

                                <span class="font-bold text-yellow-600">
                                    En attente
                                </span>

                            @endif

                        </div>


                        {{-- DERNIER MOTIF DE REJET --}}

                        @php
                            $rejectedPayment = $o->vendorOrders
                                ->flatMap(function ($vendorOrder) {
                                    if ($vendorOrder->payment_status === 'paid') {
                                        return collect();
                                    }

                                    return $vendorOrder->payments;
                                })
                                ->where('status', 'rejected')
                                ->sortByDesc('validated_at')
                                ->first();
                        @endphp

                        @if($rejectedPayment)
                            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex items-start gap-3">

                                    <div class="text-xl">
                                        🔴
                                    </div>

                                    <div>
                                        <p class="font-bold text-red-700">
                                            Paiement rejeté
                                        </p>

                                        <p class="text-sm text-red-800 mt-1">
                                            <span class="font-semibold">
                                                Motif du rejet :
                                            </span>

                                            {{ $rejectedPayment->admin_note ?: 'Aucun motif renseigné.' }}
                                        </p>
                                    </div>

                                </div>
                            </div>
                        @endif
                    </div>


                    {{-- MONTANT --}}
                    <div class="text-left md:text-right">

                        <div class="text-xl font-extrabold text-slate-900">
                            {{ number_format($o->total, 0, ',', ' ') }} FCFA
                        </div>

                        @if($o->vendorOrders)

                            <p class="text-sm text-slate-500 mt-1">

                                {{ $o->vendorOrders->count() }}

                                {{ $o->vendorOrders->count() > 1 ? 'vendeurs' : 'vendeur' }}

                            </p>

                        @endif

                        <div class="mt-2 text-indigo-600 font-semibold text-sm">
                            Voir la commande →
                        </div>

                    </div>

                </div>

            </a>

        @empty

            <div class="bg-white border rounded-2xl p-10 text-center">

                <div class="text-5xl mb-4">
                    📦
                </div>

                <h2 class="text-xl font-bold">
                    Aucune commande
                </h2>

                <p class="text-slate-500 mt-2 text-center">
                    Vous n'avez encore effectué aucune commande.
                </p>

                <a
                    href="{{ route('products') }}"
                    class="inline-block mt-5 bg-indigo-600 text-white px-5 py-3 rounded-xl font-bold"
                >
                    Découvrir les produits
                </a>

            </div>

        @endforelse

    </div>


    {{-- PAGINATION --}}
    @if($orders->hasPages())

        <div class="mt-6">
            {{ $orders->links() }}
        </div>

    @endif

</div>

@endsection
