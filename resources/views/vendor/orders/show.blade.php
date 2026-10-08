@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 text-center">

        <div class="w-full md:w-auto">
            <p class="text-indigo-600 font-bold uppercase text-sm">
                Espace vendeur
            </p>

            <h1 class="text-3xl font-extrabold text-gray-900 text-center">
                Commande {{ $vendorOrder->order_number }}
            </h1>

            <p class="text-gray-500 mt-2 text-center">
                Détails de la commande et vérification du paiement.
            </p>
        </div>

        <a href="{{ route('vendor.orders.index') }}"
           class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 w-full md:w-auto">
            ← Retour aux commandes
        </a>

    </div>


    {{-- Messages --}}
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-800 px-5 py-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-800 px-5 py-4">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Informations principales --}}
    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Commande --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="p-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">
                    📦 Produits commandés
                </h2>
            </div>

            <div class="p-5">

                <div class="space-y-4">

                    @foreach($vendorOrder->items as $item)

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 bg-gray-50 rounded-xl">

                            <div>
                                <h3 class="font-bold text-gray-900">
                                    {{ $item->product_name }}
                                </h3>

                                <p class="text-gray-500 mt-1">
                                    Quantité :
                                    <strong>{{ $item->quantity }}</strong>
                                </p>

                                <p class="text-gray-500">
                                    Prix unitaire :
                                    <strong>
                                        {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                                    </strong>
                                </p>
                            </div>

                            <div class="text-left sm:text-right">

                                <p class="text-lg font-extrabold text-gray-900">
                                    {{ number_format($item->total, 0, ',', ' ') }} FCFA
                                </p>

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- Total --}}
                <div class="mt-6 pt-5 border-t border-gray-200 flex items-center justify-between">

                    <span class="text-lg font-bold text-gray-900">
                        Total vendeur
                    </span>

                    <span class="text-2xl font-extrabold text-indigo-600">
                        {{ number_format($vendorOrder->total, 0, ',', ' ') }} FCFA
                    </span>

                </div>

            </div>

        </div>


        {{-- Client --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="p-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900">
                    👤 Client
                </h2>
            </div>

            <div class="p-5 space-y-4">

                <div>
                    <p class="text-xs text-gray-500 uppercase">
                        Nom
                    </p>

                    <p class="font-bold text-gray-900 mt-1">
                        {{ $vendorOrder->order->user->name ?? 'Non renseigné' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 uppercase">
                        Téléphone
                    </p>

                    <p class="font-bold text-gray-900 mt-1">
                        {{ $vendorOrder->order->shipping_phone ?? 'Non renseigné' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 uppercase">
                        Adresse de livraison
                    </p>

                    <p class="text-gray-700 mt-1">
                        {{ $vendorOrder->order->shipping_address ?? 'Non renseignée' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 uppercase">
                        Date
                    </p>

                    <p class="font-semibold text-gray-900 mt-1">
                        {{ $vendorOrder->created_at->format('d/m/Y à H:i') }}
                    </p>
                </div>

            </div>

        </div>

    </div>


    {{-- Paiement --}}
    <div class="mt-6 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">

            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    @if($vendorOrder->order->payment_method === 'cash_on_delivery')
                        💵 Paiement à la livraison
                    @else
                        💳 Paiement et preuve de transfert
                    @endif
                </h2>

                <p class="text-gray-500 mt-1">
                    @if($vendorOrder->order->payment_method === 'cash_on_delivery')
                        Le client paiera en espèces lors de la livraison.
                    @else
                        Vérifiez la preuve envoyée par le client avant d'accepter le paiement.
                    @endif
                </p>
            </div>

            @if($vendorOrder->payment_status === 'paid')

                <span class="inline-flex px-4 py-2 rounded-full bg-green-100 text-green-700 font-bold">
                    ✓ Paiement accepté
                </span>

            @elseif($vendorOrder->order->payment_method === 'cash_on_delivery')

                @if($vendorOrder->status === 'pending')
                    <span class="inline-flex px-4 py-2 rounded-full bg-yellow-100 text-yellow-700 font-bold">
                        ⏳ En attente d'acceptation
                    </span>
                @elseif($vendorOrder->status === 'processing')
                    <span class="inline-flex px-4 py-2 rounded-full bg-blue-100 text-blue-700 font-bold">
                        🔄 En préparation
                    </span>
                @elseif($vendorOrder->status === 'shipped')
                    <span class="inline-flex px-4 py-2 rounded-full bg-purple-100 text-purple-700 font-bold">
                        🚚 En livraison
                    </span>
                @endif

            @else

                <span class="inline-flex px-4 py-2 rounded-full bg-yellow-100 text-yellow-700 font-bold">
                    ⏳ Paiement en attente
                </span>

            @endif

        </div>


        <div class="p-5">

            {{-- ========================= --}}
            {{-- PAIEMENT À LA LIVRAISON --}}
            {{-- ========================= --}}

            @if($vendorOrder->order->payment_method === 'cash_on_delivery')

                @if($vendorOrder->status === 'pending')

                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">

                        <div class="flex items-start gap-4">

                            <div class="text-4xl">
                                💵
                            </div>

                            <div class="flex-1">

                                <h3 class="text-xl font-bold text-amber-900">
                                    Paiement à la livraison
                                </h3>

                                <p class="mt-2 text-sm text-amber-800">
                                    Le client a choisi de payer en espèces à la livraison.
                                    Aucune preuve de transfert n'est nécessaire.
                                </p>

                                <div class="mt-4 rounded-xl bg-white border border-amber-200 p-4">

                                    <p class="text-sm font-semibold text-gray-800">
                                        En acceptant cette commande :
                                    </p>

                                    <ul class="mt-2 text-sm text-gray-700 space-y-1">
                                        <li>✓ Le stock sera diminué immédiatement.</li>
                                        <li>✓ La commande passera en préparation.</li>
                                        <li>✓ Vous pourrez ensuite l'expédier.</li>
                                        <li>✓ Le paiement restera en attente jusqu'à la livraison.</li>
                                    </ul>

                                </div>

                                <form
    method="POST"
    action="{{ route('vendor.orders.accept-cod', $vendorOrder) }}"
    class="mt-5"
>
    @csrf

    <div class="rounded-2xl border border-amber-200 bg-white p-5 mb-5">

        <div class="flex items-center gap-3 mb-4">

            <div class="text-3xl">
                🛵
            </div>

            <div>
                <h4 class="text-lg font-bold text-gray-900">
                    Informations du Clando
                </h4>

                <p class="text-sm text-gray-500">
                    Renseignez le livreur chargé de cette commande.
                </p>
            </div>

        </div>

        <div class="grid md:grid-cols-2 gap-4">

            <div>
                <label
                    for="clando_name"
                    class="block text-sm font-bold text-gray-700 mb-2"
                >
                    Nom du Clando *
                </label>

                <input
                    type="text"
                    id="clando_name"
                    name="clando_name"
                    value="{{ old('clando_name', $vendorOrder->clando_name) }}"
                    required
                    maxlength="150"
                    class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 px-4 py-3"
                    placeholder="Exemple : Moussa Mahamat"
                >
            </div>

            <div>
                <label
                    for="clando_phone"
                    class="block text-sm font-bold text-gray-700 mb-2"
                >
                    Numéro du Clando *
                </label>

                <input
                    type="tel"
                    id="clando_phone"
                    name="clando_phone"
                    value="{{ old('clando_phone', $vendorOrder->clando_phone) }}"
                    required
                    maxlength="30"
                    class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 px-4 py-3"
                    placeholder="Exemple : 66 12 34 56"
                >
            </div>

        </div>

        <p class="mt-3 text-xs text-gray-500">
            * Le nom et le numéro du Clando sont obligatoires.
        </p>

    </div>

    <button
        type="submit"
        class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-6 py-4 font-bold text-white shadow-sm transition hover:bg-amber-700"
    >
        ✓ Confirmer l'acceptation
    </button>

</form>

                            </div>

                        </div>

                    </div>


                @elseif($vendorOrder->status === 'processing')

                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-6">

                        <div class="flex items-start gap-4">

                            <div class="text-4xl">
                                🔄
                            </div>

                            <div>

                                <h3 class="text-xl font-bold text-blue-900">
                                    Commande acceptée
                                </h3>

                                <p class="mt-2 text-sm text-blue-800">
                                    La commande est en préparation.
                                    Le paiement sera encaissé en espèces lors de la livraison.
                                </p>

@if($vendorOrder->clando_name || $vendorOrder->clando_phone)

    <div class="mt-5 rounded-2xl border border-blue-200 bg-white p-5">

        <div class="flex items-center gap-3 mb-4">

            <div class="text-3xl">
                🛵
            </div>

            <div>
                <h4 class="font-bold text-blue-900">
                    Clando chargé de la livraison
                </h4>

                <p class="text-sm text-blue-700">
                    Informations du livreur
                </p>
            </div>

        </div>

        <div class="grid md:grid-cols-2 gap-4">

            <div class="rounded-xl bg-blue-50 p-4">
                <p class="text-xs uppercase text-blue-600 font-bold">
                    Nom
                </p>

                <p class="font-bold text-gray-900 mt-1">
                    {{ $vendorOrder->clando_name ?? 'Non renseigné' }}
                </p>
            </div>

            <div class="rounded-xl bg-blue-50 p-4">
                <p class="text-xs uppercase text-blue-600 font-bold">
                    Téléphone
                </p>

                <p class="font-bold text-gray-900 mt-1">
                    {{ $vendorOrder->clando_phone ?? 'Non renseigné' }}
                </p>
            </div>

        </div>

    </div>

@endif

                            </div>

                        </div>

                    </div>


                @elseif($vendorOrder->status === 'shipped')

                    <div class="rounded-2xl border border-purple-200 bg-purple-50 p-6">

                        <div class="flex items-start gap-4">

                            <div class="text-4xl">
                                🚚
                            </div>

                            <div>

                                <h3 class="text-xl font-bold text-purple-900">
                                    Commande expédiée
                                </h3>

                                <p class="mt-2 text-sm text-purple-800">
                                    Rendez-vous chez le client et encaissez le montant de
                                    {{ number_format($vendorOrder->total, 0, ',', ' ') }} FCFA
                                    lors de la livraison.
                                </p>

@if($vendorOrder->clando_name || $vendorOrder->clando_phone)

    <div class="mt-5 rounded-2xl border border-purple-200 bg-white p-5">

        <div class="flex items-center gap-3 mb-4">

            <div class="text-3xl">
                🛵
            </div>

            <div>
                <h4 class="font-bold text-purple-900">
                    Clando chargé de la livraison
                </h4>
            </div>

        </div>

        <div class="grid md:grid-cols-2 gap-4">

            <div class="rounded-xl bg-purple-50 p-4">
                <p class="text-xs uppercase text-purple-600 font-bold">
                    Nom
                </p>

                <p class="font-bold text-gray-900 mt-1">
                    {{ $vendorOrder->clando_name ?? 'Non renseigné' }}
                </p>
            </div>

            <div class="rounded-xl bg-purple-50 p-4">
                <p class="text-xs uppercase text-purple-600 font-bold">
                    Téléphone
                </p>

                <p class="font-bold text-gray-900 mt-1">
                    {{ $vendorOrder->clando_phone ?? 'Non renseigné' }}
                </p>
            </div>

        </div>

    </div>

@endif

                            </div>

                        </div>

                    </div>


                @elseif($vendorOrder->status === 'delivered' && $vendorOrder->payment_status === 'paid')

                    <div class="rounded-2xl border border-green-200 bg-green-50 p-6">

                        <div class="flex items-start gap-4">

                            <div class="text-4xl">
                                ✅
                            </div>

                            <div>

                                <h3 class="text-xl font-bold text-green-900">
                                    Paiement encaissé
                                </h3>

                                <p class="mt-2 text-sm text-green-800">
                                    Le client a payé en espèces et la commande a été livrée.
                                </p>

                            </div>

                        </div>

                    </div>

                @endif


            {{-- ========================= --}}
            {{-- MOBILE MONEY --}}
            {{-- ========================= --}}

            @else

                @php
                    $pendingPayment = $vendorOrder->payments
                        ->where('status', 'pending')
                        ->sortByDesc('created_at')
                        ->first();
                @endphp


                @if($pendingPayment)

                    <div class="grid lg:grid-cols-2 gap-8">

                        {{-- Informations transfert --}}
                        <div>

                            <h3 class="text-lg font-bold text-gray-900 mb-5">
                                Informations du transfert
                            </h3>

                            <div class="space-y-4">

                                <div class="p-4 bg-gray-50 rounded-xl">
                                    <p class="text-xs text-gray-500 uppercase">
                                        Opérateur
                                    </p>

                                    <p class="font-bold text-gray-900 mt-1">
                                        {{ strtoupper(str_replace('_', ' ', $pendingPayment->provider)) }}
                                    </p>
                                </div>

                                <div class="p-4 bg-gray-50 rounded-xl">
                                    <p class="text-xs text-gray-500 uppercase">
                                        Numéro de transfert
                                    </p>

                                    <p class="font-bold text-gray-900 mt-1">
                                        {{ $pendingPayment->transfer_number ?? 'Non renseigné' }}
                                    </p>
                                </div>

                                <div class="p-4 bg-gray-50 rounded-xl">
                                    <p class="text-xs text-gray-500 uppercase">
                                        Nom du payeur
                                    </p>

                                    <p class="font-bold text-gray-900 mt-1">
                                        {{ $pendingPayment->payer_name ?? 'Non renseigné' }}
                                    </p>
                                </div>

                                <div class="p-4 bg-gray-50 rounded-xl">
                                    <p class="text-xs text-gray-500 uppercase">
                                        Référence de transaction
                                    </p>

                                    <p class="font-bold text-gray-900 mt-1">
                                        {{ $pendingPayment->transaction_reference ?? 'Non renseignée' }}
                                    </p>
                                </div>

                                <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100">

                                    <p class="text-xs text-indigo-600 uppercase font-bold">
                                        Montant
                                    </p>

                                    <p class="text-2xl font-extrabold text-indigo-700 mt-1">
                                        {{ number_format($pendingPayment->amount, 0, ',', ' ') }} FCFA
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Capture --}}
                        <div>

                            <h3 class="text-lg font-bold text-gray-900 mb-5">
                                🧾 Capture du transfert
                            </h3>

                            @if($pendingPayment->screenshot_path)

                                <div class="border border-gray-200 rounded-2xl overflow-hidden bg-gray-50">

                                    <a
                                        href="{{ asset('storage/' . $pendingPayment->screenshot_path) }}"
                                        target="_blank"
                                    >

                                        <img
                                            src="{{ asset('storage/' . $pendingPayment->screenshot_path) }}"
                                            alt="Preuve de transfert"
                                            class="w-full max-h-[500px] object-contain cursor-pointer hover:opacity-90"
                                        >

                                    </a>

                                </div>

                                <p class="text-sm text-gray-500 mt-3 text-center">
                                    Cliquez sur l'image pour l'ouvrir en grand.
                                </p>

                            @else

                                <div class="p-8 bg-gray-50 rounded-2xl text-center text-gray-500">
                                    Aucune capture de transfert disponible.
                                </div>

                            @endif

                        </div>

                    </div>


                    {{-- Actions vendeur --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">

                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            Décision du vendeur
                        </h3>

                        <div class="grid md:grid-cols-2 gap-4">

                            {{-- Accepter --}}
                            <form
                                action="{{ route('vendor.orders.payments.approve', [$vendorOrder, $pendingPayment]) }}"
                                method="POST"
                                onsubmit="return confirm('Confirmez-vous l’acceptation de cette preuve de paiement ?');"
                            >
                    <div class="mb-6 rounded-2xl border-2 border-indigo-200 bg-indigo-50 p-5">

                        <div class="flex items-center gap-3 mb-4">

                            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg">
                                🛵
                            </div>

                            <div>
                                <h3 class="font-extrabold text-indigo-900">
                                    Clando chargé de la livraison
                                </h3>

                                <p class="text-sm text-indigo-700">
                                    Renseignez le livreur avant d'accepter le paiement.
                                </p>
                            </div>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Nom du Clando *
                                </label>

                                <input
                                    type="text"
                                    name="clando_name"
                                    required
                                    maxlength="150"
                                    placeholder="Ex : Mahamat Ali"
                                    value="{{ old('clando_name', $vendorOrder->clando_name) }}"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 font-semibold focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Numéro du Clando *
                                </label>

                                <input
                                    type="text"
                                    name="clando_phone"
                                    required
                                    maxlength="30"
                                    placeholder="Ex : +235 66 00 00 00"
                                    value="{{ old('clando_phone', $vendorOrder->clando_phone) }}"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 font-semibold focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                        </div>

                    </div>



                                @csrf

                                <button
                                    type="submit"
                                    class="w-full px-5 py-4 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700"
                                >
                                    ✓ Accepter le paiement
                                </button>

                            </form>


                            {{-- Refuser --}}
                            <div>

                                <button
                                    type="button"
                                    onclick="document.getElementById('reject-payment-form').classList.toggle('hidden')"
                                    class="w-full px-5 py-4 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700"
                                >
                                    ✕ Refuser le paiement
                                </button>

                                <form
                                    id="reject-payment-form"
                                    action="{{ route('vendor.orders.payments.reject', [$vendorOrder, $pendingPayment]) }}"
                                    method="POST"
                                    class="hidden mt-4"
                                >

                                    @csrf

                                    <label class="block text-sm font-bold text-gray-700 mb-2">
                                        Motif du refus
                                    </label>

                                    <textarea
                                        name="note"
                                        rows="4"
                                        required
                                        maxlength="1000"
                                        class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                                        placeholder="Exemple : La capture est illisible ou le montant transféré ne correspond pas à la commande."
                                    ></textarea>

                                    <button
                                        type="submit"
                                        class="w-full mt-3 px-5 py-3 rounded-xl bg-red-700 text-white font-bold hover:bg-red-800"
                                    >
                                        Confirmer le refus
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                @elseif($vendorOrder->payment_status === 'paid')

                    <div class="p-6 bg-green-50 border border-green-200 rounded-2xl">

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

                            <div>

                                @if($vendorOrder->status === 'processing')

                                    <p class="font-bold text-lg text-blue-700">
                                        🔵 En préparation
                                    </p>

                                    <p class="mt-1 text-blue-700">
                                        Le paiement est accepté.
                                        Préparez la commande avant son expédition.
                                    </p>

                                @elseif($vendorOrder->status === 'shipped')

                                    <p class="font-bold text-lg text-indigo-700">
                                        📦 Commande expédiée
                                    </p>

                                    <p class="mt-1 text-indigo-700">
                                        La commande a été remise pour livraison.
                                    </p>

                                @elseif($vendorOrder->status === 'delivered')

                                    <p class="font-bold text-lg text-green-700">
                                        🚚 Commande livrée
                                    </p>

                                    <p class="mt-1 text-green-700">
                                        La commande a été marquée comme livrée.
                                    </p>

                                @endif

                            </div>

                        </div>

                    </div>

                @else

                    <div class="p-6 bg-yellow-50 border border-yellow-200 rounded-2xl">

                        <p class="font-bold text-lg text-yellow-800">
                            ⏳ Paiement en attente
                        </p>

                        <p class="mt-1 text-yellow-700">
                            Le client doit envoyer une preuve de paiement Mobile Money.
                        </p>

                    </div>

                @endif

            @endif


            {{-- ACTION EXPÉDITION --}}
            @if($vendorOrder->status === 'processing')

                <div class="mt-8 pt-6 border-t border-gray-200">

                    <form
                        method="POST"
                        action="{{ route('vendor.orders.ship', $vendorOrder) }}"
                        onsubmit="return confirm('Confirmer que cette commande est prête et expédiée ?');"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700"
                        >
                            📦 Marquer comme expédiée
                        </button>

                    </form>

                </div>

            @endif


            {{-- ACTION LIVRAISON --}}
            @if($vendorOrder->status === 'shipped')

                <div class="mt-8 pt-6 border-t border-gray-200">

                    <form
                        method="POST"
                        action="{{ route('vendor.orders.deliver', $vendorOrder) }}"
                        onsubmit="return confirm('Confirmer que cette commande a été livrée au client ?');"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700"
                        >
                            🚚 Marquer comme livrée
                        </button>

                    </form>

                </div>

            @endif

        </div>

    </div>
</div>

@endsection
