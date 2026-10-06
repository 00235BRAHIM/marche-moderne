<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\AdminActivityNotification;

class AdminNotifier
{
    public static function send(
        string $title,
        string $message,
        string $icon = '🔔',
        ?string $url = null
    ): void {
        User::where('role', 'admin')
            ->get()
            ->each(function ($admin) use ($title, $message, $icon, $url) {
                $admin->notify(
                    new AdminActivityNotification(
                        $title,
                        $message,
                        $icon,
                        $url
                    )
                );
            });
    }
}
