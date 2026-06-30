<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReceiveInvoiceController extends Controller
{
    /**
     * Display a listing of all pending invoices.
     */
    public function index()
    {
        $invoices = Invoice::with('items')
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->get();

        return view('receive-invoices.index', compact('invoices'));
    }

    /**
     * Show the form for receiving the specified invoice.
     */
    public function show($id)
    {
        $invoice = Invoice::with('items')->where('status', 'pending')->findOrFail($id);

        return view('receive-invoices.form', compact('invoice'));
    }

    /**
     * Update the invoice items with delivered quantities and complete the reception.
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::where('status', 'pending')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|exists:invoice_items,id',
            'items.*.delivered_qty' => 'required|numeric|min:0',
            'items.*.delivered_total_kg' => 'required|numeric|min:0',
        ], [
            'items.required' => 'يجب إدخال تفاصيل الأصناف.',
            'items.*.delivered_qty.required' => 'الكمية المستلمة مطلوبة لكل صنف.',
            'items.*.delivered_qty.numeric' => 'يجب أن تكون الكمية المستلمة رقماً.',
            'items.*.delivered_qty.min' => 'يجب أن تكون الكمية المستلمة 0 أو أكثر.',
            'items.*.delivered_total_kg.required' => 'الوزن المستلم مطلوب لكل صنف.',
            'items.*.delivered_total_kg.numeric' => 'يجب أن يكون الوزن المستلم رقماً.',
            'items.*.delivered_total_kg.min' => 'يجب أن يكون الوزن المستلم 0 أو أكثر.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        foreach ($validated['items'] as $itemData) {
            $item = InvoiceItem::where('invoice_id', $invoice->id)->findOrFail($itemData['id']);
            $item->update([
                'delivered_qty' => $itemData['delivered_qty'],
                'delivered_total_kg' => $itemData['delivered_total_kg'],
            ]);
        }

        // Change status to received so it disappears from pending
        $invoice->update([
            'status' => 'received',
        ]);

        return redirect()->route('receive-invoices.index')->with('success', 'تم استلام الفاتورة وتأكيد الكميات بنجاح!');
    }
}
