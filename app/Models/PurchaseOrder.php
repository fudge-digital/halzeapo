<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    public const STATUS_PENDING  = 'PENDING_FINANCE';
    public const STATUS_APPROVED = 'APPROVED_FINANCE';
    public const STATUS_REJECTED = 'REJECTED';

    public const STATUS_QUEUE_PRODUCTION = 'QUEUE_PRODUCTION';
    public const STATUS_PENDING_PRODUCTION = 'PENDING_PRODUCTION';
    public const STATUS_IN_PRODUCTION = 'IN_PRODUCTION';
    public const STATUS_DONE_PRODUCTION = 'DONE_PRODUCTION';

    public const STATUS_READY_TO_SHIP = 'READY_TO_SHIP';
    public const STATUS_SHIPPED = 'SHIPPED';

    protected $fillable = [
        'no_spk',
        'customer',
        'total_hpp',
        'total_harga_jual',
        'down_payment',
        'sisa_pembayaran',
        'status',
        'production_status',
        'created_by',
        'approved_by',
        'produced_by',
        'shipped_by',
        'bukti_transfer',
        'rejected_note',
        'production_note',
        'updated_by',
        'tanggal_kirim',
        'no_invoice',
        'shipping_status',
        'alamat_pengiriman',
    ];

    protected $casts = [
        'total_hpp' => 'decimal:2',
        'total_harga_jual' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'sisa_pembayaran' => 'decimal:2',
        'tanggal_kirim' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    //
    public function creator() {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function producer()
    {
        return $this->belongsTo(User::class, 'produced_by');
    }

    public function shipper()
    {
        return $this->belongsTo(User::class, 'shipped_by');
    }

    public static function generatePreviewNoSpk()
    {
        $lastPo = self::latest('id')->first();
        $nextNumber = $lastPo ? $lastPo->id + 1 : 1;

        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return sprintf(
            "PO.%05d/HZ/%s/%d",
            $nextNumber,
            $bulanRomawi[now()->month],
            now()->year
        );
    }

}
