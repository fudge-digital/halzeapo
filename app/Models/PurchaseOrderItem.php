<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $table = 'purchase_order_items';
    protected $fillable = [
        'purchase_order_id',
        'jenis_produksi',
        'kode_desain',
        'desain_approve',
        'bahan',
        'ukuran',
        'quantity',
        'harga_pokok_penjualan',
        'total_hpp',
        'harga_jual',
        'total_harga_jual',
        'po_press',
        'po_print',
        'po_press_print',
        'fqc_us',
        'fqc_la',
        'fqc_jt',
    ];

    protected $casts = [
        'harga_pokok_penjualan' => 'decimal:2',
        'total_hpp' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'total_harga_jual' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    
}
