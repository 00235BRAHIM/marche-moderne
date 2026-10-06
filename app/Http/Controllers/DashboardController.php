<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $u = auth()->user();

        if (in_array($u->role, ['admin', 'super_admin'], true)) {
            return redirect()->route('admin.dashboard');
        }

        if ($u->role === 'vendor') {
            return redirect()->route('vendor.dashboard');
        }

        return view('dashboard.customer');
    }

    public function vendor(Request $request)
    {
        $u = auth()->user();

        $period = $request->get('period', 'monthly');

        if (!in_array($period, ['daily', 'monthly', 'annual'], true)) {
            $period = 'monthly';
        }

        $selectedYear = (int) $request->get('year', now()->year);

        if ($selectedYear < 2020 || $selectedYear > now()->year) {
            $selectedYear = now()->year;
        }

        $selectedMonth = (int) $request->get('month', now()->month);

        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = now()->month;
        }

        $selectedDay = (int) $request->get('day', now()->day);

        $daysInMonth = \Carbon\Carbon::create(
            $selectedYear,
            $selectedMonth,
            1
        )->daysInMonth;

        if ($selectedDay < 1 || $selectedDay > $daysInMonth) {
            $selectedDay = 1;
        }

        $vendorOrders = \App\Models\VendorOrder::where(
            'vendor_id',
            $u->id
        )->where(
            'payment_status',
            'paid'
        );

        if ($period === 'daily') {

            $selectedDate = \Carbon\Carbon::create(
                $selectedYear,
                $selectedMonth,
                $selectedDay
            );

            $vendorOrders->whereDate(
                'created_at',
                $selectedDate->toDateString()
            );

        } elseif ($period === 'monthly') {

            $vendorOrders
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth);

        } else {

            $vendorOrders->whereYear(
                'created_at',
                $selectedYear
            );
        }

        $revenue = $vendorOrders->sum('total');
        $paidOrders = $vendorOrders->count();

        return view('dashboard.vendor', [
            'products' => $u->products()->count(),

            'orders' => \App\Models\Order::whereHas(
                'items.product',
                fn ($q) => $q->where('vendor_id', $u->id)
            )->count(),

            'subscription' => $u
                ->activeSubscription()
                ->with('plan')
                ->first(),

            'revenue' => $revenue,
            'paidOrders' => $paidOrders,

            'period' => $period,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'selectedDay' => $selectedDay,
            'daysInMonth' => $daysInMonth,
        ]);
    }


}
