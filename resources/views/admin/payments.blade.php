@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold">Validation des paiements</h1>
            <p class="text-gray-600 mt-2">
                Vérifiez la capture, le numéro de transfert et le montant avant de valider.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">
            ← Retour au tableau de bord
        </a>
    </div>

    <form method="GET" action="{{ route('admin.payments') }}" class="mt-6">
        <div class="bg-white border rounded-2xl p-4 flex flex-col md:flex-row gap-3">

            <div class="flex-1">
                <input
                    type="search"
                    name="q"
                    value="{{ $search ?? request('q') }}"
                    placeholder="Rechercher un client, une commande, une référence, un numéro..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500"
                >
            </div>

            <button
                type="submit"
                class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700">
                🔎 Rechercher
            </button>

            @if(!empty($search ?? request('q')))
                <a
                    href="{{ route('admin.payments') }}"
                    class="inline-flex items-center justify-center border border-gray-200 px-6 py-3 rounded-xl font-semibold hover:bg-gray-50">
                    Réinitialiser
                </a>
            @endif

        </div>
    </form>

    @if(!empty($search ?? request('q')))
        <p class="text-sm text-gray-500 mt-3">
            Résultats pour :
            <strong class="text-gray-900">"{{ $search ?? request('q') }}"</strong>
        </p>
    @endif

    @if(session('success'))<div class="mt-4 p-3 rounded bg-green-100 text-green-800">{{session('success')}}</div>@endif
<div class="mt-7 space-y-4">@foreach($payments as $p)<div class="bg-white border rounded-2xl p-5 grid md:grid-cols-4 gap-5"><div><div class="font-semibold">{{$p->user->name}}</div><div class="text-sm">{{strtoupper(str_replace('_',' ',$p->provider))}}</div><div class="text-sm">Transfert : {{$p->transfer_number}}</div></div><div><div>Montant : <strong>{{number_format($p->amount,0,',',' ')}} {{$p->currency}}</strong></div><div>Commande : {{$p->order?->order_number ?? 'Abonnement #'.$p->vendor_subscription_id}}</div><div>Réf. : {{$p->transaction_reference ?: '—'}}</div></div><div><a href="{{asset('storage/'.$p->screenshot_path)}}" target="_blank" class="inline-block border rounded-lg px-4 py-2">Voir la capture</a><div class="mt-2 text-sm">Statut : {{$p->status}}</div></div><div>@if($p->status==='pending')<form method="POST" action="{{route('admin.payments.validate',$p)}}" class="space-y-2">@csrf<input name="admin_note" placeholder="Note admin (facultatif)" class="w-full border rounded-lg p-2"><div class="flex gap-2 justify-center"><button name="action" value="approve" class="bg-green-600 text-white rounded-lg px-4 py-2">Valider</button><button name="action" value="reject" class="bg-red-600 text-white rounded-lg px-4 py-2">Rejeter</button></div></form>@else<span class="text-gray-500">Traité le {{$p->validated_at?->format('d/m/Y H:i')}}</span>@endif</div></div>@endforeach</div><div class="mt-6">{{$payments->links()}}</div></div>@endsection
