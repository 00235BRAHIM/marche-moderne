@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="mb-6">
        <a
            href="{{ route('vendor.dashboard') }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-indigo-100 hover:text-indigo-700 transition"
        >
            ← Retour au dashboard
        </a>
    </div>

    <h1 class="text-3xl font-bold">
        Abonnements vendeur
    </h1>

    <p class="text-gray-600 mt-2">
        Un abonnement actif et une certification validée sont nécessaires pour publier et gérer vos produits.
    </p>

    {{-- MESSAGE POUR VENDEUR NON CERTIFIÉ --}}
    @if(!auth()->user()->isCertifiedVendor())

        <div class="mt-6 p-5 rounded-2xl bg-amber-50 border border-amber-200 text-center">

            <div class="text-3xl mb-2">
                🔒
            </div>

            <h2 class="text-lg font-bold text-amber-900">
                Certification obligatoire
            </h2>

            <p class="text-amber-800 mt-1">
                Vous devez être certifié avant de pouvoir choisir un abonnement.
            </p>

            <a
                href="{{ route('vendor.certification') }}"
                class="inline-flex items-center justify-center mt-4 px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition"
            >
                🛡️ Demander la certification
            </a>

        </div>

    @endif

    <a
        href="{{ route('vendor.payment-settings') }}"
        class="inline-block mt-4 border rounded-lg px-4 py-2"
    >
        ⚙️ Mes numéros de transfert
    </a>

    @if(session('success'))
        <div class="mt-4 p-3 rounded bg-green-100 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="mt-4 p-3 rounded bg-yellow-100 text-yellow-800">
            {{ session('warning') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mt-4 p-3 rounded bg-red-100 text-red-800">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- PLANS --}}
    <div class="grid md:grid-cols-3 gap-6 mt-8">

        @foreach($plans as $plan)

            <div class="border rounded-2xl p-6 shadow-sm bg-white">

                <h2 class="text-xl font-semibold">
                    {{ $plan->name }}
                </h2>

                <div class="text-3xl font-bold mt-3">
                    {{ number_format($plan->price,0,',',' ') }} FCFA
                </div>

                <p class="text-gray-500 mt-1">
                    {{ $plan->duration_days }} jours
                </p>

                <p class="mt-4 text-gray-600">
                    {{ $plan->description }}
                </p>

                @if(auth()->user()->isCertifiedVendor())

                    {{-- VENDEUR CERTIFIÉ --}}
                    <form
                        method="POST"
                        action="{{ route('vendor.subscriptions.store') }}"
                        class="mt-5 space-y-3"
                    >

                        @csrf

                        <input
                            type="hidden"
                            name="subscription_plan_id"
                            value="{{ $plan->id }}"
                        >

                        <select
                            name="payment_method"
                            required
                            class="w-full border rounded-lg p-2"
                        >
                            <option value="airtel_money">
                                Airtel Money
                            </option>

                            <option value="moov_money">
                                Moov Money
                            </option>
                        </select>

                        <button
                            type="submit"
                            class="w-full bg-black text-white rounded-lg py-2 hover:bg-gray-800 transition"
                        >
                            Choisir ce plan
                        </button>

                    </form>

                @else

                    {{-- VENDEUR NON CERTIFIÉ --}}
                    <div class="mt-5">

                        <div
                            class="w-full bg-slate-100 text-slate-400 border border-slate-200 rounded-lg py-3 text-center font-bold cursor-not-allowed"
                        >
                            🔒 Certification requise
                        </div>

                        <p class="text-xs text-center text-gray-500 mt-2">
                            Certifiez votre compte pour choisir ce plan.
                        </p>

                    </div>

                @endif

            </div>

        @endforeach

    </div>

    {{-- MES ABONNEMENTS --}}
    <div class="mt-10 bg-white border rounded-2xl overflow-hidden">

        <div class="p-5 border-b">
            <h2 class="text-xl font-semibold">
                Mes abonnements
            </h2>
        </div>

        @forelse($subscriptions as $sub)

            <div class="p-5 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>

                    <div class="font-semibold">
                        {{ $sub->plan->name }}
                        —
                        {{ number_format($sub->plan->price,0,',',' ') }} FCFA
                    </div>

                    <div class="text-sm text-gray-500">
                        Paiement :
                        {{ $sub->payment_method }}
                        · Statut :
                        {{ $sub->status }}
                        ·
                        {{ $sub->payment_status }}
                    </div>

                    @if($sub->ends_at)

                        <div class="text-sm">
                            Expiration :
                            {{ $sub->ends_at->format('d/m/Y H:i') }}
                        </div>

                    @endif

                </div>

                @if($sub->payment_status !== 'paid')

                    <a
                        href="{{ route('vendor.subscriptions.payment',$sub) }}"
                        class="bg-green-600 text-white rounded-lg px-4 py-2 hover:bg-green-700 transition"
                    >
                        Envoyer la preuve de paiement
                    </a>

                @endif

            </div>

        @empty

            <div class="p-5 text-gray-500">
                Aucun abonnement pour le moment.
            </div>

        @endforelse

    </div>

</div>

@endsection