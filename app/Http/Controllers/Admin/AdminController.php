<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;use App\Http\Controllers\Controller;
use App\Models\{User,SubscriptionPlan,VendorSubscription,Product,Order,ManualPaymentSubmission};
use Illuminate\Http\Request;
use App\Notifications\VendorSubscriptionApprovedNotification;
use Illuminate\Support\Str;
class AdminController extends Controller {
    public function dashboard(){

        $admin = auth()->user();

        $adminNotificationTypes = [
            \App\Notifications\AdminActivityNotification::class,
            \App\Notifications\VendorAdminActivityNotification::class,
        ];

        $notifications = $admin->unreadNotifications()
            ->whereIn("type", $adminNotificationTypes)
            ->latest()
            ->take(10)
            ->get();

        $unreadNotifications = $admin->unreadNotifications()
            ->whereIn("type", $adminNotificationTypes)
            ->count();

        return view('admin.dashboard',[
            'users'=>User::count(),
            'vendors'=>User::where('role','vendor')->count(),
            'pendingVendors'=>User::where('role','vendor')->where('vendor_status','pending')->count(),
            'activeSubscriptions'=>VendorSubscription::active()->count(),
            'products'=>Product::count(),
            'orders'=>Order::count(),
            'revenue'=>ManualPaymentSubmission::whereNotNull('vendor_subscription_id')->where('status','approved')->sum('amount'),
            'notifications'=>$notifications,
            'unreadNotifications'=>$unreadNotifications,
        ]);

    }

