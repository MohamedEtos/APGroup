<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class TablesController extends Controller
{
    /**
     * Display a listing of all invoice items with their parent invoice info.
     */
    public function index()
    {
        $invoices = Invoice::with('items')
            ->orderByDesc('id')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id'             => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'img'            => $invoice->img,
                    'sender'         => $invoice->sender,
                    'receiver'       => $invoice->receiver,
                    'date'           => $invoice->date,
                    'total_qty'      => $invoice->total_qty,
                    'total_kg'       => $invoice->total_kg,
                    'items'          => $invoice->items->map(fn($item) => [
                        'code'         => $item->code,
                        'type'         => $item->type,
                        'fabric_color' => $item->fabric_color,
                        'qty'          => $item->qty,
                        'unit'         => $item->unit,
                        'total_kg'     => $item->total_kg,
                    ])->values()->all(),
                ];
            });

        return view('tables', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(tables $tables)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(tables $tables)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, tables $tables)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(tables $tables)
    {
        //
    }
}
