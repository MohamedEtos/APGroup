<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'code',
        'type',
        'fabric_color',
        'qty',
        'unit',
        'price',
    ];

    /**
     * العلاقة مع الفاتورة الأم
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * إجمالي سطر الفاتورة
     */
    public function getSubtotalAttribute()
    {
        return $this->qty * $this->price;
    }
}
