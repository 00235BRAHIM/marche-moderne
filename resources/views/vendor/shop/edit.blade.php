@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- EN-TÊTE --}}
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-center">
                <div class="w-full md:w-auto">
                    <p class="text-sm font-semibold text-indigo-600 mb-1">
                        ESPACE VENDEUR
                    </p>

                    <h1 class="text-3xl font-black text-slate-900 text-center">
                        Ma boutique
                    </h1>

                    <p class="mt-2 text-slate-500 text-center">
                        Configurez votre boutique, vos coordonnées et vos moyens de paiement.
                    </p>
                </div>

                <a
                    href="{{ route('vendor.dashboard') }}"
                    class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-100 transition w-full md:w-auto"
                >
                    ← Retour au dashboard
                </a>
            </div>
        </div>

        {{-- MESSAGE --}}
        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-800">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- ERREURS --}}
        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800">
                <p class="font-bold mb-2">Veuillez corriger les erreurs :</p>
                <ul class="list-disc ml-5 space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('vendor.shop.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf

            {{-- INFORMATIONS PRINCIPALES --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h2 class="text-xl font-bold text-slate-900">
                        🏪 Informations de la boutique
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Ces informations seront visibles par vos clients.
                    </p>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Nom de la boutique *
                        </label>

                        <input
                            type="text"
                            name="shop_name"
                            value="{{ old('shop_name', $vendor->shop_name) }}"
                            required
                            placeholder="Exemple : Boutique Ibrahim"
                            class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Nom du vendeur *
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $vendor->name) }}"
                            required
                            class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Numéro de téléphone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $vendor->phone) }}"
                            placeholder="+235 ..."
                            class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Identifiant public de la boutique *
                        </label>

                        <div class="flex rounded-xl overflow-hidden border border-slate-300">
                            <span class="bg-slate-100 px-4 flex items-center text-sm text-slate-500">
                                /shop/
                            </span>

                            <input
                                type="text"
                                name="shop_slug"
                                value="{{ old('shop_slug', $vendor->shop_slug) }}"
                                required
                                placeholder="boutique-ibrahim"
                                class="flex-1 border-0 focus:ring-0"
                                pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
                            >
                        </div>

                        <p class="mt-2 text-xs text-slate-500">
                            Utilisez uniquement des lettres minuscules, chiffres et tirets.
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Description de la boutique
                        </label>

                        <textarea
                            name="shop_description"
                            rows="5"
                            maxlength="2000"
                            placeholder="Présentez votre boutique, vos produits et vos services..."
                            class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('shop_description', $vendor->shop_description) }}</textarea>
                    </div>

                </div>
            </div>

            {{-- LOGO + CERTIFICATION --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-xl font-bold text-slate-900">
                        🖼️ Logo de la boutique
                    </h2>

                    <div class="mt-5 flex flex-col sm:flex-row items-center gap-5">

                        <div class="w-28 h-28 rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center">
                            @if($vendor->shop_logo)
                                <img
                                    src="{{ asset('storage/' . $vendor->shop_logo) }}"
                                    alt="Logo {{ $vendor->shop_name }}"
                                    class="w-full h-full object-cover"
                                >
                            @else
                                <span class="text-4xl">🏪</span>
                            @endif
                        </div>

                        <div class="flex-1">
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Choisir un nouveau logo
                            </label>

                            <input
                                type="file"
                                name="shop_logo"
                                accept=".jpg,.jpeg,.png,.webp"
                                class="w-full text-sm"
                            >

                            <p class="mt-2 text-xs text-slate-500">
                                JPG, PNG ou WEBP — maximum 2 Mo.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-xl font-bold text-slate-900">
                        🛡️ Certification
                    </h2>

                    <div class="mt-5 rounded-2xl p-5
                        {{ $vendor->isCertifiedVendor()
                            ? 'bg-green-50 border border-green-200'
                            : 'bg-amber-50 border border-amber-200' }}">

                        @if($vendor->isCertifiedVendor())
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-2xl">
                                    ✓
                                </div>

                                <div>
                                    <p class="font-bold text-green-800">
                                        Vendeur certifié
                                    </p>

                                    <p class="text-sm text-green-700">
                                        Votre boutique bénéficie du badge de certification.
                                    </p>
                                </div>
                            </div>

                            @if($vendor->certification_number)
                                <p class="mt-4 text-sm text-green-800">
                                    <strong>Numéro :</strong>
                                    {{ $vendor->certification_number }}
                                </p>
                            @endif
                        @else
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-2xl">
                                    !
                                </div>

                                <div>
                                    <p class="font-bold text-amber-800">
                                        Boutique non certifiée
                                    </p>

                                    <p class="text-sm text-amber-700">
                                        Soumettez votre dossier de certification depuis votre espace vendeur.
                                    </p>
                                </div>
                            </div>

                            <a
                                href="{{ route('vendor.certification') }}"
                                class="inline-flex mt-4 px-4 py-2 rounded-xl bg-amber-600 text-white font-semibold hover:bg-amber-700"
                            >
                                Demander la certification
                            </a>
                        @endif
                    </div>
                </div>

            </div>

            {{-- PAIEMENTS --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h2 class="text-xl font-bold text-slate-900">
                        💳 Moyens de paiement
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Les clients utiliseront ces numéros pour payer leurs commandes.
                    </p>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    @php
                        $airtel = $paymentMethods['airtel_money'] ?? null;
                        $moov = $paymentMethods['moov_money'] ?? null;
                    @endphp

                    <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                        <label class="block font-bold text-red-800 mb-2">
                            🔴 Airtel Money
                        </label>

                        <input
                            type="text"
                            name="airtel_number"
                            value="{{ old('airtel_number', $airtel?->transfer_number) }}"
                            placeholder="+235 60 XX XX XX"
                            class="w-full rounded-xl border-red-200 focus:border-red-500 focus:ring-red-500"
                        >

                        <p class="mt-2 text-xs text-red-700">
                            Numéro Airtel Money destiné aux paiements des clients.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-green-200 bg-green-50 p-5">
                        <label class="block font-bold text-green-800 mb-2">
                            🟢 Moov Money
                        </label>

                        <input
                            type="text"
                            name="moov_number"
                            value="{{ old('moov_number', $moov?->transfer_number) }}"
                            placeholder="+235 99 XX XX XX"
                            class="w-full rounded-xl border-green-200 focus:border-green-500 focus:ring-green-500"
                        >

                        <p class="mt-2 text-xs text-green-700">
                            Numéro Moov Money destiné aux paiements des clients.
                        </p>
                    </div>

                </div>
            </div>

            {{-- RÉSEAUX SOCIAUX --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h2 class="text-xl font-bold text-slate-900">
                        🌐 Réseaux sociaux
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Ajoutez vos liens pour permettre aux clients de vous contacter.
                    </p>
                </div>

                <div class="p-6 grid grid-cols-1 gap-5">

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            📘 Facebook
                        </label>

                        <input
                            type="url"
                            name="facebook_url"
                            value="{{ old('facebook_url', $vendor->facebook_url) }}"
                            placeholder="https://facebook.com/..."
                            class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            ▶️ YouTube
                        </label>

                        <input
                            type="url"
                            name="youtube_url"
                            value="{{ old('youtube_url', $vendor->youtube_url) }}"
                            placeholder="https://youtube.com/..."
                            class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            💬 WhatsApp
                        </label>

                        <input
                            type="url"
                            name="whatsapp_url"
                            value="{{ old('whatsapp_url', $vendor->whatsapp_url) }}"
                            placeholder="https://wa.me/235..."
                            class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                </div>
            </div>

            {{-- LIEN PUBLIC --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-3xl p-6">
                <h2 class="text-xl font-bold text-indigo-900">
                    🔗 Lien public de votre boutique
                </h2>

                @if($vendor->shop_slug)
                    <div class="mt-4 flex flex-col md:flex-row gap-3">
                        <input
                            id="shopPublicLink"
                            type="text"
                            readonly
                            value="{{ url('/shop/' . $vendor->shop_slug) }}"
                            class="flex-1 rounded-xl border-indigo-200 bg-white"
                        >

                        <button
                            type="button"
                            onclick="copyShopLink()"
                            class="px-5 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700"
                        >
                            📋 Copier
                        </button>

                        <button
                            type="button"
                            onclick="shareShop()"
                            class="px-5 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800"
                        >
                            📤 Partager
                        </button>
                    </div>
                @else
                    <p class="mt-3 text-sm text-indigo-700">
                        Enregistrez votre boutique pour générer votre lien public.
                    </p>
                @endif
            </div>

            {{-- ENREGISTRER --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-7 py-4 rounded-2xl bg-indigo-600 text-white font-black hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition"
                >
                    💾 Enregistrer ma boutique
                </button>
            </div>

        </form>
    </div>
</div>

<script>
function copyShopLink() {
    const input = document.getElementById('shopPublicLink');

    if (!input) return;

    navigator.clipboard.writeText(input.value).then(() => {
        alert('Lien de la boutique copié.');
    });
}

async function shareShop() {
    const input = document.getElementById('shopPublicLink');

    if (!input) return;

    const url = input.value;

    if (navigator.share) {
        await navigator.share({
            title: '{{ addslashes($vendor->shop_name ?: "Ma boutique") }}',
            text: 'Découvrez ma boutique sur Souk Kabir.',
            url: url
        });
    } else {
        await navigator.clipboard.writeText(url);
        alert('Le lien a été copié. Vous pouvez maintenant le partager.');
    }
}
</script>
@endsection
