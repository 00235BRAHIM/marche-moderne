@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="mb-8">

        <div class="text-center">

            <p class="text-indigo-600 font-bold">
                Administration / Vendeurs
            </p>

            <h1 class="text-3xl font-extrabold">
                Certification des vendeurs
            </h1>

            <p class="text-slate-500 mt-2">
                Vérifiez l'identité et les documents du vendeur avant de valider sa certification.
            </p>

            <div class="mt-4 flex justify-start">
            </div>

        </div>

    </div>

    <div class="bg-white border rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('admin.vendors') }}" class="flex flex-col md:flex-row gap-3">

        <div class="flex-1">
            <input
                type="text"
                name="q"
                value="{{ $search ?? '' }}"
                placeholder="Rechercher un vendeur, email, téléphone, certification ou boutique..."
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
                href="{{ route('admin.vendors') }}"
                class="bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold px-6 py-3 rounded-xl text-center"
            >
                Réinitialiser
            </a>
        @endif

    </form>

    @if(!empty($search))
        <p class="text-sm text-slate-500 mt-3">
            Résultats pour :
            <strong>{{ $search }}</strong>
        </p>
    @endif
</div>

<div class="bg-white border rounded-2xl overflow-hidden">

        <div class="overflow-x-auto">

            
<div class="flex justify-start mb-6">
    <a href="{{ route('admin.dashboard') }}"
       class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
        ← Retour au tableau de bord
    </a>
</div>

<table class="w-full text-sm">

                <thead class="bg-slate-50">
                    <tr>

                        <th class="p-4 text-left">
                            Vendeur
                        </th>

                        <th class="p-4 text-center">
                            NNI
                        </th>

                        <th class="p-4 text-center">
                            Carte nationale
                        </th>

                        <th class="p-4 text-center">
                            Statut
                        </th>

                        <th class="p-4 text-center">
                            Certification
                        </th>

                        <th class="p-4 text-center">
                            Produits
                        </th>

                        <th class="p-4 text-center">
                            Abonnement
                        </th>

                        <th class="p-4 text-center">
                            Action
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @foreach($vendors as $vendor)

                    <tr class="border-t">

                        {{-- VENDEUR --}}
                        <td class="p-4">

                            <b class="block">
                                {{ $vendor->name }}
                            </b>

                            <div class="text-slate-500">
                                {{ $vendor->email }}
                            </div>

                            @if($vendor->phone)
                                <div class="text-slate-400 text-xs mt-1">
                                    📞 {{ $vendor->phone }}
                                </div>
                            @endif

                        </td>

                        {{-- NNI --}}
                        <td class="p-4 text-center">

                            @if($vendor->nni)

                                <span class="font-mono font-bold text-slate-700">
                                    {{ $vendor->nni }}
                                </span>

                            @else

                                <span class="text-red-500 font-semibold">
                                    Non fourni
                                </span>

                            @endif

                        </td>

                        {{-- DOCUMENT --}}
                        <td class="p-4 text-center">

                            @if($vendor->certification_document)

                                <a
                                    href="{{ asset('storage/' . $vendor->certification_document) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 bg-indigo-600 text-white px-3 py-2 rounded-lg font-semibold hover:bg-indigo-700">

                                    👁 Voir

                                </a>

                            @else

                                <span class="text-red-500 font-semibold">
                                    Non fourni
                                </span>

                            @endif

                        </td>

                        {{-- STATUT --}}
                        <td class="p-4 text-center">

                            @if($vendor->vendor_status === 'approved')

                                <span class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                                    Approuvé
                                </span>

                            @elseif($vendor->vendor_status === 'suspended')

                                <span class="inline-flex px-3 py-1 rounded-full bg-red-100 text-red-700 font-semibold">
                                    Suspendu
                                </span>

                            @else

                                <span class="inline-flex px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">
                                    En attente
                                </span>

                            @endif

                        </td>

                        {{-- CERTIFICATION --}}
                        <td class="p-4 text-center">

                            @if($vendor->is_certified)

                                <span class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                                    ✓ Certifié
                                </span>

                                @if($vendor->certification_number)
                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $vendor->certification_number }}
                                    </div>
                                @endif

                            @else

                                <span class="text-slate-500">
                                    Non certifié
                                </span>

                            @endif

                        </td>

                        {{-- PRODUITS --}}
                        <td class="p-4 text-center">
                            {{ $vendor->products_count }}
                        </td>

                        {{-- ABONNEMENT --}}
                        <td class="p-4 text-center">
                            {{ $vendor->activeSubscription?->plan?->name ?? 'Aucun' }}
                        </td>

                        {{-- ACTION --}}
                        <td class="p-4">

                            <form
                                method="POST"
                                action="{{ route('admin.vendors.certify', $vendor) }}"
                                class="flex gap-2 flex-wrap justify-center">

                                @csrf

                                <input
                                    name="certification_number"
                                    placeholder="N° certificat"
                                    value="{{ old('certification_number') }}"
                                    class="border rounded-lg px-2 py-2 w-32"
                                >

                                <button
                                    name="action"
                                    value="approve"
                                    type="submit"
                                    class="bg-emerald-600 text-white px-3 py-2 rounded-lg font-semibold hover:bg-emerald-700"
                                    @if(!$vendor->nni || !$vendor->certification_document) disabled @endif>

                                    ✓ Certifier

                                </button>

                                <button
                                    name="action"
                                    value="suspend"
                                    type="submit"
                                    class="bg-red-600 text-white px-3 py-2 rounded-lg font-semibold hover:bg-red-700">

                                    Suspendre

                                </button>

                            </form>

                            @if(!$vendor->nni || !$vendor->certification_document)

                                <p class="text-xs text-red-500 mt-2 text-center">
                                    NNI et carte nationale requis pour certifier.
                                </p>

                            @endif

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

    <div class="mt-4">
        {{ $vendors->links() }}
    </div>

</div>
@endsection
