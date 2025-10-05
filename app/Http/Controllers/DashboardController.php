<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseOrder;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalPO = \App\Models\PurchaseOrder::count();
        $pendingPO = \App\Models\PurchaseOrder::where('status', 'PENDING_FINANCE')->count();
        $approvedPO = \App\Models\PurchaseOrder::where('status', 'APPROVED_FINANCE')->count();
        $rejectedPO = \App\Models\PurchaseOrder::where('status', 'REJECTED')->count();
        $queuedPO = \App\Models\PurchaseOrder::where('production_status', 'QUEUE_PRODUCTION')->count();
        $inprogPO = \App\Models\PurchaseOrder::where('production_status', 'IN_PRODUCTION')->count();
        $completedPO = \App\Models\PurchaseOrder::where('production_status', 'DONE_PRODUCTION')->count();
        $readyshipPO = \App\Models\PurchaseOrder::where('shipping_status', 'READY_TO_SHIP')->count();
        $shippedPO = \App\Models\PurchaseOrder::where('shipping_status', 'SHIPPED')->count();

        // Ambil PO sesuai role
        if (in_array($user->role, ['SUPERADMIN', 'ADMIN'])) {
            // lihat semua PO
            $pos = PurchaseOrder::with('creator')->latest()->limit(10)->get();
        } elseif ($user->role === 'FINANCE') {
            $pos = PurchaseOrder::with('creator')
                ->where('status', 'PENDING_FINANCE')
                ->latest()
                ->limit(10)
                ->get();
        } elseif ($user->role === 'PRODUKSI') {
            $pos = PurchaseOrder::with('creator')
                ->where('status', 'APPROVED_FINANCE')
                ->latest()
                ->limit(10)
                ->get();
        } elseif ($user->role === 'SHIPPER') {
            $pos = PurchaseOrder::with('creator')
                ->where('shipping_status', 'READY_TO_SHIP')
                ->latest()
                ->limit(10)
                ->get();
        } else {
            // default: MARKETING / other -> hanya milik sendiri
            $pos = PurchaseOrder::with('creator')
                ->where('created_by', $user->id)
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('dashboard.index', compact('pos', 'user', 'totalPO', 'pendingPO', 'approvedPO', 'rejectedPO', 'queuedPO', 'inprogPO', 'completedPO', 'readyshipPO', 'shippedPO'));
    
    }
}
