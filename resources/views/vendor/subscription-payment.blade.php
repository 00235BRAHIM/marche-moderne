@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-8">

    <h1 class="text-3xl font-bold">
        Payer l'abonnement
    </h1>

    <div class="mt-5 bg-white border rounded-2xl p-6">

        <h2 class="text-xl font-semibold">
            {{ $subscription->plan->name }}
        </h2>

        <div class="text-3xl font-bold mt-2">
            {{ number_format($subscription->plan->price, 0, ',', ' ') }} FCFA
        </div>

        <p class="text-gray-600 mt-3">
            Effectuez d'abord le transfert vers l'un des numéros
            de l'administrateur ci-dessous, puis joignez la capture
            de la transaction.
        </p>

        {{-- NUMÉROS DE L'ADMINISTRATEUR --}}
        <div class="grid md:grid-cols-2 gap-4 mt-6">

            @forelse($methods as $m)

                <div class="border rounded-xl p-4">

                    <div class="font-semibold text-lg">
                        {{ $m['name'] }}
                    </div>

                    <div class="mt-2">
                        Nom :
                        <strong>
                            {{ config('payment.admin.name') }}
                        </strong>
                    </div>

                    <div class="mt-1">
                        Numéro de transfert :
                        <strong>
                            {{ $m['transfer_number'] }}
                        </strong>
                    </div>

                </div>

            @empty

                <div class="md:col-span-2 bg-yellow-50 text-yellow-800 p-4 rounded-xl">
                    Aucun moyen de paiement administrateur n'est configuré.
                </div>

            @endforelse

        </div>

    </div>

    {{-- FORMULAIRE DE PAIEMENT --}}
    <form
        method="POST"
        action="{{ route('vendor.subscriptions.payment.submit', $subscription) }}"
        enctype="multipart/form-data"
        class="mt-6 bg-white border rounded-2xl p-6 space-y-4"
    >

        @csrf

        <div>
            <label class="font-medium">
                Moyen de paiement
            </label>

            <select
                name="provider"
                required
                class="w-full border rounded-xl p-3 mt-2"
            >

                <option value="">
                    Choisir le moyen de paiement
                </option>

                @foreach($methods as $m)

                    <option value="{{ $m['provider'] }}">
                        {{ $m['name'] }} — {{ $m['transfer_number'] }}
                    </option>

                @endforeach

            </select>
        </div>

        <div>
            <label class="font-medium">
                Nom du payeur
            </label>

            <input
                name="payer_name"
                required
                value="{{ auth()->user()->name }}"
                placeholder="Nom du payeur"
                class="w-full border rounded-xl p-3 mt-2"
            >
        </div>

        <div>
            <label class="font-medium">
                Référence de transaction
            </label>

            <input
                name="transaction_reference"
                placeholder="Référence/ID de transaction (facultatif)"
                class="w-full border rounded-xl p-3 mt-2"
            >
        </div>

        <div>
            <label class="font-medium">
                Capture de la transaction
            </label>

            <input
                type="file"
                name="screenshot"
                accept="image/*"
                required
                class="w-full border rounded-xl p-3 mt-2"
            >
        </div>

        <button
            type="submit"
            class="w-full bg-slate-950 text-white rounded-xl py-3 font-bold"
        >
            Soumettre pour validation
        </button>

    </form>

</div>

@endsection
