@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="mb-8">
        <p class="text-indigo-600 font-bold">Administration / Abonnements</p>
        <h1 class="text-3xl font-extrabold">Contrôle des abonnements vendeurs</h1>
<a href="{{ route('admin.dashboard') }}"
   class="inline-flex items-center justify-center mt-5 px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">
    ← Retour au tableau de bord
</a>
    </div>

    <div class="bg-white border rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('admin.subscriptions') }}" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="Rechercher un vendeur, email, plan, statut ou paiement..."
                class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
            >
        </div>

        <button
            type="submit"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-7 py-3 rounded-xl"
        >
            🔎 Rechercher
        </button>

        @if(!empty($search))
            <a
                href="{{ route('admin.subscriptions') }}"
                class="bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold px-6 py-3 rounded-xl text-center"
            >
                Réinitialiser
            </a>
        @endif
    </form>

    @if(!empty($search))
        <p class="text-sm text-slate-500 mt-3">
            Résultats pour : <strong>{{ $search }}</strong>
        </p>
    @endif
</div>

<div class="bg-white border rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-4 text-left">Vendeur</th>
                        <th class="p-4">Plan</th>
                        <th class="p-4">Paiement</th>
                        <th class="p-4">Preuve</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4">Fin</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($subscriptions as $s)
                    <tr class="border-t">

                        <td class="p-4">
                            <b>{{ $s->vendor->name }}</b>
                            <div class="text-slate-500">
                                {{ $s->vendor->email }}
                            </div>
                        </td>

                        <td class="p-4 text-center">
                            {{ $s->plan->name }}
                            <div>{{ $s->plan->price }} FCFA</div>
                        </td>

                        <td class="p-4 text-center">
                            {{ $s->payment_status }}
                            <div>{{ $s->payment_method }}</div>
                        </td>

                        @php
                            $proof = $paymentsBySubscription->get($s->id);
                        @endphp

                        <td class="p-4 text-center">
                            @if($proof)
                                <div class="space-y-2">
                                    <div class="font-semibold">
                                        {{ number_format((float) $proof->amount, 0, ',', ' ') }} FCFA
                                    </div>

                                    <div class="text-xs text-slate-500">
                                        {{ $proof->provider === 'airtel_money' ? 'Airtel Money' : 'Moov Money' }}
                                    </div>

                                    @if($proof->transaction_reference)
                                        <div class="text-xs text-slate-500">
                                            Réf. : {{ $proof->transaction_reference }}
                                        </div>
                                    @endif

                                    @if($proof->screenshot_path)
                                        <a
                                            href="{{ asset('storage/' . $proof->screenshot_path) }}"
                                            target="_blank"
                                            class="inline-flex items-center justify-center bg-indigo-600 text-white px-3 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700"
                                        >
                                            👁️ Voir la preuve
                                        </a>
                                    @else
                                        <span class="text-xs text-red-600">
                                            Aucune capture
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-400">
                                    Aucune preuve
                                </span>
                            @endif
                        </td>

                        <td class="p-4 text-center">
                            {{ $s->status }}
                        </td>

                        <td class="p-4 text-center">
                            {{ $s->ends_at ? $s->ends_at->format('d/m/Y H:i') : '-' }}
                        </td>

                        <td class="p-4">
                            <form
                                method="POST"
                                action="{{ route('admin.subscriptions.update', $s) }}"
                                class="flex gap-2"
                            >
                                @csrf

                                @if($s->status === 'pending')
                                    <button
                                        type="submit"
                                        name="action"
                                        value="activate"
                                        class="bg-emerald-600 text-white px-3 py-1 rounded-lg"
                                    >
                                        Activer
                                    </button>
                                @endif

                                <button
                                    type="submit"
                                    name="action"
                                    value="expire"
                                    class="bg-amber-500 text-white px-3 py-1 rounded-lg"
                                >
                                    Expirer
                                </button>

                                <button
                                    type="submit"
                                    name="action"
                                    value="cancel"
                                    class="bg-red-600 text-white px-3 py-1 rounded-lg"
                                >
                                    Annuler
                                </button>
                            </form>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $subscriptions->links() }}
    </div>

</div>
@endsection
