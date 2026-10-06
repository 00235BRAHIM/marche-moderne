<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VendorNotificationController extends Controller
{
    private function notificationTypes(): array
    {
        return [
            'App\\Notifications\\NewVendorOrderNotification',
            'App\\Notifications\\VendorPaymentProofNotification',
            'App\\Notifications\\VendorSubscriptionApprovedNotification',
            'App\\Notifications\\VendorAdminActivityNotification',
        ];
    }

    public function index()
    {
        $types = $this->notificationTypes();

        $notifications = auth()->user()
            ->notifications()
            ->whereIn('type', $types)
            ->latest()
            ->take(20)
            ->get();

        $unreadCount = auth()->user()
            ->unreadNotifications()
            ->whereIn('type', $types)
            ->count();

        return response()->json([
            'count' => $unreadCount,

            'notifications' => $notifications->map(function ($notification) {

                $data = $notification->data;
                $type = $data['type'] ?? null;

                $isPaymentProof = $type === 'vendor_payment_proof';
                $isSubscriptionApproved = $type === 'vendor_subscription_approved';
                $isAdminActivity = $type === 'vendor_admin_activity';

                /*
                 * Notifications administratives
                 */
                if ($isAdminActivity) {

                    return [
                        'id' => $notification->id,
                        'type' => $type,
                        'event' => $data['event'] ?? 'admin_activity',
                        'title' => $data['title'] ?? 'Mise à jour administrative',
                        'icon' => $data['icon'] ?? '🔔',
                        'message' => $data['message'] ?? '',
                        'order_number' => null,
                        'vendor_order_number' => null,
                        'customer_name' => null,
                        'total' => 0,
                        'provider' => null,
                        'created_at' => $notification->created_at
                            ->locale('fr')
                            ->translatedFormat('d F Y'),
                        'time' => $notification->created_at->format('H:i:s'),
                        'url' => $data['url'] ?? route('vendor.dashboard'),
                        'read' => !is_null($notification->read_at),
                    ];
                }

                /*
                 * Notification abonnement validé
                 */
                if ($isSubscriptionApproved) {

                    return [
                        'id' => $notification->id,
                        'type' => $type,
                        'event' => $data['event'] ?? 'subscription_approved',
                        'title' => $data['title'] ?? 'Abonnement validé',
                        'icon' => $data['icon'] ?? '✅',
                        'message' => $data['message']
                            ?? 'Votre abonnement a été validé par l’administrateur.',
                        'order_number' => null,
                        'vendor_order_number' => null,
                        'customer_name' => null,
                        'total' => 0,
                        'provider' => null,
                        'created_at' => $notification->created_at
                            ->locale('fr')
                            ->translatedFormat('d F Y'),
                        'time' => $notification->created_at->format('H:i:s'),
                        'url' => $data['url'] ?? route('vendor.subscriptions'),
                        'read' => !is_null($notification->read_at),
                    ];
                }

                /*
                 * Notification preuve de paiement
                 */
                if ($isPaymentProof) {

                    return [
                        'id' => $notification->id,
                        'type' => $type,
                        'event' => $data['event'] ?? 'payment_proof_submitted',
                        'title' => 'Nouvelle preuve de paiement',
                        'icon' => '💳',
                        'message' => 'Le client a envoyé une nouvelle preuve de paiement.',
                        'order_number' => $data['order_number'] ?? null,
                        'vendor_order_number' => $data['vendor_order_number'] ?? null,
                        'customer_name' => $data['customer_name'] ?? null,
                        'total' => $data['total'] ?? ($data['amount'] ?? 0),
                        'provider' => $data['provider'] ?? null,
                        'created_at' => $notification->created_at
                            ->locale('fr')
                            ->translatedFormat('d F Y'),
                        'time' => $notification->created_at->format('H:i:s'),
                        'url' => !empty($data['vendor_order_id'])
                            ? route('vendor.orders.show', $data['vendor_order_id'])
                            : route('vendor.dashboard'),
                        'read' => !is_null($notification->read_at),
                    ];
                }

                /*
                 * Notification nouvelle commande
                 */
                return [
                    'id' => $notification->id,
                    'type' => $type,
                    'event' => $data['event'] ?? 'new_order',
                    'title' => 'Nouvelle commande',
                    'icon' => '🛒',
                    'message' => 'Une nouvelle commande vous a été attribuée.',
                    'order_number' => $data['order_number'] ?? null,
                    'vendor_order_number' => $data['vendor_order_number'] ?? null,
                    'customer_name' => $data['customer_name'] ?? null,
                    'total' => $data['total'] ?? 0,
                    'provider' => $data['provider'] ?? null,
                    'created_at' => $notification->created_at
                        ->locale('fr')
                        ->translatedFormat('d F Y'),
                    'time' => $notification->created_at->format('H:i:s'),
                    'url' => !empty($data['vendor_order_id'])
                        ? route('vendor.orders.show', $data['vendor_order_id'])
                        : route('vendor.dashboard'),
                    'read' => !is_null($notification->read_at),
                ];
            }),
        ]);
    }

    public function read(Request $request, string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->whereIn('type', $this->notificationTypes())
            ->firstOrFail();

        $notification->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function readAll()
    {
        auth()->user()
            ->unreadNotifications()
            ->whereIn('type', $this->notificationTypes())
            ->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
