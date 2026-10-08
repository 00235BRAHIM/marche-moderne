<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function poll()
    {
        $admin = auth()->user();

        $adminNotificationTypes = [
            \App\Notifications\AdminActivityNotification::class,
            \App\Notifications\VendorAdminActivityNotification::class,
        ];

        $notifications = $admin->unreadNotifications()
            ->whereIn('type', $adminNotificationTypes)
            ->latest()
            ->take(10)
            ->get();

        $unreadNotifications = $admin->unreadNotifications()
            ->whereIn('type', $adminNotificationTypes)
            ->count();

        return response()->json([
            'unreadNotifications' => $unreadNotifications,
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'icon' => $notification->data['icon'] ?? '🔔',
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'url' => $notification->data['url'] ?? null,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                ];
            }),
        ]);
    }

    public function read(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('admin.dashboard');

        return redirect()->to($url);
    }

    public function readAll()
    {
        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with(
            'success',
            'Toutes les notifications ont été marquées comme lues.'
        );
    }
}
