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
        'status',
    ];

    /**
     * العلاقة مع محتويات الفاتورة
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    /**
     * إجمالي الكيلو للفاتورة كلها
     */
    public function getTotalKgAttribute()
    {
        return $this->items->sum('total_kg');
    }

    /**
     * إجمالي الكميات
     */
    public function getTotalQtyAttribute()
    {
        return $this->items->sum('qty');
    }
}
