<?php

namespace App\Http\Controllers;

use App\Support\AdminNotifier;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function login(Request $r)
    {
        $d = $r->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($d, $r->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->withInput();
        }

        $r->session()->regenerate();

        $shop = $r->input('shop');
        $buy = $r->input('buy');

        /*
         * Si le client vient d'une boutique publique,
         * on mémorise cette boutique dans la session.
         */
        if ($shop) {
            $vendor = User::query()
                ->where('role', 'vendor')
                ->where('shop_slug', $shop)
                ->first();

            if ($vendor) {
                session([
                    'public_shop_slug' => $vendor->shop_slug,
                ]);
            }
        }

        /*
         * Si le client avait choisi un produit avant de se connecter,
         * on vérifie que ce produit appartient bien à la boutique.
         */
        if ($shop && $buy) {

            $product = Product::query()
                ->where('id', $buy)
                ->where('stock', '>', 0)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->whereHas('vendor', function ($q) use ($shop) {
                    $q->where('role', 'vendor')
                      ->where('shop_slug', $shop);
                })
                ->first();

            if ($product) {

                $cart = auth()->user()
                    ->cart()
                    ->firstOrCreate();

                $item = $cart->items()->firstOrNew([
                    'product_id' => $product->id,
                ]);

                $item->quantity = min(
                    $product->stock,
                    ($item->quantity ?? 0) + 1
                );

                $item->unit_price = $product->price;
                $item->save();

                return redirect()
                    ->route('cart')
                    ->with(
                        'success',
                        'Produit ajouté au panier.'
                    );
            }

            return redirect()
                ->route('shop.public', $shop)
                ->withErrors([
                    'product' => 'Ce produit n’est pas disponible dans cette boutique.'
                ]);
        }

        /*
         * Connexion depuis une boutique sans produit sélectionné.
         */
        if ($shop) {
            return redirect()
                ->route('shop.public', $shop);
        }

        /*
         * Connexion normale.
         */
        return redirect()
            ->intended('/dashboard');
    }

    public function register(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|max:120',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|max:30',
            'password' => 'required|min:8|confirmed',
            'role' => 'nullable|in:customer,vendor',
        ]);

        $u = User::create($d);

        if ($u->role === 'vendor') {
            AdminNotifier::send(
                'Nouveau vendeur inscrit',
                $u->name . ' vient de créer un compte vendeur.',
                '👤',
                route('admin.vendors')
            );
        }

        Auth::login($u);

        return redirect('/dashboard');
    }

    public function forgotPasswordForm()
    {
        return view("auth.forgot-password");
    }

    public function sendResetLink(Request $r)
    {
        $r->validate(["email" => "required|email"]);

        $status = Password::sendResetLink($r->only("email"));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with("status", __($status));
        }

        return back()->withErrors(["email" => __($status)]);
    }

    public function resetPasswordForm(Request $r, string $token)
    {
        return view("auth.reset-password", [
            "token" => $token,
            "email" => $r->query("email"),
        ]);
    }

    public function resetPassword(Request $r)
    {
        $data = $r->validate([
            "token" => "required",
            "email" => "required|email",
            "password" => "required|min:8|confirmed",
        ]);

        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill([
                "password" => $password,
                "remember_token" => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route("login")->with("success", "Votre mot de passe a été réinitialisé avec succès.");
        }

        return back()->withInput($r->only("email"))->withErrors([
            "email" => __($status),
        ]);
    }

    public function logout(Request $r)
    {
        // Conserve la boutique publique avant de détruire la session.
        $shopSlug = $r->session()->get('public_shop_slug');

        Auth::logout();

        $r->session()->invalidate();
        $r->session()->regenerateToken();

        // Si le client venait d'une boutique publique,
        // retour direct vers cette boutique après déconnexion.
        if ($shopSlug) {
            return redirect()->route('shop.public', $shopSlug);
        }

        // Déconnexion normale.
        return redirect('/');
    }
}
