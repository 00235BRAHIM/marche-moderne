@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <p class="text-indigo-600 font-bold">Administration / Plans</p>

            <h1 class="text-3xl font-extrabold">
                Plans d’abonnement vendeur
            </h1>
        </div>

        <a
            href="{{ route('admin.dashboard') }}"
            class="inline-flex items-center justify-center bg-slate-950 text-white px-5 py-3 rounded-xl font-bold hover:bg-slate-800 transition"
        >
            ← Retour au tableau de bord
        </a>

    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-100 border border-green-200 text-green-800 px-5 py-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-xl bg-red-100 border border-red-200 text-red-800 px-5 py-4">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-100 border border-red-200 text-red-800 px-5 py-4">
            {{ $errors->first() }}
        </div>
    @endif


    <div class="grid md:grid-cols-3 gap-6">

        {{-- CREER UN PLAN --}}
        <div class="bg-white border rounded-2xl p-6">

            <h2 class="font-bold text-xl mb-5">
                Créer un plan
            </h2>

            <form
                method="POST"
                action="{{ route('admin.plans.store') }}"
                class="space-y-3"
            >

                @csrf

                <input
                    name="name"
                    required
                    placeholder="Nom du plan"
                    class="w-full border rounded-xl px-3 py-3"
                >

                <textarea
                    name="description"
                    placeholder="Description"
                    class="w-full border rounded-xl px-3 py-3"
                ></textarea>

                <input
                    name="price"
                    type="number"
                    min="0"
                    required
                    placeholder="Prix FCFA"
                    class="w-full border rounded-xl px-3 py-3"
                >

                <input
                    name="duration_days"
                    type="number"
                    min="1"
                    value="30"
                    required
                    placeholder="Durée en jours"
                    class="w-full border rounded-xl px-3 py-3"
                >

                <input
                    name="product_limit"
                    type="number"
                    min="1"
                    placeholder="Limite produits (optionnel)"
                    class="w-full border rounded-xl px-3 py-3"
                >

                <button
                    type="submit"
                    class="w-full bg-indigo-600 text-white rounded-xl py-3 font-bold hover:bg-indigo-700"
                >
                    + Créer le plan
                </button>

            </form>

        </div>


        {{-- LISTE DES PLANS --}}
        <div class="md:col-span-2 grid sm:grid-cols-2 gap-5">

            @foreach($plans as $plan)

                <div class="bg-white border rounded-2xl p-6 shadow-sm">

                    <div class="flex items-start justify-between gap-4">

                        <div>
                            <h3 class="font-bold text-xl">
                                {{ $plan->name }}
                            </h3>

                            @if($plan->is_active)
                                <span class="inline-block mt-2 text-xs font-bold bg-green-100 text-green-700 px-3 py-1 rounded-full">
                                    Actif
                                </span>
                            @else
                                <span class="inline-block mt-2 text-xs font-bold bg-red-100 text-red-700 px-3 py-1 rounded-full">
                                    Désactivé
                                </span>
                            @endif
                        </div>

                        <span class="text-indigo-600 font-extrabold whitespace-nowrap">
                            {{ number_format($plan->price, 0, ',', ' ') }} FCFA
                        </span>

                    </div>

                    <p class="text-slate-500 my-4">
                        {{ $plan->description ?: 'Aucune description.' }}
                    </p>

                    <p>
                        Durée :
                        <b>{{ $plan->duration_days }} jours</b>
                    </p>

                    <p>
                        Produits :
                        <b>{{ $plan->product_limit ?? 'Illimité' }}</b>
                    </p>


                    {{-- ACTIONS --}}
                    <div class="flex flex-wrap gap-2 mt-5">

                        {{-- MODIFIER --}}
                        <a
                            href="{{ route('admin.plans.edit', $plan) }}"
                            class="inline-flex items-center justify-center bg-indigo-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-indigo-700"
                        >
                            ✏️ Modifier
                        </a>


                        {{-- ACTIVER / DESACTIVER --}}
                        <form
                            method="POST"
                            action="{{ route('admin.plans.toggle', $plan) }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="border border-slate-300 px-4 py-2 rounded-xl font-semibold hover:bg-slate-50"
                            >
                                {{ $plan->is_active ? 'Désactiver' : 'Activer' }}
                            </button>

                        </form>


                        {{-- SUPPRIMER --}}
                        <form
                            method="POST"
                            action="{{ route('admin.plans.delete', $plan) }}"
                            onsubmit="return confirm('Voulez-vous vraiment supprimer ce plan ? Cette action est définitive.');"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-red-700"
                            >
                                🗑️ Supprimer
                            </button>

                        </form>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>

@endsection
