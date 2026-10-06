<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class VendorAccessMiddleware {
    public function handle(Request $request, Closure $next) {
        $user=$request->user();
        abort_unless($user && $user->role==='vendor',403);
        if (!$user->isCertifiedVendor()) return redirect()->route('vendor.certification')->with('warning','Votre compte vendeur doit être certifié par l’administrateur avant de vendre.');
        if (!$user->hasActiveSubscription()) return redirect()->route('vendor.subscriptions')->with('warning','Votre abonnement vendeur est absent ou expiré. Abonnez-vous pour accéder à votre espace de vente.');
        return $next($request);
    }
}
