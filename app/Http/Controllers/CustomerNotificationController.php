<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerNotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->where('type', 'App\\Notifications\\CustomerOrderStatusNotification')
            ->latest()
            ->take(20)
            ->get();

        $unreadCount = auth()->user()
            ->unreadNotifications()
            ->where('type', 'App\\Notifications\\CustomerOrderStatusNotification')
            ->count();

        return response()->json([
            'count' => $unreadCount,

            'notifications' => $notifications->map(function ($notification) {
                $data = $notification->data;

                $event = $data['event'] ?? 'status';

                $titles = [
                    'payment_approved' => 'Paiement validé',
                    'payment_rejected' => 'Paiement rejeté',
                    'delivered' => 'Commande livrée',
                ];

                $icons = [
                    'payment_approved' => '✅',
                    'payment_rejected' => '❌',
                    'delivered' => '🚚',
                ];

                return [
                    'id' => $notification->id,
                    'event' => $event,
                    'title' => $titles[$event] ?? 'Mise à jour de commande',
                    'icon' => $icons[$event] ?? '🔔',
                    'order_number' => $data['order_number'] ?? null,
                    'vendor_order_number' => $data['vendor_order_number'] ?? null,
                    'total' => $data['total'] ?? 0,
                    'message' => $data['message'] ?? null,
                    'created_at' => $notification->created_at
                        ->locale('fr')
                        ->translatedFormat('d F Y'),
                    'time' => $notification->created_at->format('H:i:s'),
                    'url' => route(
                        'order.show',
                        $data['order_id']
                    ),
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
            ->where('type', 'App\\Notifications\\CustomerOrderStatusNotification')
            ->firstOrFail();

        $notification->delete();

        $unreadCount = auth()->user()
            ->unreadNotifications()
            ->where('type', 'App\\Notifications\\CustomerOrderStatusNotification')
            ->count();

        return response()->json([
            'success' => true,
            'count' => $unreadCount,
        ]);
    }

    public function readAll()
    {
        auth()->user()
            ->unreadNotifications()
            ->where('type', 'App\\Notifications\\CustomerOrderStatusNotification')
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
