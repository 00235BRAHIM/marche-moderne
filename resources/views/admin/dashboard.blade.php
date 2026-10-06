@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

<div class="bg-white border rounded-2xl shadow-sm p-6 mb-8 text-center">

    <div class="flex flex-col items-center justify-center mb-5 text-center">
        <div>
            <h2 class="text-xl font-extrabold">🔔 Notifications</h2>
            <p class="text-slate-500 text-sm mt-1">
                @if($unreadNotifications > 0)
                    <span class="font-bold text-red-600">
                        {{ $unreadNotifications }} notification(s) non lue(s)
                    </span>
                @else
                    Toutes les notifications sont lues.
                @endif
            </p>
        </div>

        @if($unreadNotifications > 0)
            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                <button class="border px-4 py-2 rounded-xl text-sm font-medium hover:bg-slate-50">
                    Tout marquer comme lu
                </button>
            </form>
        @endif
    </div>

    @forelse($notifications as $notification)

        <div class="border-t py-4 flex flex-col items-center justify-center gap-4 text-center {{ $notification->read_at ? 'opacity-60' : '' }}">

            <div class="flex flex-col items-center justify-center gap-2 text-center">

                <div class="text-2xl">
                    {{ $notification->data['icon'] ?? '🔔' }}
                </div>

                <div class="text-center w-full">
                    <p class="font-bold">
                        {{ $notification->data['title'] ?? 'Notification' }}
                    </p>

                    <p class="text-slate-600 text-sm mt-1">
                        {{ $notification->data['message'] ?? '' }}
                    </p>

                    <p class="text-xs text-slate-400 mt-2">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>

            </div>

            <div class="flex items-center justify-center gap-2">

                @if(!empty($notification->data['url']))
                    <form method="POST"
                          action="{{ route('admin.notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit"
                                class="text-indigo-600 text-sm font-semibold">
                            Ouvrir
                        </button>
                    </form>
                @endif

                @if(!$notification->read_at)
                    <form method="POST"
                          action="{{ route('admin.notifications.read', $notification->id) }}">
                        @csrf
                        <button class="text-slate-500 text-sm">
                            Lu
                        </button>
                    </form>
                @endif

            </div>

        </div>

    @empty

        <div class="border-t pt-5 text-slate-500 text-center">
            Aucune notification pour le moment.
        </div>

    @endforelse

</div>

<div class="order-1 flex flex-col items-center text-center gap-5 mb-8">
    <div class="w-full text-center">
        <p class="text-indigo-600 font-bold">Administration</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold break-words">Dashboard Marche Moderne</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap gap-2 w-full lg:w-auto">
        <a
            href="{{ route('admin.vendors') }}"
            class="inline-flex items-center justify-center gap-2 border px-4 py-3 rounded-xl font-semibold hover:bg-slate-50 transition text-center"
        >
            👥 Vendeurs
        </a>

        <a
            href="{{ route('admin.subscriptions') }}"
            class="inline-flex items-center justify-center gap-2 border px-4 py-3 rounded-xl font-semibold hover:bg-slate-50 transition text-center"
        >
            📋 Abonnements
        </a>

        <a
            href="{{ route('admin.payments') }}"
            class="inline-flex items-center justify-center gap-2 border px-4 py-3 rounded-xl font-semibold hover:bg-slate-50 transition text-center"
        >
            💳 Paiements à valider
        </a>

        @if(auth()->user()->isSuperAdmin())
            <a
                href="{{ route('admin.super-admin.users') }}"
                class="inline-flex items-center justify-center gap-2 bg-purple-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-purple-700 transition text-center"
            >
                👥 Gestion des utilisateurs
            </a>
        @endif
    </div>
</div>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">@foreach([['Utilisateurs',$users],['Vendeurs',$vendors],['À certifier',$pendingVendors],['Abonnements actifs',$activeSubscriptions],['Produits',$products],['Commandes',$orders],['CA encaissé',number_format($revenue,0,',',' ').' FCFA']] as $card)<div class="bg-white border rounded-2xl p-6 shadow-sm text-center"><p class="text-slate-500">{{$card[0]}}</p><p class="text-3xl font-extrabold mt-2">{{$card[1]}}</p></div>@endforeach</div>
<div class="mt-8 grid md:grid-cols-3 gap-4"><a href="{{route('admin.vendors')}}" class="bg-slate-900 text-white rounded-2xl p-6 text-center lg:text-left"><b>Certification vendeurs</b><p class="text-slate-300 mt-1">Valider, suspendre ou examiner les dossiers.</p></a><a href="{{route('admin.subscriptions')}}" class="bg-indigo-600 text-white rounded-2xl p-6 text-center lg:text-left"><b>Gestion des abonnements</b><p class="text-indigo-100 mt-1">Activer les paiements et contrôler les accès.</p></a><a href="{{route('admin.payments')}}" class="bg-emerald-600 text-white rounded-2xl p-6 text-center lg:text-left"><b>Validation des paiements</b><p class="text-emerald-100 mt-1">Vérifier les captures et valider les transferts.</p></a><a href="{{route('admin.plans')}}" class="bg-white border rounded-2xl p-6 text-center lg:text-left"><b>Plans d’abonnement</b><p class="text-slate-500 mt-1">Créer et gérer les offres vendeurs.</p></a></div>
</div>
@endsection
