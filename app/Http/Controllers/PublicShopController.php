<?php

namespace App\Http\Controllers;

use App\Models\User;

class PublicShopController extends Controller
{
    public function index()
    {
        // On quitte le contexte d'une boutique précise.
        session()->forget([
            'public_shop_slug',
            'public_shop_name',
        ]);

        /*
        |--------------------------------------------------------------------------
        | BOUTIQUES VISIBLES PUBLIQUEMENT
        |--------------------------------------------------------------------------
        |
        | Une boutique est visible uniquement si le vendeur :
        | - est approuvé
        | - est certifié
        | - possède un abonnement actif
        |
        */

        $shops = User::query()
            ->where('role', 'vendor')
            ->where('vendor_status', 'approved')
            ->where('is_certified', true)
            ->whereNotNull('shop_slug')
            ->where('shop_slug', '!=', '')
            ->where(function ($q) {
                $q->whereNotNull('shop_name')
                    ->orWhereNotNull('name');
            })
            ->whereHas('subscriptions', function ($q) {
                $q->active();
            })
            ->withCount([
                'products' => function ($q) {
                    $q->where('is_active', true)
                        ->where('is_archived', false);
                },
            ])
            ->latest()
            ->paginate(12);

        return view('shop.index', compact('shops'));
    }

    public function show(string $slug)
    {
        /*
        |--------------------------------------------------------------------------
        | RECHERCHE DU VENDEUR
        |--------------------------------------------------------------------------
        */

        $vendor = User::query()
            ->where('role', 'vendor')
            ->where('shop_slug', $slug)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | VÉRIFICATION CERTIFICATION + ABONNEMENT
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $vendor->isCertifiedVendor()
            && $vendor->hasActiveSubscription(),
            404
        );

        /*
        |--------------------------------------------------------------------------
        | SESSION BOUTIQUE
        |--------------------------------------------------------------------------
        */

        session([
            'public_shop_slug' => $vendor->shop_slug,
            'public_shop_name' => $vendor->shop_name ?: $vendor->name,
        ]);

        /*
        |--------------------------------------------------------------------------
        | PRODUITS VISIBLES
        |--------------------------------------------------------------------------
        */

        $products = $vendor->products()
            ->where('is_active', true)
            ->where('is_archived', false)
            ->latest()
            ->paginate(12);

        /*
        |--------------------------------------------------------------------------
        | MOYENS DE PAIEMENT
        |--------------------------------------------------------------------------
        */

        $paymentMethods = $vendor->paymentMethods()
            ->where('is_active', true)
            ->get()
            ->keyBy('provider');

        return view('shop.public', compact(
            'vendor',
            'products',
            'paymentMethods'
        ));
    }
}