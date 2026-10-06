@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

    {{-- EN-TÊTE VENDEUR --}}
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">

        <div class="bg-gradient-to-r from-indigo-600 via-indigo-600 to-violet-600 px-5 sm:px-8 py-7 sm:py-8 text-white text-center">

            <p class="text-indigo-100 font-bold text-sm sm:text-base">
                🏪 Espace vendeur
            </p>

            <div class="mt-2">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold break-words">
                    {{ auth()->user()->name }}
                    @if(auth()->user()->is_certified)
                        <span class="text-blue-200">✓</span>
                    @endif
                </h1>

                <p class="text-indigo-100 mt-2 text-sm sm:text-base">
                    Gérez votre boutique, vos produits et vos commandes.
                </p>
            </div>

            <div class="flex flex-wrap justify-center gap-2 mt-5 text-sm">

                <span class="inline-flex items-center gap-1 bg-white/15 backdrop-blur px-3 py-2 rounded-xl">
                    🛡️ Certification :
                    <b>
                        {{ auth()->user()->is_certified ? 'Certifié' : 'En attente' }}
                    </b>
                </span>

                <span class="inline-flex items-center gap-1 bg-white/15 backdrop-blur px-3 py-2 rounded-xl">
                    💳 Abonnement :
                    <b>
                        {{ $subscription?->plan?->name ?? 'Aucun' }}
                    </b>
                </span>

            </div>

        </div>

        {{-- ACTIONS --}}
        <div class="p-4 sm:p-6">

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                <a
                    href="{{ route('vendor.shop.edit') }}"
                    class="group flex flex-col items-center justify-center text-center min-h-[95px] rounded-2xl bg-indigo-50 text-indigo-700 border border-indigo-100 hover:bg-indigo-100 transition p-3"
                >
                    <span class="text-2xl">🏪</span>
                    <span class="font-bold text-sm mt-2">Ma boutique</span>
                </a>

                <a
                    href="{{ route('vendor.certification') }}"
                    class="group flex flex-col items-center justify-center text-center min-h-[95px] rounded-2xl bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100 transition p-3"
                >
                    <span class="text-2xl">🛡️</span>
                    <span class="font-bold text-sm mt-2">Certification</span>
                </a>

                <a
                    href="{{ route('vendor.subscriptions') }}"
                    class="group flex flex-col items-center justify-center text-center min-h-[95px] rounded-2xl bg-violet-50 text-violet-700 border border-violet-100 hover:bg-violet-100 transition p-3"
                >
                    <span class="text-2xl">💳</span>
                    <span class="font-bold text-sm mt-2">Abonnement</span>
                </a>

                <a
                    href="{{ route('vendor.payment-settings') }}"
                    class="group flex flex-col items-center justify-center text-center min-h-[95px] rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100 transition p-3"
                >
                    <span class="text-2xl">📱</span>
                    <span class="font-bold text-sm mt-2">Transferts</span>
                </a>

                <a
                    href="{{ route('vendor.orders.index') }}"
                    class="group flex flex-col items-center justify-center text-center min-h-[95px] rounded-2xl bg-slate-900 text-white hover:bg-slate-800 transition p-3 col-span-2 sm:col-span-2 lg:col-span-1"
                >
                    <span class="text-2xl">📦</span>
                    <span class="font-bold text-sm mt-2">Mes commandes</span>
                </a>

            </div>

        </div>

    </div>


    {{-- ACCÈS VENDEUR VERROUILLÉ --}}
    @if(!auth()->user()->canSell())

        <div class="mt-6 bg-amber-50 border border-amber-200 rounded-3xl p-5 sm:p-6 text-center">

            <div class="text-4xl mb-3">
                🔒
            </div>

            <h2 class="font-extrabold text-xl text-amber-900">
                Votre espace de vente est verrouillé
            </h2>

            <p class="text-slate-600 mt-2 max-w-2xl mx-auto leading-6">
                Vous devez être <b>certifié</b> et avoir un
                <b>abonnement actif</b> pour gérer vos produits et ventes.
            </p>

        </div>

    @else

        {{-- STATISTIQUES --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mt-6">

            <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 shadow-sm text-center">
                <div class="mx-auto w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl">
                    🛍️
                </div>

                <p class="text-slate-500 mt-4 font-medium">
                    Produits
                </p>

                <b class="text-4xl font-extrabold block mt-1 text-slate-900">
                    {{ $products }}
                </b>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 shadow-sm text-center">
                <div class="mx-auto w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                    📦
                </div>

                <p class="text-slate-500 mt-4 font-medium">
                    Commandes
                </p>

                <b class="text-4xl font-extrabold block mt-1 text-slate-900">
                    {{ $orders }}
                </b>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 shadow-sm text-center">
                <div class="mx-auto w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                    💰
                </div>

                <p class="text-slate-500 mt-4 font-medium">
                    Commandes payées
                </p>

                <b class="text-4xl font-extrabold block mt-1 text-slate-900">
                    {{ $paidOrders }}
                </b>
            </div>

        </div>


        {{-- REVENUS --}}
        <div class="mt-6 bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">

            <div class="p-5 sm:p-7 border-b border-slate-100">

                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

                    <div class="text-center lg:text-left">

                        <div class="inline-flex items-center gap-2 text-indigo-600 font-bold">
                            💰 Revenus
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">
                            Votre chiffre d'affaires
                        </h2>

                        <p class="text-slate-500 mt-2 text-sm sm:text-base">
                            Seules les commandes payées sont comptabilisées.
                        </p>

                    </div>


                    {{-- FILTRES --}}
                    <div class="w-full lg:w-auto">

                        <div class="grid grid-cols-3 bg-slate-100 rounded-2xl p-1 gap-1">

                            <a
                                href="{{ route('vendor.dashboard', ['period' => 'daily', 'year' => $selectedYear, 'month' => $selectedMonth, 'day' => $selectedDay]) }}"
                                class="flex items-center justify-center px-2 sm:px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition {{ $period === 'daily' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-indigo-600' }}"
                            >
                                Jour
                            </a>

                            <a
                                href="{{ route('vendor.dashboard', ['period' => 'monthly', 'year' => $selectedYear, 'month' => $selectedMonth]) }}"
                                class="flex items-center justify-center px-2 sm:px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition {{ $period === 'monthly' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-indigo-600' }}"
                            >
                                Mois
                            </a>

                            <a
                                href="{{ route('vendor.dashboard', ['period' => 'annual', 'year' => $selectedYear]) }}"
                                class="flex items-center justify-center px-2 sm:px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition {{ $period === 'annual' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-indigo-600' }}"
                            >
                                Année
                            </a>

                        </div>


                        {{-- FORMULAIRE FILTRE --}}
                        <form
                            method="GET"
                            action="{{ route('vendor.dashboard') }}"
                            class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap items-end gap-3 mt-4"
                        >

                            <input
                                type="hidden"
                                name="period"
                                value="{{ $period }}"
                            >

                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">
                                    Année
                                </label>

                                <select
                                    name="year"
                                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-white outline-none focus:ring-2 focus:ring-indigo-500"
                                >

                                    @for($year = 20100; $year >= 2020; $year--)

                                        <option
                                            value="{{ $year }}"
                                            {{ $selectedYear == $year ? 'selected' : '' }}
                                        >
                                            {{ $year }}
                                        </option>

                                    @endfor

                                </select>

                            </div>


                            @if($period === 'daily' || $period === 'monthly')

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">
                                        Mois
                                    </label>

                                    <select
                                        name="month"
                                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-white outline-none focus:ring-2 focus:ring-indigo-500"
                                    >

                                        @php
                                            $months = [
                                                1 => 'Janvier',
                                                2 => 'Février',
                                                3 => 'Mars',
                                                4 => 'Avril',
                                                5 => 'Mai',
                                                6 => 'Juin',
                                                7 => 'Juillet',
                                                8 => 'Août',
                                                9 => 'Septembre',
                                                10 => 'Octobre',
                                                11 => 'Novembre',
                                                12 => 'Décembre',
                                            ];
                                        @endphp

                                        @foreach($months as $number => $name)

                                            <option
                                                value="{{ $number }}"
                                                {{ $selectedMonth == $number ? 'selected' : '' }}
                                            >
                                                {{ $name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                            @endif


                            @if($period === 'daily')

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">
                                        Jour
                                    </label>

                                    <select
                                        name="day"
                                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-white outline-none focus:ring-2 focus:ring-indigo-500"
                                    >

                                        @for($day = 1; $day <= $daysInMonth; $day++)

                                            <option
                                                value="{{ $day }}"
                                                {{ $selectedDay == $day ? 'selected' : '' }}
                                            >
                                                {{ $day }}
                                            </option>

                                        @endfor

                                    </select>

                                </div>

                            @endif


                            <button
                                type="submit"
                                class="w-full lg:w-auto bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition"
                            >
                                Afficher
                            </button>

                        </form>

                    </div>

                </div>

            </div>


            {{-- MONTANT --}}
            <div class="p-5 sm:p-7 text-center">

                @php
                    $monthNames = [
                        1 => 'Janvier',
                        2 => 'Février',
                        3 => 'Mars',
                        4 => 'Avril',
                        5 => 'Mai',
                        6 => 'Juin',
                        7 => 'Juillet',
                        8 => 'Août',
                        9 => 'Septembre',
                        10 => 'Octobre',
                        11 => 'Novembre',
                        12 => 'Décembre',
                    ];
                @endphp

                @if($period === 'daily')

                    <p class="text-slate-500">
                        Revenu du {{ $selectedDay }}
                        {{ $monthNames[$selectedMonth] }}
                        {{ $selectedYear }}
                    </p>

                @elseif($period === 'monthly')

                    <p class="text-slate-500">
                        Revenu de {{ $monthNames[$selectedMonth] }}
                        {{ $selectedYear }}
                    </p>

                @else

                    <p class="text-slate-500">
                        Revenu de l'année {{ $selectedYear }}
                    </p>

                @endif


                <p class="text-4xl sm:text-5xl font-extrabold text-indigo-600 mt-3 break-words">
                    {{ number_format($revenue, 0, ',', ' ') }}
                    <span class="text-xl sm:text-2xl">
                        FCFA
                    </span>
                </p>

                <p class="text-sm text-slate-500 mt-3">

                    @if($period === 'daily')
                        📅 Journée sélectionnée
                    @elseif($period === 'monthly')
                        📅 Mois sélectionné
                    @else
                        📅 Année sélectionnée
                    @endif

                </p>

            </div>

        </div>


        {{-- GESTION --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-6">

            <a
                href="{{ route('vendor.products.index') }}"
                class="group bg-gradient-to-br from-indigo-600 to-violet-700 text-white rounded-3xl p-6 sm:p-7 hover:shadow-xl hover:-translate-y-0.5 transition"
            >

                <div class="w-14 h-14 rounded-2xl bg-white/15 flex items-center justify-center text-3xl">
                    🛍️
                </div>

                <h2 class="font-extrabold text-xl mt-5">
                    Gérer mes produits
                </h2>

                <p class="mt-2 text-indigo-100 leading-6">
                    Ajouter, modifier ou supprimer vos produits.
                </p>

                <div class="mt-5 font-bold">
                    Accéder → 
                </div>

            </a>


            <a
                href="{{ route('vendor.orders.index') }}"
                class="group bg-slate-900 text-white rounded-3xl p-6 sm:p-7 hover:bg-slate-800 hover:shadow-xl hover:-translate-y-0.5 transition"
            >

                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-3xl">
                    📦
                </div>

                <h2 class="font-extrabold text-xl mt-5">
                    Mes commandes
                </h2>

                <p class="mt-2 text-slate-300 leading-6">
                    Consultez les commandes et vérifiez les paiements.
                </p>

                <div class="mt-5 font-bold">
                    Voir les commandes →
                </div>

            </a>

        </div>

    @endif

</div>

@endsection
