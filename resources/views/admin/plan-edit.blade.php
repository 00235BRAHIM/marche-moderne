@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto px-4 py-10">

    <div class="mb-6">

        <p class="text-indigo-600 font-bold">
            Administration / Plans
        </p>

        <h1 class="text-3xl font-extrabold mt-1">
            Modifier le plan
        </h1>

    </div>


    <div class="bg-white border rounded-3xl p-6 md:p-8">

        @if($errors->any())

            <div class="mb-6 p-4 rounded-xl bg-red-100 text-red-800">

                @foreach($errors->all() as $error)

                    <p>{{ $error }}</p>

                @endforeach

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('admin.plans.update', $plan) }}"
            class="space-y-5">

            @csrf
            @method('PUT')


            <div>

                <label class="block font-bold mb-2">
                    Nom du plan
                </label>

                <input
                    name="name"
                    value="{{ old('name', $plan->name) }}"
                    required
                    class="w-full border rounded-xl px-4 py-3"
                >

            </div>


            <div>

                <label class="block font-bold mb-2">
                    Description
                </label>

                <textarea
                    name="description"
                    rows="4"
                    class="w-full border rounded-xl px-4 py-3"
                >{{ old('description', $plan->description) }}</textarea>

            </div>


            <div class="grid md:grid-cols-2 gap-4">

                <div>

                    <label class="block font-bold mb-2">
                        Prix FCFA
                    </label>

                    <input
                        name="price"
                        type="number"
                        min="0"
                        value="{{ old('price', $plan->price) }}"
                        required
                        class="w-full border rounded-xl px-4 py-3"
                    >

                </div>


                <div>

                    <label class="block font-bold mb-2">
                        Durée en jours
                    </label>

                    <input
                        name="duration_days"
                        type="number"
                        min="1"
                        value="{{ old('duration_days', $plan->duration_days) }}"
                        required
                        class="w-full border rounded-xl px-4 py-3"
                    >

                </div>

            </div>


            <div>

                <label class="block font-bold mb-2">
                    Limite de produits
                </label>

                <input
                    name="product_limit"
                    type="number"
                    min="1"
                    value="{{ old('product_limit', $plan->product_limit) }}"
                    placeholder="Laisser vide = illimité"
                    class="w-full border rounded-xl px-4 py-3"
                >

            </div>


            <div class="flex flex-wrap gap-3 pt-3">

                <a
                    href="{{ route('admin.plans') }}"
                    class="border px-5 py-3 rounded-xl font-bold hover:bg-slate-50">

                    ← Retour aux plans

                </a>


                <button
                    type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700">

                    💾 Enregistrer les modifications

                </button>

            </div>

        </form>

    </div>

</div>

@endsection
