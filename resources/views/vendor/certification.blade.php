@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    <div class="bg-white border rounded-3xl p-8">

        <div class="mb-6 text-center">
            <a href="{{ route('vendor.dashboard') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-indigo-100 hover:text-indigo-700 transition">
                ← Retour au dashboard
            </a>
        </div>

        <p class="text-indigo-600 font-bold text-center">Espace vendeur</p>

        <h1 class="text-3xl font-extrabold mt-1 text-center">
            Certification vendeur
        </h1>

        <p class="text-slate-500 mt-3 text-center">
            Votre boutique doit être certifiée par l’administrateur
            avant de pouvoir publier et vendre des produits.
        </p>

        <div class="grid md:grid-cols-3 gap-3 my-7">

            <div class="border rounded-xl p-4">
                <b>1. Dossier</b>
                <p class="text-sm text-slate-500">
                    Envoyer les documents
                </p>
            </div>

            <div class="border rounded-xl p-4">
                <b>2. Vérification</b>
                <p class="text-sm text-slate-500">
                    Contrôle par admin
                </p>
            </div>

            <div class="border rounded-xl p-4">
                <b>3. Certification</b>
                <p class="text-sm text-slate-500">
                    Accès vendeur
                </p>
            </div>

        </div>

        <form method="POST"
              action="{{ route('vendor.certification.submit') }}"
              enctype="multipart/form-data"
              class="space-y-5">

            @csrf

            <div>
                <label class="block font-bold mb-2">
                    Numéro NNI *
                </label>

                <input
                    type="text"
                    name="nni"
                    value="{{ old('nni', auth()->user()->nni) }}"
                    placeholder="Entrez votre numéro NNI"
                    required
                    maxlength="50"
                    class="w-full border rounded-xl px-4 py-3"
                >

                @error('nni')
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block font-bold mb-2">
                    Téléphone
                </label>

                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', auth()->user()->phone) }}"
                    placeholder="Téléphone"
                    maxlength="30"
                    class="w-full border rounded-xl px-4 py-3"
                >

                @error('phone')
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="block font-bold mb-2">
                    Carte nationale d'identité *
                </label>

                <input
                    type="file"
                    name="certification_document"
                    required
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full border rounded-xl px-4 py-3"
                >

                <p class="text-sm text-slate-500 mt-2">
                    Formats acceptés : PDF, JPG, JPEG ou PNG.
                    Taille maximale : 4 Mo.
                </p>

                @error('certification_document')
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button
                type="submit"
                class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700">
                📤 Envoyer mon dossier
            </button>

        </form>

    </div>

</div>
@endsection
