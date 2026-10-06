<?php
namespace App\Services\Payments;
interface PaymentGateway {
    public function initiate(\App\Models\PaymentTransaction $transaction): array;
    public function status(\App\Models\PaymentTransaction $transaction): array;
}
