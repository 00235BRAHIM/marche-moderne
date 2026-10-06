<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function edit()
    {
        $vendor = auth()->user();

        $paymentMethods = $vendor->paymentMethods()
            ->whereIn('provider', ['airtel_money', 'moov_money'])
            ->get()
            ->keyBy('provider');

        return view('vendor.shop.edit', compact(
            'vendor',
            'paymentMethods'
        ));
    }

    public function update(Request $request)
    {
        $vendor = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:30',
            'shop_name' => 'required|string|max:150',
            'shop_slug' => [
                'required',
                'string',
                'max:120',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:users,shop_slug,' . $vendor->id,
            ],
            'shop_description' => 'nullable|string|max:2000',
            'facebook_url' => 'nullable|url|max:500',
            'youtube_url' => 'nullable|url|max:500',
            'whatsapp_url' => 'nullable|url|max:500',
            'shop_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'airtel_number' => 'nullable|string|max:30',
            'moov_number' => 'nullable|string|max:30',
        ]);

        if ($request->hasFile('shop_logo')) {
            $data['shop_logo'] = $request
                ->file('shop_logo')
                ->store('shops', 'public');
        }

        $vendor->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'shop_name' => $data['shop_name'],
            'shop_slug' => Str::slug($data['shop_slug']),
            'shop_description' => $data['shop_description'] ?? null,
            'facebook_url' => $data['facebook_url'] ?? null,
            'youtube_url' => $data['youtube_url'] ?? null,
            'whatsapp_url' => $data['whatsapp_url'] ?? null,
            'shop_logo' => $data['shop_logo'] ?? $vendor->shop_logo,
        ]);

        $methods = [
            'airtel_money' => $data['airtel_number'] ?? null,
            'moov_money' => $data['moov_number'] ?? null,
        ];

        foreach ($methods as $provider => $number) {
            if ($number) {
                VendorPaymentMethod::updateOrCreate(
                    [
                        'vendor_id' => $vendor->id,
                        'provider' => $provider,
                    ],
                    [
                        'account_name' => $vendor->name,
                        'transfer_number' => $number,
                        'is_active' => true,
                    ]
                );
            }
        }

        return back()->with(
            'success',
            'Les informations de votre boutique ont été enregistrées.'
        );
    }
}
