<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        $user = Auth::user();

        // contoh: marketing sees own, finance sees all, produksi sees approved
        if ($user->role === 'MARKETING') {
            $pos = PurchaseOrder::with('items')->where('created_by', $user->id)->latest()->paginate(10);
        } elseif (in_array($user->role, ['PRODUKSI', 'SHIPPER'])) {
            $pos = PurchaseOrder::with('items')->where('status', PurchaseOrder::STATUS_APPROVED)->latest()->paginate(10);
        } else {
            $pos = PurchaseOrder::with('items')->latest()->paginate(10);
        }

        return view('purchase_orders.index', compact('pos'));
    }

    public function edit($id)
    {
        // Ambil data PO berdasarkan ID
        $po = PurchaseOrder::with('items')->findOrFail($id);

        return view('purchase_orders.edit', compact('po'));
    }

    public function create()
    {
        $previewNoSpk = PurchaseOrder::generatePreviewNoSpk();
        return view('purchase_orders.create', compact('previewNoSpk'));
    }

    // -----------------------
    // UPDATE (MARKETING ONLY)
    // -----------------------
    public function update(Request $request, PurchaseOrder $po)
    {
        $id = $po->id;
        $po = PurchaseOrder::findOrFail($id);

        $user = Auth::user();
        if ($user->role !== 'MARKETING') {
            abort(403);
        }

        $headerRules = [
            'customer' => 'required|string|max:255',
            'tempat_produksi' => 'nullable|string|max:255',
            'down_payment' => 'nullable|numeric',
            'down_payment_type' => 'nullable|string|in:nominal,persen',
        ];

        $itemsRules = [
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:purchase_order_items,id',
            'items.*.jenis_produksi' => 'nullable|string|max:255',
            'items.*.kode_desain' => 'nullable|string|max:255',
            'items.*.desain_approve' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'items.*.bahan' => 'nullable|string|max:255',
            'items.*.ukuran' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.harga_pokok_penjualan' => 'nullable|numeric',
            'items.*.harga_jual' => 'nullable|numeric',
            'items.*.po_press' => 'nullable|string',
            'items.*.po_print' => 'nullable|string',
            'items.*.po_press_print' => 'nullable|string',
            'items.*.fqc_us_option' => 'nullable|string',
            'items.*.fqc_us_note' => 'nullable|string',
            'items.*.fqc_la_option' => 'nullable|string',
            'items.*.fqc_la_note' => 'nullable|string',
            'items.*.fqc_jt_option' => 'nullable|string',
            'items.*.fqc_jt_note' => 'nullable|string',
        ];

        $request->validate(array_merge($headerRules, $itemsRules));

        DB::beginTransaction();
        try {
            $po->update([
                'customer' => $request->input('customer'),
                'tempat_produksi' => $request->input('tempat_produksi'),
            ]);

            $totalHpp = 0;
            $totalHargaJual = 0;

            // === FIX BAGIAN INI ===
            // Ambil semua id item lama dari DB
            $existingIds = $po->items()->pluck('id')->toArray();
            // Ambil semua id item dari request
            $requestIds = collect($request->items)->pluck('id')->filter()->toArray();

            // Hapus item yang tidak ada di request
            $toDelete = array_diff($existingIds, $requestIds);
            if (!empty($toDelete)) {
                // Hapus file desain_approve juga kalau ada
                $itemsToDelete = $po->items()->whereIn('id', $toDelete)->get();
                foreach ($itemsToDelete as $itemDel) {
                    if ($itemDel->desain_approve && file_exists(public_path($itemDel->desain_approve))) {
                        @unlink(public_path($itemDel->desain_approve));
                    }
                }
                $po->items()->whereIn('id', $toDelete)->delete();
            }
            // === SAMPAI SINI ===

            foreach ($request->items as $i => $item) {
                $itemModel = isset($item['id'])
                    ? $po->items()->find($item['id'])
                    : $po->items()->make();

                // Upload file baru jika ada
                $desainPath = $itemModel->desain_approve ?? null;

                if ($request->hasFile("items.$i.desain_approve")) {
                    // Hapus file lama jika ada dan masih tersimpan di public path
                    if ($desainPath && file_exists(public_path($desainPath))) {
                        @unlink(public_path($desainPath));
                    }

                    // Upload file baru
                    $file = $request->file("items.$i.desain_approve");
                    $filename = time() . '_' . Str::random(6) . '_' . $file->getClientOriginalName();
                    $destination = public_path(env('UPLOAD_PATH', 'uploads/desain'));

                    if (!file_exists($destination)) {
                        mkdir($destination, 0777, true);
                    }

                    $file->move($destination, $filename);
                    $desainPath = 'uploads/desain/' . $filename;
                }

                $qty = (int) ($item['quantity'] ?? 0);
                $hpp = isset($item['harga_pokok_penjualan']) ? floatval(preg_replace('/[^\d.]/','',$item['harga_pokok_penjualan'])) : 0;
                $hargaJual = isset($item['harga_jual']) ? floatval(preg_replace('/[^\d.]/','',$item['harga_jual'])) : 0;

                $total_hpp_item = $hpp * $qty;
                $total_harga_jual_item = $hargaJual * $qty;

                $itemModel->fill([
                    'jenis_produksi' => $item['jenis_produksi'] ?? null,
                    'kode_desain' => $item['kode_desain'] ?? null,
                    'desain_approve' => $desainPath,
                    'bahan' => $item['bahan'] ?? null,
                    'ukuran' => $item['ukuran'] ?? null,
                    'quantity' => $qty,
                    'harga_pokok_penjualan' => $hpp,
                    'total_hpp' => $total_hpp_item,
                    'harga_jual' => $hargaJual,
                    'total_harga_jual' => $total_harga_jual_item,
                    'po_press' => $item['po_press'] ?? null,
                    'po_print' => $item['po_print'] ?? null,
                    'po_press_print' => $item['po_press_print'] ?? null,
                    'fqc_us' => $item['fqc_us_note'] ?? null,
                    'fqc_la' => $item['fqc_la_note'] ?? null,
                    'fqc_jt' => $item['fqc_jt_note'] ?? null,
                ])->save();

                $totalHpp += $total_hpp_item;
                $totalHargaJual += $total_harga_jual_item;
            }

            // Recalculate DP & sisa
            $downPaymentType = $request->input('down_payment_type', 'nominal');
            $downPaymentInput = floatval($request->input('down_payment', 0));

            $downPaymentHargaJual = 0;
            $downPaymentHpp = 0;

            // Hitung nilai nominal down payment (dalam rupiah)
            if ($downPaymentType === 'persen') {
                // Jika persen, konversi ke nominal berdasarkan total harga jual
                $downPaymentHargaJual = ($totalHargaJual * $downPaymentInput) / 100;
                $downPaymentHpp = ($totalHpp * $downPaymentInput) / 100;
            } else {
                // Jika nominal langsung
                $downPaymentHargaJual = $downPaymentInput;
                $downPaymentHpp = $downPaymentInput;
            }

            // Sisa masing-masing
            $sisaHPP = max($totalHpp - $downPaymentHpp, 0);
            $sisaHargaJual = max($totalHargaJual - $downPaymentHargaJual, 0);

            $po->update([
                'total_hpp' => $totalHpp,
                'total_harga_jual' => $totalHargaJual,
                'down_payment' => round($downPaymentHpp, 2),
                'down_payment_type' => $downPaymentType,
                'sisa_pembayaran_hpp' => round($sisaHPP, 2),
                'sisa_pembayaran_hargajual' => round($sisaHargaJual, 2)
            ]);

            DB::commit();

            return redirect()->route('purchase-orders.show', $po)
                ->with('success', 'Purchase Order berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error saat update PO: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // -----------------------
    // STORE (MARKETING ONLY)
    // -----------------------
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'MARKETING') {
            abort(403);
        }

        // header validation
        $headerRules = [
            'customer' => 'required|string|max:255',
            'tempat_produksi' => 'nullable|string|max:255',
            'no_spk' => 'nullable|string|max:255',
            'down_payment' => 'nullable|numeric',
            'down_payment_type' => 'nullable|in:nominal,persen',
            // sisa_pembayaran optional; server akan hitung ulang
        ];

        // item validation rules
        $itemsRules = [
            'items' => 'required|array|min:1',
            'items.*.jenis_produksi' => 'nullable|string|max:255',
            'items.*.kode_desain' => 'nullable|string|max:255',
            'items.*.desain_approve' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'items.*.bahan' => 'nullable|string|max:255',
            'items.*.ukuran' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.harga_pokok_penjualan' => 'nullable|numeric',
            'items.*.harga_jual' => 'nullable|numeric',
            'items.*.po_press' => 'nullable|string',
            'items.*.po_print' => 'nullable|string',
            'items.*.po_press_print' => 'nullable|string',
            'items.*.fqc_us_option' => 'nullable|string',
            'items.*.fqc_us_note' => 'nullable|string',
            'items.*.fqc_la_option' => 'nullable|string',
            'items.*.fqc_la_note' => 'nullable|string',
            'items.*.fqc_jt_option' => 'nullable|string',
            'items.*.fqc_jt_note' => 'nullable|string',
        ];

        $request->validate(array_merge($headerRules, $itemsRules));

        // Prepare header
        DB::beginTransaction();
        try {
            // generate no_spk reliably
            $lastPo = PurchaseOrder::latest('id')->lockForUpdate()->first();
            $nextNumber = $lastPo ? $lastPo->id + 1 : 1;
            $bulanRomawi = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'];

            $no_spk = $request->input('no_spk') ?? sprintf("PO.%05d/HZ/%s/%d", $nextNumber, $bulanRomawi[now()->month], now()->year);

            $po = PurchaseOrder::create([
                'no_spk' => $no_spk,
                'customer' => $request->input('customer'),
                'tempat_produksi' => $request->input('tempat_produksi'),
                'down_payment' => $request->input('down_payment', 0),
                'down_payment_type' => $request->input('down_payment_type', 'nominal'),
                'sisa_pembayaran_hpp' => 0, // akan diupdate setelah hitung totals
                'sisa_pembayaran_hargajual' => 0, // akan diupdate setelah hitung totals
                'status' => PurchaseOrder::STATUS_PENDING,
                'created_by' => $user->id,
            ]);

            $totalHpp = 0;
            $totalHargaJual = 0;

            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $i => $item) {
                    // handle per-item file
                    $desainPath = null;
                    if ($request->hasFile("items.$i.desain_approve")) {
                        $file = $request->file("items.$i.desain_approve");
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $destination = public_path(env('UPLOAD_PATH', 'uploads/desain'));

                        if (!file_exists($destination)) {
                            mkdir($destination, 0777, true);
                        }

                        $file->move($destination, $filename);
                        $desainPath = 'uploads/desain/' . $filename;
                    }

                    // map FQC: (example mapping)
                    $fqc_us = ($item['fqc_us_option'] ?? null) === 'Ya' ? ($item['fqc_us_note'] ? 'F-QC Ultrasonic - '. $item['fqc_us_note'] : 'FQC US') : null;
                    $fqc_la = ($item['fqc_la_option'] ?? null) === 'Ya' ? ($item['fqc_la_note'] ? 'F-QC Laser Api - '. $item['fqc_la_note'] : 'FQC LA') : null;
                    $fqc_jt = ($item['fqc_jt_option'] ?? null) === 'Ya' ? ($item['fqc_jt_note'] ? 'F-QC Jahit Tepi - '. $item['fqc_jt_note'] : 'FQC JT') : null;

                    $qty = (int) ($item['quantity'] ?? 0);
                    $hpp = isset($item['harga_pokok_penjualan']) ? floatval(preg_replace('/[^\d.]/','',$item['harga_pokok_penjualan'])) : 0;
                    $hargaJual = isset($item['harga_jual']) ? floatval(preg_replace('/[^\d.]/','',$item['harga_jual'])) : 0;

                    $total_hpp_item = $hpp * $qty;
                    $total_harga_jual_item = $hargaJual * $qty;

                    $po->items()->create([
                        'jenis_produksi' => $item['jenis_produksi'] ?? null,
                        'kode_desain' => $item['kode_desain'] ?? null,
                        'desain_approve' => $desainPath,
                        'bahan' => $item['bahan'] ?? null,
                        'ukuran' => $item['ukuran'] ?? null,
                        'quantity' => $qty,
                        'harga_pokok_penjualan' => $hpp,
                        'total_hpp' => $total_hpp_item ?? '0',
                        'harga_jual' => $hargaJual,
                        'total_harga_jual' => $total_harga_jual_item,
                        'po_press' => $item['po_press'] ?? null,
                        'po_print' => $item['po_print'] ?? null,
                        'po_press_print' => $item['po_press_print'] ?? null,
                        'fqc_us' => $item['fqc_us'] ?? null,
                        'fqc_la' => $item['fqc_la'] ?? null,
                        'fqc_jt' => $item['fqc_jt'] ?? null,
                    ]);

                    $totalHpp += $total_hpp_item;
                    $totalHargaJual += $total_harga_jual_item;
                }
            }

            // Bukti Transfer DP
            $request->validate([
                'bukti_transfer_dp' => 'file|mimes:pdf,jpg,png',
            ]);

            if ($request->hasFile('bukti_transfer_dp')) {
                $file = $request->file('bukti_transfer_dp');
                $filename = time() . '_' . $file->getClientOriginalName();

                $destination = public_path(env('UPLOAD_PATH_BUKTI_DP', 'uploads/bukti_transfer_dp'));

                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }

                $file->move($destination, $filename);
                $po->bukti_transfer_dp = 'uploads/bukti_transfer_dp/' . $filename;
            }

            // Recalculate DP & sisa
            $downPaymentType = $request->input('down_payment_type', 'nominal');
            $downPaymentInput = floatval($request->input('down_payment', 0));

            $downPaymentHargaJual = 0;
            $downPaymentHpp = 0;

            // Hitung nilai nominal down payment (dalam rupiah)
            if ($downPaymentType === 'persen' || $downPaymentType === 'percent') {
                // Jika persen, konversi ke nominal berdasarkan total harga jual
                $downPaymentHargaJual = ($totalHargaJual * $downPaymentInput) / 100;
                $downPaymentHpp = ($totalHpp * $downPaymentInput) / 100;
            } else {
                // Jika nominal langsung
                $downPaymentHargaJual = $downPaymentInput;
                $downPaymentHpp = $downPaymentInput;
            }

            // Sisa masing-masing
            $sisaHPP = max($totalHpp - $downPaymentHpp, 0);
            $sisaHargaJual = max($totalHargaJual - $downPaymentHargaJual, 0);

            $po->update([
                'total_hpp' => $totalHpp,
                'total_harga_jual' => $totalHargaJual,
                'down_payment' => round($downPaymentHpp, 2),
                'down_payment_type' => $downPaymentType,
                'sisa_pembayaran_hpp' => round($sisaHPP, 2),
                'sisa_pembayaran_hargajual' => round($sisaHargaJual, 2)
            ]);

            DB::commit();

            return redirect()->route('purchase-orders.show', $po)->with('success', 'Purchase Order berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error saat membuat PO: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // -----------------------
    // SHOW
    // -----------------------
    public function show(PurchaseOrder $po)
    {
        $user = Auth::user();
        $po->load('items');
        return view('purchase_orders.show', ['po' => $po]);
    }

    // -----------------------
    // UPDATE FINANCE STATUS (FINANCE ONLY)
    // -----------------------
    public function updateFinanceStatus(Request $request, PurchaseOrder $po)
    {
        $this->authorize('finance-actions');

        $request->validate([
            'status' => 'required|in:APPROVED_FINANCE,REJECTED',
            'bukti_transfer' => 'required_if:status_finance,APPROVED_FINANCE|file|mimes:pdf,jpg,png',
            'rejected_note' => 'required_if:status_finance,REJECTED|nullable|string|max:500',
        ]);

        $po->status = $request->status;

        if ($request->status === 'APPROVED_FINANCE' && $request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = time() . '_' . $file->getClientOriginalName();

            $destination = public_path(env('UPLOAD_PATH_BUKTI', 'uploads/bukti_transfer'));

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $po->bukti_transfer = 'uploads/bukti_transfer/' . $filename;
            $po->rejected_note = null;
        }

        if ($request->status === 'REJECTED') {
            $po->rejected_note = $request->rejected_note;
            $po->bukti_transfer = null;
        }

        $po->save();

        return redirect()->back()->with('success', 'Status finance berhasil diperbarui.');
    }

    // -----------------------
    // UPDATE PRODUCTION STATUS (PRODUCTION ONLY)
    // -----------------------

    public function updateProductionStatus(Request $request, PurchaseOrder $po)
    {
        $this->authorize('production-actions', $po); // hanya PRODUKSI

        $request->validate([
            'production_status' => 'required|string|in:QUEUE_PRODUCTION,PENDING_PRODUCTION,IN_PRODUCTION,DONE_PRODUCTION',
            'production_note' => 'nullable|string',
        ]);

        // Simpan
        $po->update([
            'production_status' => $request->input('production_status'),
            'production_note' => $request->input('production_status') === 'PENDING_PRODUCTION'
                ? $request->input('production_note')
                : null, // hanya simpan note jika status pending
        ]);

        if ($request->input('production_status') === 'DONE_PRODUCTION') {
            // otomatis set status READY_TO_SHIP untuk SHIPPER
            $po->update([
                'production_status' => 'DONE_PRODUCTION',
                'ready_to_ship_status' => 'READY_TO_SHIP' // bisa pakai kolom terpisah atau sama production_status jika mau
            ]);
        }

        return redirect()->route('purchase-orders.show', $po)
                        ->with('success', 'Status Produksi berhasil diupdate.');
    }

    // -----------------------
    // UPDATE SHIPPING STATUS (SHIPPER ONLY)
    // -----------------------
    public function updateShippingStatus(Request $request, PurchaseOrder $po)
    {
        $this->authorize('shipping-actions'); // hanya SHIPPER

        $validated = $request->validate([
            'shipping_status' => 'required|string|in:READY_TO_SHIP,SHIPPED',
            'no_invoice' => 'nullable|string|max:255',
            'tanggal_kirim' => 'required_if:shipping_status,SHIPPED|date|after_or_equal:today',
            'alamat_pengiriman' => 'required_if:shipping_status,SHIPPED|string',
        ]);

        // Default data
        $data = [
            'shipping_status' => $validated['shipping_status'],
        ];

        // Jika status SHIPPED → generate invoice kalau belum ada
        if ($validated['shipping_status'] === 'SHIPPED') {
            if (!$po->no_invoice) {
                $lastPoWithInvoice = PurchaseOrder::whereNotNull('no_invoice')
                    ->latest('id')
                    ->first();

                $nextNumber = $lastPoWithInvoice
                    ? intval(substr($lastPoWithInvoice->no_invoice, -4)) + 1
                    : 1;

                $no_invoice = sprintf(
                    "INV-%s-HZ-%04d",
                    now()->format('dmY'),
                    $nextNumber
                );

                $data['no_invoice'] = $no_invoice;
            } else {
                $data['no_invoice'] = $po->no_invoice; // jangan overwrite
            }

            $data['tanggal_kirim'] = $validated['tanggal_kirim'];
            $data['alamat_pengiriman'] = $validated['alamat_pengiriman'];
        }

        $po->update($data);

        return redirect()->route('purchase-orders.show', $po)
            ->with('success', 'Status pengiriman berhasil diupdate.');
    }

    // -----------------------
    // EXPORT PDF
    // -----------------------
    // public function exportPdf(PurchaseOrder $po)
    // {
    //     $user = auth()->user();

    //     if ($user->hasRole('MARKETING')) {
    //         $view = 'pdf.marketing';
    //         $prefix = 'MARKETING';
    //     } elseif ($user->hasRole('FINANCE')) {
    //         $view = 'pdf.finance';
    //         $prefix = 'FINANCE';
    //     } elseif ($user->hasRole('PRODUKSI')) {
    //         $view = 'pdf.produksi';
    //         $prefix = 'PRODUKSI';
    //     } elseif ($user->hasRole('SHIPPER')) {
    //         $view = 'pdf.shipper';
    //         $prefix = 'SHIPPER';
    //     } else {
    //         $view = 'pdf.default';
    //         $prefix = 'PO';
    //     }

    //     $pdf = \PDF::loadView($view, compact('po', 'user'))
    //         ->setPaper('A4', 'landscape');
        
    //     $filename = $prefix . "-" . str_replace(['/', '.', '\\'], '-', $po->no_spk) . ".pdf";

    //     return $pdf->download($filename);
    // }

    public function invoiceCustomer(PurchaseOrder $po)
    {
        $user = auth()->user();

        if ($user->hasRole('MARKETING')) {
            $view = 'pdf.customerinvoice';
        } else {
            $view = 'pdf.default';
        }

        $pdf = \PDF::loadView($view, compact('po', 'user'))
            ->setPaper('A4', 'landscape');

        $filename = "INVOICE-CUSTOMER-" . str_replace(['/', '.', '\\'], '-', $po->no_spk) . ".pdf";

        return $pdf->download($filename);
    }
    public function OrderProduksi(PurchaseOrder $po)
    {
        $user = auth()->user();

        if ($user->hasRole('PRODUKSI')) {
            $view = 'pdf.produksi';
        } else {
            $view = 'pdf.default';
        }

        $pdf = \PDF::loadView($view, compact('po', 'user'))
            ->setPaper('A4', 'landscape');

        $filename = "ORDER-PRODUKSI-" . str_replace(['/', '.', '\\'], '-', $po->no_spk) . ".pdf";

        return $pdf->download($filename);
    }
    public function CustomerOrder(PurchaseOrder $po)
    {
        $user = auth()->user();

        if ($user->hasRole('SHIPPER')) {
            $view = 'pdf.shipper';
        } else {
            $view = 'pdf.default';
        }

        $pdf = \PDF::loadView($view, compact('po', 'user'))
            ->setPaper('A4', 'landscape');

        $filename = "CUSTOMER-ORDER-" . str_replace(['/', '.', '\\'], '-', $po->no_spk) . ".pdf";

        return $pdf->download($filename);
    }

    // DELETE PURCHASE ORDER (MARKETING ONLY)
    public function destroy($id)
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->delete();

        return redirect()->route('purchase-orders.index')
                        ->with('success', 'Purchase Order berhasil dihapus.');
    }

}