    public function vendors(\Illuminate\Http\Request $request)
{
    $search = trim((string) $request->input('q', ''));

    $vendors = \App\Models\User::query()
        ->where('role', 'vendor')
        ->with(['activeSubscription.plan'])
        ->withCount('products')
        ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('vendor_status', 'like', "%{$search}%")
                  ->orWhere('certification_number', 'like', "%{$search}%")
                  ->orWhere('shop_name', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(20)
        ->withQueryString();

    return view('admin.vendors', compact('vendors', 'search'));
}
    public function certify(Request $request, User $user)
{
    abort_unless($user->role === 'vendor', 404);

    $data = $request->validate([
        'action' => 'required|in:approve,reject,suspend',
        'certification_number' => 'nullable|max:100',
    ]);

    if ($data['action'] === 'approve') {

        if (!$user->nni) {
            return back()->withErrors([
                'certification' => 'Impossible de certifier ce vendeur : le NNI n’a pas été fourni.'
            ]);
        }

        if (!$user->certification_document) {
            return back()->withErrors([
                'certification' => 'Impossible de certifier ce vendeur : la carte nationale n’a pas été fournie.'
            ]);
        }

        $user->update([
            'vendor_status' => 'approved',
            'is_certified' => true,
            'certification_number' => $data['certification_number']
                ?: 'SK-CERT-' . strtoupper(Str::random(8)),
            'certified_at' => now(),
        ]);

        // Réafficher uniquement les produits qui étaient actifs avant la suspension.
        \App\Models\Product::where('vendor_id', $user->id)
            ->where('is_archived', false)
            ->where('was_active_before_subscription_expiry', true)
            ->update([
                'is_active' => true,
                'was_active_before_subscription_expiry' => false,
            ]);
    }

    elseif ($data['action'] === 'suspend') {

        $user->update([
            'vendor_status' => 'suspended',
            'is_certified' => false,
        ]);

        // Mémoriser les produits actifs puis les masquer pendant la suspension.
        \App\Models\Product::where('vendor_id', $user->id)
            ->where('is_active', true)
            ->where('is_archived', false)
            ->update([
                'was_active_before_subscription_expiry' => true,
                'is_active' => false,
            ]);
    }

    else {

        $user->update([
            'vendor_status' => 'pending',
            'is_certified' => false,
        ]);
    }

    // Notification au vendeur concernant la décision de certification.
    if ($data['action'] === 'approve') {
        $user->notify(new \App\Notifications\VendorAdminActivityNotification(
            'Certification acceptée',
            'Votre dossier de certification vendeur a été accepté par l’administrateur.',
            '✅',
            route('vendor.dashboard'),
            'certification_approved'
        ));
    } elseif ($data['action'] === 'reject') {
        $user->notify(new \App\Notifications\VendorAdminActivityNotification(
            'Certification rejetée',
            'Votre dossier de certification vendeur a été rejeté par l’administrateur.',
            '❌',
            route('vendor.certification'),
            'certification_rejected'
        ));
    } elseif ($data['action'] === 'suspend') {
        $user->notify(new \App\Notifications\VendorAdminActivityNotification(
            'Certification suspendue',
            'Votre certification vendeur a été suspendue par l’administrateur.',
            '⏸️',
            route('vendor.certification'),
            'certification_suspended'
        ));
    }

    return back()->with('success', 'Statut vendeur mis à jour.');
}
    public function subscriptions()
{
    $subscriptions = VendorSubscription::with(['vendor', 'plan'])
        ->latest()
        ->paginate(25);

    $paymentsBySubscription = ManualPaymentSubmission::with(['user', 'vendor', 'subscription'])
        ->whereNotNull('vendor_subscription_id')
        ->latest()
        ->get()
        ->groupBy('vendor_subscription_id')
        ->map(function ($payments) {
            return $payments->first();
        });

    return view('admin.subscriptions', compact(
        'subscriptions',
        'paymentsBySubscription'
    ));
}
    public function payments(Request $request)
{
    $search = trim((string) $request->input('q', ''));

    $payments = ManualPaymentSubmission::with([
            'user',
            'vendor',
            'subscription',
            'order'
        ])
        ->whereNotNull('vendor_subscription_id')
        ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($q) use ($search) {

                $q->where('provider', 'like', "%{$search}%")
                    ->orWhere('transfer_number', 'like', "%{$search}%")
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");

                $q->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });

                $q->orWhereHas('order', function ($orderQuery) use ($search) {
                    $orderQuery
                        ->where('order_number', 'like', "%{$search}%")
                        ->orWhere('shipping_phone', 'like', "%{$search}%");
                });
            });
        })
        ->latest()
        ->paginate(30)
        ->withQueryString();

    return view('admin.payments', compact('payments', 'search'));
}
    public function validatePayment(Request $request, ManualPaymentSubmission $payment){$data=$request->validate(['action'=>'required|in:approve,reject','admin_note'=>'nullable|max:1000']);abort_if($payment->status!=='pending',422,'Cette demande a déjà été traitée.'); if($data['action']==='reject'){$payment->update(['status'=>'rejected','admin_note'=>$data['admin_note']??null,'validated_by'=>auth()->id(),'validated_at'=>now()]); if($payment->vendor_subscription_id)$payment->subscription->update(['payment_status'=>'pending']); return back()->with('success','Paiement rejeté.');} DB::transaction(function()use($payment,$data){$payment->update(['status'=>'approved','admin_note'=>$data['admin_note']??null,'validated_by'=>auth()->id(),'validated_at'=>now()]); if($payment->vendor_subscription_id){
                $sub=$payment->subscription;
                $start=now();

                $sub->update([
                    'status'=>'active',
                    'payment_status'=>'paid',
                    'starts_at'=>$start,
                    'ends_at'=>$start->copy()->addDays((int) $sub->plan->duration_days),
                    'admin_note'=>$data['admin_note']??null
                ]);

                $vendor = $sub->vendor;

                if($vendor){
                    $vendor->notify(
                        new VendorSubscriptionApprovedNotification($sub)
                    );
                }
            } if($payment->order_id){$vo=$payment->vendorOrder; if($vo){$vo->update(['payment_status'=>'paid','status'=>'processing']); $order=$payment->order; $pending=$order->vendorOrders()->where('payment_status','!=','paid')->exists(); if(!$pending)$order->update(['payment_status'=>'paid','status'=>'processing']);}}});

        // Supprimer uniquement la notification liée à cette preuve de paiement.
        $paymentUserName = optional($payment->user)->name;

        if ($paymentUserName) {
            auth()->user()->notifications()
                ->where('type', \App\Notifications\AdminActivityNotification::class)
                ->where('data->title', 'Nouvelle preuve de paiement')
                ->get()
                ->filter(function ($notification) use ($paymentUserName) {
                    return str_contains(
                        $notification->data['message'] ?? '',
                        $paymentUserName
                    );
                })
                ->each
                ->delete();
        }

        return back()->with('success','Paiement validé.');
}
    public function updateSubscription(Request $request, VendorSubscription $subscription)
    {
        $data = $request->validate([
            'action' => 'required|in:activate,expire,cancel',
            'admin_note' => 'nullable|max:1000',
        ]);

        if ($data['action'] === 'activate') {
            $starts = now();

            $subscription->update([
                'status' => 'active',
                'payment_status' => 'paid',
                'starts_at' => $starts,
                'ends_at' => $starts->copy()->addDays((int) $subscription->plan->duration_days),
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            // Réafficher uniquement les produits qui étaient actifs avant l'expiration.
            \App\Models\Product::where('vendor_id', $subscription->vendor_id)
                ->where('is_archived', false)
                ->where('was_active_before_subscription_expiry', true)
                ->update([
                    'is_active' => true,
                    'was_active_before_subscription_expiry' => false,
                ]);
        }

        if ($data['action'] === 'expire') {
            $subscription->update([
                'status' => 'expired',
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            // Mémoriser les produits visibles avant expiration puis les masquer.
            \App\Models\Product::where('vendor_id', $subscription->vendor_id)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->update([
                    'was_active_before_subscription_expiry' => true,
                    'is_active' => false,
                ]);
        }

        if ($data['action'] === 'cancel') {
            $subscription->update([
                'status' => 'cancelled',
                'payment_status' => 'pending',
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            // Une annulation rend également les produits du vendeur invisibles.
            \App\Models\Product::where('vendor_id', $subscription->vendor_id)
                ->where('is_active', true)
                ->where('is_archived', false)
                ->update([
                    'was_active_before_subscription_expiry' => true,
                    'is_active' => false,
                ]);
        }

        // Notification au vendeur concernant la décision sur son abonnement.
        $vendor = $subscription->vendor;

        if ($vendor) {
            if ($data['action'] === 'activate') {
                $vendor->notify(new \App\Notifications\VendorAdminActivityNotification(
                    'Abonnement activé',
                    'Votre abonnement ' . ($subscription->plan->name ?? '') . ' a été activé par l’administrateur.',
                    '✅',
                    route('vendor.dashboard'),
                    'subscription_activated'
                ));
            } elseif ($data['action'] === 'expire') {
                $vendor->notify(new \App\Notifications\VendorAdminActivityNotification(
                    'Abonnement expiré',
                    'Votre abonnement a été marqué comme expiré par l’administrateur.',
                    '⏸️',
                    route('vendor.dashboard'),
                    'subscription_expired'
                ));
            } elseif ($data['action'] === 'cancel') {
                $vendor->notify(new \App\Notifications\VendorAdminActivityNotification(
                    'Abonnement annulé',
                    'Votre abonnement a été annulé par l’administrateur.',
                    '❌',
                    route('vendor.dashboard'),
                    'subscription_cancelled'
                ));
            }
        }

        return back()->with('success', 'Abonnement mis à jour.');
    }
    public function plans(){return view('admin.plans',['plans'=>SubscriptionPlan::latest()->get()]);}
    public function storePlan(Request $request){$data=$request->validate(['name'=>'required|max:120','description'=>'nullable','price'=>'required|numeric|min:0','duration_days'=>'required|integer|min:1','product_limit'=>'nullable|integer|min:1']);$data['is_active']=true;SubscriptionPlan::create($data);return back()->with('success','Plan créé.');}

    public function editPlan(SubscriptionPlan $plan)
    {
        return view('admin.plan-edit', compact('plan'));
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $data = $request->validate([
            'name' => 'required|max:120',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'product_limit' => 'nullable|integer|min:1',
        ]);

        $plan->update($data);

        return redirect()
            ->route('admin.plans')
            ->with('success', 'Plan modifié avec succès.');
    }

    public function deletePlan(SubscriptionPlan $plan)
    {
        $used = \App\Models\VendorSubscription::where(
            'subscription_plan_id',
            $plan->id
        )->exists();

        if ($used) {
            return back()->with(
                'error',
                'Ce plan ne peut pas être supprimé car il est déjà utilisé par un ou plusieurs abonnements. Vous pouvez le désactiver à la place.'
            );
        }

        $plan->delete();

        return back()->with(
            'success',
            'Plan supprimé avec succès.'
        );
    }

    public function togglePlan(SubscriptionPlan $plan){$plan->update(['is_active'=>!$plan->is_active]);return back();}
}
