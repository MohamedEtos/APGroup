<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OfficeInvoiceController extends Controller
{
    /**
     * Display a listing of the resource (last 5 invoices) and the form.
     */
    public function index()
    {
        $invoices = Invoice::with('items')->orderBy('id', 'desc')->take(5)->get();
        return view('office-invoices', compact('invoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_number'       => 'required|string|max:255',
            'receiver'             => 'required|string|max:255',
            'img'                  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'items'                => 'required|array|min:1',
            'items.*.code'         => 'required|string|max:255',
            'items.*.type'         => 'required|string|max:255',
            'items.*.fabric_color' => 'nullable|string|max:100',
            'items.*.qty'          => 'required|numeric|min:0.001',
            'items.*.unit'         => 'required|in:كيلو,متر,توب',
            'items.*.total_kg'     => 'required|numeric|min:0',
        ], [
            'invoice_number.required'  => 'حقل رقم الفاتورة مطلوب.',
            'receiver.required'        => 'حقل المستلم مطلوب.',
            'items.required'           => 'يجب إضافة صنف واحد على الأقل.',
            'items.array'              => 'صيغة الأصناف غير صحيحة.',
            'items.min'                => 'يجب إضافة صنف واحد على الأقل.',
            'items.*.code.required'    => 'كود التوب مطلوب لكل صنف.',
            'items.*.type.required'    => 'النوع مطلوب لكل صنف.',
            'items.*.qty.required'     => 'الكمية مطلوبة لكل صنف.',
            'items.*.qty.numeric'      => 'يجب أن تكون الكمية رقماً.',
            'items.*.qty.min'          => 'يجب أن تكون الكمية أكبر من صفر.',
            'items.*.unit.required'    => 'الوحدة مطلوبة لكل صنف.',
            'items.*.unit.in'          => 'الوحدة يجب أن تكون: كيلو، متر، أو توب.',
            'items.*.total_kg.required' => 'الإجمالي بالكيلو مطلوب لكل صنف.',
            'items.*.total_kg.numeric'  => 'يجب أن يكون الإجمالي بالكيلو رقماً.',
            'items.*.total_kg.min'      => 'يجب أن يكون الإجمالي بالكيلو 0 أو أكثر.',
            'img.image'                => 'يجب أن يكون الملف المرفوع صورة.',
            'img.mimes'                => 'يجب أن تكون الصورة بصيغة: jpeg, png, jpg, gif, svg.',
            'img.max'                  => 'حجم الصورة لا يجب أن يتعدى 2 ميجابايت.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // معالجة الصورة المضغوطة (base64 من Canvas)
        $imgPath = 'assets/img/team-2.jpg';
        $base64  = $request->input('img_base64');

        if ($base64 && str_contains($base64, 'base64,')) {
            // استخراج نوع الصورة والبيانات
            [$meta, $data] = explode('base64,', $base64);
            $ext      = str_contains($meta, 'webp') ? 'webp' : 'jpg';
            $imgName  = time() . '_' . uniqid() . '.' . $ext;
            $imgDest  = public_path('assets/img/invoices/');

            if (!file_exists($imgDest)) {
                mkdir($imgDest, 0775, true);
            }

            file_put_contents($imgDest . $imgName, base64_decode($data));
            $imgPath = 'assets/img/invoices/' . $imgName;
        }

        // إنشاء الفاتورة الرئيسية
        $invoice = Invoice::create([
            'invoice_number' => $validated['invoice_number'],
            'receiver'       => $validated['receiver'],
            'sender'         => auth()->user()->name ?? 'مستخدم تجريبي',
            'date'           => now()->toDateString(),
            'img'            => $imgPath,
        ]);

        // إنشاء محتويات الفاتورة
        foreach ($validated['items'] as $item) {
            InvoiceItem::create([
                'invoice_id'   => $invoice->id,
                'code'         => $item['code'],
                'type'         => $item['type'],
                'fabric_color' => $item['fabric_color'] ?? null,
                'qty'          => $item['qty'],
                'unit'         => $item['unit'],
                'total_kg'     => $item['total_kg'],
            ]);
        }

        return redirect()->route('office-invoices.index')->with('success', 'تم إضافة الفاتورة بنجاح!');
    }

    /**
     * Display the printable invoice receipt.
     */
    public function showInvoice($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        return view('invoice-receipt', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        return view('edit-invoice', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'invoice_number'       => 'required|string|max:255',
            'receiver'             => 'required|string|max:255',
            'img'                  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'items'                => 'required|array|min:1',
            'items.*.code'         => 'required|string|max:255',
            'items.*.type'         => 'required|string|max:255',
            'items.*.fabric_color' => 'nullable|string|max:100',
            'items.*.qty'          => 'required|numeric|min:0.001',
            'items.*.unit'         => 'required|in:كيلو,متر,توب',
            'items.*.total_kg'     => 'required|numeric|min:0',
        ], [
            'invoice_number.required'  => 'حقل رقم الفاتورة مطلوب.',
            'receiver.required'        => 'حقل المستلم مطلوب.',
            'items.required'           => 'يجب إضافة صنف واحد على الأقل.',
            'items.array'              => 'صيغة الأصناف غير صحيحة.',
            'items.min'                => 'يجب إضافة صنف واحد على الأقل.',
            'items.*.code.required'    => 'كود التوب مطلوب لكل صنف.',
            'items.*.type.required'    => 'النوع مطلوب لكل صنف.',
            'items.*.qty.required'     => 'الكمية مطلوبة لكل صنف.',
            'items.*.qty.numeric'      => 'يجب أن تكون الكمية رقماً.',
            'items.*.qty.min'          => 'يجب أن تكون الكمية أكبر من صفر.',
            'items.*.unit.required'    => 'الوحدة مطلوبة لكل صنف.',
            'items.*.unit.in'          => 'الوحدة يجب أن تكون: كيلو، متر، أو توب.',
            'items.*.total_kg.required' => 'الإجمالي بالكيلو مطلوب لكل صنف.',
            'items.*.total_kg.numeric'  => 'يجب أن يكون الإجمالي بالكيلو رقماً.',
            'items.*.total_kg.min'      => 'يجب أن يكون الإجمالي بالكيلو 0 أو أكثر.',
            'img.image'                => 'يجب أن يكون الملف المرفوع صورة.',
            'img.mimes'                => 'يجب أن تكون الصورة بصيغة: jpeg, png, jpg, gif, svg.',
            'img.max'                  => 'حجم الصورة لا يجب أن يتعدى 2 ميجابايت.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $imgPath = $invoice->img;
        $base64  = $request->input('img_base64');

        if ($base64 && str_contains($base64, 'base64,')) {
            // Delete old image if not default
            if ($invoice->img && $invoice->img !== 'assets/img/team-2.jpg') {
                $oldPath = public_path($invoice->img);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            [$meta, $data] = explode('base64,', $base64);
            $ext      = str_contains($meta, 'webp') ? 'webp' : 'jpg';
            $imgName  = time() . '_' . uniqid() . '.' . $ext;
            $imgDest  = public_path('assets/img/invoices/');

            if (!file_exists($imgDest)) {
                mkdir($imgDest, 0775, true);
            }

            file_put_contents($imgDest . $imgName, base64_decode($data));
            $imgPath = 'assets/img/invoices/' . $imgName;
        } elseif ($request->input('img_removed') === '1') {
            // Delete old image if not default
            if ($invoice->img && $invoice->img !== 'assets/img/team-2.jpg') {
                $oldPath = public_path($invoice->img);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $imgPath = 'assets/img/team-2.jpg';
        }

        $invoice->update([
            'invoice_number' => $validated['invoice_number'],
            'receiver'       => $validated['receiver'],
            'img'            => $imgPath,
        ]);

        // Recreate all items
        $invoice->items()->delete();

        foreach ($validated['items'] as $item) {
            InvoiceItem::create([
                'invoice_id'   => $invoice->id,
                'code'         => $item['code'],
                'type'         => $item['type'],
                'fabric_color' => $item['fabric_color'] ?? null,
                'qty'          => $item['qty'],
                'unit'         => $item['unit'],
                'total_kg'     => $item['total_kg'],
            ]);
        }

        return redirect()->route('tables')->with('success', 'تم تعديل الفاتورة بنجاح!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Delete invoice image if exists and not default
        if ($invoice->img && $invoice->img !== 'assets/img/team-2.jpg') {
            $oldPath = public_path($invoice->img);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('tables')->with('success', 'تم حذف الفاتورة بنجاح!');
    }
}
