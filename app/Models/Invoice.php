<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'invoice_number',
        'receiver',
        'sender',
        'img',
        'date',
    ];

    /**
     * العلاقة مع محتويات الفاتورة
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    /**
     * حساب الإجمالي الكلي للفاتورة
     */
    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->qty * $item->price;
        });
    }

    /**
     * إجمالي الكميات
     */
    public function getTotalQtyAttribute()
    {
        return $this->items->sum('qty');
    }
}
