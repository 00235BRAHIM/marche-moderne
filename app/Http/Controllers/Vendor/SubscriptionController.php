<?php

namespace App\Http\Controllers\Vendor;

use App\Support\AdminNotifier;
use App\Http\Controllers\Controller;
use App\Models\{SubscriptionPlan, VendorSubscription};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        $subscriptions = auth()->user()
            ->subscriptions()
            ->with(['plan', 'payments' => fn($q) => $q->latest()])
            ->latest()
            ->get();

        return view('vendor.subscriptions', compact('plans', 'subscriptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'required|in:airtel_money,moov_money',
        ]);

        $plan = SubscriptionPlan::where('is_active', true)
            ->findOrFail($data['subscription_plan_id']);

        $sub = VendorSubscription::create([
            'vendor_id' => auth()->id(),
            'subscription_plan_id' => $plan->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $data['payment_method'],
            'reference' => 'SUB-' . strtoupper(Str::random(10)),
        ]);

        return redirect()
            ->route('vendor.subscriptions')
            ->with(
                'success',
                'Abonnement créé. Saisissez votre numéro puis lancez le paiement automatique.'
            );
    }

    public function certification()
    {
        return view('vendor.certification');
    }

    public function submitCertification(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'nni' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'nni')->ignore($user->id),
            ],
            'certification_document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:4096',
            ],
            'phone' => 'nullable|max:30',
        ], [
            'nni.unique' => 'Ce NNI est déjà utilisé par un autre compte. Veuillez vérifier votre numéro.',
            'nni.required' => 'Le NNI est obligatoire.',
            'certification_document.required' => 'La pièce de certification est obligatoire.',
            'certification_document.mimes' => 'Le document doit être au format PDF, JPG, JPEG ou PNG.',
            'certification_document.max' => 'Le document ne doit pas dépasser 4 Mo.',
        ]);

        if ($request->hasFile('certification_document')) {
            $data['certification_document'] =
                $request->file('certification_document')
                    ->store('vendor-certifications', 'public');
        }

        $user->update([
            'nni' => $data['nni'],
            'certification_document' => $data['certification_document'],
            'phone' => $data['phone'] ?? $user->phone,
            'vendor_status' => 'pending',
            'is_certified' => false,
        ]);

        /*
         * Envoyer la notification Admin seulement
         * après l'enregistrement réussi de la certification.
         */
        AdminNotifier::send(
            'Certification vendeur à vérifier',
            $user->name . ' a envoyé son dossier de certification.',
            '📄',
            route('admin.vendors')
        );

        return back()->with(
            'success',
            'Votre dossier de certification a été transmis à l’administrateur.'
        );
    }
}
