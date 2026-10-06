@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
<div class="mb-6 text-center">
<a href="{{ route('vendor.dashboard') }}"
class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-indigo-100 hover:text-indigo-700 transition">
← Retour au dashboard
</a>
</div><h1 class="text-3xl font-bold text-center">Mes numéros de transfert</h1><p class="text-gray-600 mt-2 text-center">Ces numéros seront affichés aux clients pour payer vos produits. Vous pouvez les modifier à tout moment.</p>
@if(session('success'))<div class="mt-4 p-3 rounded bg-green-100 text-green-800">{{session('success')}}</div>@endif
@if($errors->any())<div class="mt-4 p-3 rounded bg-red-100 text-red-800">{{$errors->first()}}</div>@endif
<form method="POST" action="{{route('vendor.payment-settings.save')}}" class="mt-7 bg-white border rounded-2xl p-6 space-y-6">@csrf
<div><h2 class="font-semibold text-lg">Airtel Money</h2><div class="grid md:grid-cols-2 gap-4 mt-3"><input name="airtel_name" required value="{{old('airtel_name', optional($methods->firstWhere('provider','airtel_money'))->account_name)}}" placeholder="Nom du titulaire" class="border rounded-xl p-3"><input name="airtel_number" required value="{{old('airtel_number', optional($methods->firstWhere('provider','airtel_money'))->transfer_number)}}" placeholder="Numéro de transfert" class="border rounded-xl p-3"></div></div>
<div><h2 class="font-semibold text-lg">Moov Money</h2><div class="grid md:grid-cols-2 gap-4 mt-3"><input name="moov_name" required value="{{old('moov_name', optional($methods->firstWhere('provider','moov_money'))->account_name)}}" placeholder="Nom du titulaire" class="border rounded-xl p-3"><input name="moov_number" required value="{{old('moov_number', optional($methods->firstWhere('provider','moov_money'))->transfer_number)}}" placeholder="Numéro de transfert" class="border rounded-xl p-3"></div></div>
<button class="bg-slate-950 text-white rounded-xl px-6 py-3 font-semibold mx-auto block">Enregistrer</button></form></div>
@endsection
