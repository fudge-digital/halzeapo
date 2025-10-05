<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PurchaseOrder;

class PurchaseOrderPolicy
{
    /**
     * Siapa saja bisa lihat list (marketing & finance).
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['MARKETING', 'FINANCE']);
    }

    /**
     * Siapa saja bisa lihat detail (marketing & finance).
     */
    public function view(User $user, PurchaseOrder $po): bool
    {
        return in_array($user->role, ['MARKETING', 'FINANCE', 'PRODUKSI', 'SHIPPER']);
    }

    /**
     * Hanya marketing boleh buat PO.
     */
    public function create(User $user): bool
    {
        return $user->role === 'MARKETING';
    }

    public function update(User $user, PurchaseOrder $po)
    {
        return $user->role === 'MARKETING';
    }

    /**
     * Hanya finance boleh upload bukti transfer.
     */
    public function uploadBuktiTransfer(User $user, PurchaseOrder $po): bool
    {
        return $user->role === 'FINANCE';
    }

    /**
     * Marketing & finance boleh melihat bukti transfer.
     */
    public function viewBuktiTransfer(User $user, PurchaseOrder $po): bool
    {
        return in_array($user->role, ['MARKETING', 'FINANCE']);
    }
}
