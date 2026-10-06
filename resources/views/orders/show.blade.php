@extends('layouts.app') @section('content')<div class="max-w-5xl mx-auto px-4 py-10"><div class="flex justify-between"><h1 class="text-3xl font-extrabold">{{ $order->order_number }}</h1><span class="bg-slate-100 px-3 py-2 rounded-xl">{{ $order->status }} — paiement {{ $order->payment_status }}</span></div><div class="bg-white border rounded-2xl mt-7 p-6"><h2 class="text-xl font-bold mb-4">Résumé global</h2>@foreach($order->vendorOrders as $vo)<div class="border rounded-xl p-4 mb-4"><div class="flex justify-between"><div><b>{{$vo->vendor->name}}</b><div class="text-sm text-gray-500">{{$vo->order_number}}</div></div><div class="text-right"><b>{{number_format($vo->total,0,',',' ')}} FCFA</b><div class="text-sm">Paiement : {{$vo->payment_status}}</div></div></div>@foreach($vo->items as $i)
@php
    $productImage = $i->product?->image
        ? asset('storage/' . $i->product->image)
        : asset('images/product-placeholder.png');
@endphp

<div class="flex items-center gap-4 py-4 border-t mt-2">

    <div class="w-20 h-20 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
        <img
            src="{{ $productImage }}"
            alt="{{ $i->product_name }}"
            class="w-full h-full object-cover"
            onerror="this.src='https://placehold.co/160x160?text=Produit';"
        >
    </div>

    <div class="flex-1 min-w-0">
        <p class="font-bold text-slate-900">
            {{ $i->product_name }}
        </p>

        <p class="text-sm text-slate-500 mt-1">
            Quantité : {{ $i->quantity }}
        </p>
    </div>

    <div class="text-right">
        <p class="font-extrabold text-slate-900">
            {{ number_format($i->total,0,',',' ') }} FCFA
        </p>

        @if($i->quantity > 1)
            <p class="text-xs text-slate-500 mt-1">
                {{ number_format($i->unit_price,0,',',' ') }} FCFA / unité
            </p>
        @endif
    </div>

</div>
@endforeach</div>@endforeach<div class="flex justify-between text-xl font-extrabold mt-5"><span>Total global</span><span>{{ number_format($order->total,0,',',' ') }} FCFA</span></div><p class="text-sm text-slate-500 mt-5">Livraison : {{ $order->shipping_name }} — {{ $order->shipping_phone }}<br>{{ $order->shipping_address }}</p></div>@if($order->payment_status!=='paid')<a href="{{route('order.payment',$order)}}" class="inline-block mt-5 bg-slate-950 text-white px-5 py-3 rounded-xl">Payer vendeur par vendeur</a>@endif</div></div>@endsection
