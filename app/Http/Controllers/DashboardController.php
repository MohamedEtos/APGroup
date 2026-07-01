<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin/manager dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasAnyRole(['admin', 'manager'])) {
            // Fetch stats
            $totalInvoices = Invoice::count();
            $totalQty = InvoiceItem::sum('qty');
            $totalKg = InvoiceItem::sum('total_kg');
            $totalDeliveredKg = InvoiceItem::sum('delivered_total_kg');

            // 1. Shipped weight monthly stats (last 12 months)
            $monthlyKg = array_fill(1, 12, 0);
            $invoices = Invoice::with('items')->get();
            foreach ($invoices as $invoice) {
                if ($invoice->date) {
                    $month = Carbon::parse($invoice->date)->month;
                    $monthlyKg[$month] += $invoice->items->sum('total_kg');
                }
            }
            $monthlyDataArray = array_values($monthlyKg); 

            // 2. Top 5 consumed fabric types
            $topFabrics = InvoiceItem::select('type')
                ->selectRaw('SUM(total_kg) as total_kg')
                ->groupBy('type')
                ->orderByDesc('total_kg')
                ->limit(5)
                ->get();
            
            $fabricLabels = $topFabrics->pluck('type')->toArray();
            $fabricData = $topFabrics->pluck('total_kg')->map(fn($v) => (float)$v)->toArray();

            // 3. Latest 6 invoices
            $latestInvoices = Invoice::with('items')->orderBy('id', 'desc')->limit(6)->get();

            // 4. Latest 6 received logs (timeline)
            $timelineInvoices = Invoice::where('status', 'received')
                ->orderBy('updated_at', 'desc')
                ->limit(6)
                ->get();

            return view('dashboard', compact(
                'totalInvoices',
                'totalQty',
                'totalKg',
                'totalDeliveredKg',
                'monthlyDataArray',
                'fabricLabels',
                'fabricData',
                'latestInvoices',
                'timelineInvoices',
                'topFabrics'
            ));
        } elseif ($user->hasRole('office') || $user->can('create invoices')) {
            return redirect()->route('office-invoices.index');
        } elseif ($user->hasRole('store') || $user->hasRole('employee') || $user->can('receive invoices')) {
            return redirect()->route('receive-invoices.index');
        }
        
        abort(403, 'غير مصرح لك بدخول هذه الصفحة.');
    }
}
