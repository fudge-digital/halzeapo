<?php

use App\Models\PurchaseOrder;
use Carbon\Carbon;

if (!function_exists('poStatusInfo')) {
    function poStatusInfo(PurchaseOrder $po)
    {
        $info = [];

        // Format tanggal biar seragam
        $formatDate = fn($date) => Carbon::parse($date)->format('d-m-Y');

        // Bagian: Dibuat oleh (Marketing)
        if ($po->creator) {
            $info[] = "PO Dibuat oleh: <strong>{$po->creator->name}</strong> ({$po->creator->role}) pada " . $formatDate($po->created_at);
        }

        // Bagian: Disetujui oleh (Finance)
        if ($po->approver) {
            $info[] = "PO Disetujui oleh: <strong>{$po->approver->name}</strong> ({$po->approver->role})";
        }

        // Bagian: Diproduksi oleh (Produksi)
        if ($po->producer) {
            $info[] = "PO Diproduksi oleh: <strong>{$po->producer->name}</strong> ({$po->producer->role})";
        }

        // Bagian: Dikirim oleh (Shipper)
        if ($po->shipper) {
            $info[] = "PO Dikirim oleh: <strong>{$po->shipper->name}</strong> ({$po->shipper->role})";
        }

        // Bagian: Terakhir diupdate
        if ($po->updated_at) {
            $lastUpdater = null;

            if ($po->shipped_by) {
                $lastUpdater = $po->shipper;
            } elseif ($po->produced_by) {
                $lastUpdater = $po->producer;
            } elseif ($po->approved_by) {
                $lastUpdater = $po->approver;
            } elseif ($po->created_by) {
                $lastUpdater = $po->creator;
            }

            if ($lastUpdater) {
                $info[] = "Status Terakhir diubah oleh: <strong>{$lastUpdater->name}</strong> ({$lastUpdater->role}) pada " . $formatDate($po->updated_at);
            }
        }

        return implode('<br>', $info);
    }
}
