@extends('layouts.dashboard')

@section('content')
<div class="max-w-8xl mx-auto p-6">
    <!-- Back Button -->
    <div class="mb-6 flex justify-between space-x-3">
        <div>
            <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 bg-gray-500 rounded-lg text-sm text-white">Kembali</a>
        </div>
        <div>
            <a href="{{ route('purchase-orders.export.pdf', $po) }}" class="text-sm px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Export PDF
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-xl p-6 border">
        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-4 mb-4">
            <div>
                <h1 class="text-xl font-bold">Purchase Order</h1>
                <p class="text-gray-500 text-sm">Nomor SPK: {{ $po->no_spk }}</p>
                <p class="text-gray-500 text-sm">Customer: <span class="font-medium">{{ $po->customer }}</span></p>
                <p class="text-gray-500 text-sm">Tempat Produksi: <span class="font-medium">{{ $po->tempat_produksi ?? 'N/A' }}</span></p>
            </div>
            <div>
                <div>
                    <span>Status Finance: </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($po->status === 'PENDING_FINANCE') bg-yellow-100 text-yellow-800
                        @elseif($po->status === 'APPROVED_FINANCE') bg-green-100 text-green-800
                        @elseif($po->status === 'REJECTED') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ str_replace('_',' ', $po->status) ?? 'N/A' }}
                    </span>
                </div>
                <div class="mt-2">
                    <span>Status Production: </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($po->production_status === 'QUEUE_PRODUCTION') bg-yellow-100 text-yellow-800
                        @elseif($po->production_status === 'IN_PRODUCTION') bg-blue-100 text-blue-800
                        @elseif($po->production_status === 'PENDING_PRODUCTION') bg-purple-100 text-purple-800
                        @elseif($po->production_status === 'DONE_PRODUCTION') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ str_replace('_',' ', $po->production_status) ?? 'N/A' }}
                    </span>
                </div>
                <div class="mt-2">
                    @if($po->production_status === 'DONE_PRODUCTION')
                    <span>Status Shipping: </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($po->shipping_status === 'READY_TO_SHIP') bg-yellow-100 text-yellow-800
                        @elseif($po->shipping_status === 'SHIPPED') bg-green-100 text-green-800
                        @endif">
                        {{ str_replace('_',' ', $po->shipping_status) ?? 'N/A' }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Info Utama -->
        <div class="grid grid-cols-2 gap-6 text-sm mb-6">
            <div>
                <p class="text-gray-500">Tanggal Dibuat</p>
                <p class="font-medium">{{ $po->created_at ? $po->created_at->format('d M Y H:i') : '-' }}</p>
            </div>
            @if($po->status === 'APPROVED_FINANCE' && in_array(Auth::user()->role, ['PRODUKSI', 'SHIPPER', 'MARKETING']))
            <div>
                <p class="text-gray-500">Tanggal diedit</p>
                <p class="font-medium">{{ $po->updated_at ? $po->updated_at->format('d M Y H:i') : '-' }}</p>
            </div>
            @endif
        </div>

        <!-- Items -->
        <h2 class="text-lg font-semibold mb-3">Detail Item</h2>
        <div class="overflow-x-auto">
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border">Jenis Produksi</th>
                        <th class="px-3 py-2 border">Kode Desain</th>
                        <th class="px-3 py-2 border">Desain</th>
                        <th class="px-3 py-2 border">Bahan</th>
                        <th class="px-3 py-2 border">Ukuran</th>
                        <th class="px-3 py-2 border text-right">Qty</th>
                        @if(!in_array(Auth::user()->role, ['PRODUKSI','SHIPPER']))
                        <th class="px-3 py-2 border text-right">HPP</th>
                        <th class="px-3 py-2 border text-right">Total HPP</th>
                        <th class="px-3 py-2 border text-right">Harga Jual</th>
                        <th class="px-3 py-2 border text-right">Total Harga Jual</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($po->items as $item)
                        <tr>
                            <td class="px-3 py-2 border">{{ $item->jenis_produksi ?? '-' }}</td>
                            <td class="px-3 py-2 border">{{ $item->kode_desain ?? '-' }}</td>
                            <td class="px-3 py-2 border">
                                @if($item->desain_approve)
                                    <img src="{{ asset($item->desain_approve) }}" class="w-16 h-16 object-cover items-center mx-auto" alt="Belum di upload">
                                    <div class="text-center bg-green-500 text-xs font-semibold w-full rounded-full p-1 mt-2 inline-block">
                                    <button type="button" class="text-xs text-white" @click="$store.imageModal.show('{{ asset($item->desain_approve) }}')">Lihat Desain</button></div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border">{{ $item->bahan ?? '-' }}</td>
                            <td class="px-3 py-2 border">{{ $item->ukuran ?? '-' }}</td>
                            <td class="px-3 py-2 border text-right">{{ number_format($item->quantity) }}</td>
                            @if(!in_array(Auth::user()->role, ['PRODUKSI','SHIPPER']))
                            <td class="px-3 py-2 border text-right">Rp {{ number_format($item->harga_pokok_penjualan, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 border text-right">Rp {{ number_format($item->total_hpp, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 border text-right">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 border text-right">Rp {{ number_format($item->total_harga_jual, 0, ',', '.') }}</td>
                            @endif
                        </tr>
                        @if($item->po_press || $item->po_print || $item->po_press_print)
                        <tr class="bg-gray-50 text-xs text-gray-600">
                            <td colspan="10" class="px-3 py-2 border">
                                <strong>Produksi:</strong>
                                {{ $item->po_press ? 'Press: '.$item->po_press.' ' : '' }}
                                {{ $item->po_print ? 'Print: '.$item->po_print.' ' : '' }}
                                {{ $item->po_press_print ? 'Press/Print: '.$item->po_press_print : '' }}
                            </td>
                        </tr>
                        @endif
                        @if($item->fqc_us || $item->fqc_la || $item->fqc_jt)
                        <tr class="bg-gray-50 text-xs text-gray-600">
                            <td colspan="10" class="px-3 py-2 border">
                                <strong>FQC:</strong>
                                {{ $item->fqc_us ? $item->fqc_us.' ' : '' }}
                                {{ $item->fqc_la ? $item->fqc_la.' ' : '' }}
                                {{ $item->fqc_jt ? $item->fqc_jt : '' }}
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
                @if(!in_array(Auth::user()->role, ['PRODUKSI','SHIPPER']))
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="7" class="px-3 py-2 border text-right">Grand Total</td>
                        <td class="px-3 py-2 border text-right">Rp {{ number_format($po->total_hpp, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 border"></td>
                        <td class="px-3 py-2 border text-right">Rp {{ number_format($po->total_harga_jual, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Payment Info -->
        @if(!in_array(Auth::user()->role, ['PRODUKSI','SHIPPER']))
        <div class="flex justify-end items-center gap-12 mt-6 text-sm">
            <div>
                <h3 class="text-md font-semibold mb-2">Informasi Modal Kerja</h3>
            </div>
            <div>
                <p class="text-gray-500">Down Payment</p>
                <p class="font-medium">Rp {{ number_format($po->down_payment, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Sisa Pembayaran</p>
                <p class="font-medium">Rp {{ number_format($po->sisa_pembayaran_hpp, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="flex justify-end items-center gap-12 mt-6 text-sm">
            <div>
                <h3 class="text-md font-semibold mb-2">Informasi Payment Customer</h3>
            </div>
            <div>
                <p class="text-gray-500">Down Payment</p>
                <p class="font-medium">Rp {{ number_format($po->down_payment, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Sisa Pembayaran</p>
                <p class="font-medium">Rp {{ number_format($po->sisa_pembayaran_hargajual, 0, ',', '.') }}</p>
            </div>
        </div>
        @endif
    </div>

    @can('finance-actions')
    <div class="bg-white shadow rounded-xl p-6 border mt-6">
        {{-- Card status saat ini --}}
        @if($po->status)
            <div class="flex mb-4 p-4 bg-gray-100 rounded border shadow-sm">
                <div>
                    <p class="font-medium">Status saat ini: 
                        <span class="font-bold text-sm bg-green-100 text-green-800 p-2 rounded">
                            {{ str_replace('_',' ',$po->status === 'APPROVED_FINANCE' ? 'Approved Finance' : 'Rejected') ?? 'N/A' }}
                        </span>
                    </p>
                    @if($po->status === 'REJECTED' && $po->rejected_note)
                        <p class="mt-1 text-gray-700">Alasan: {{ $po->rejected_note }}</p>
                    @endif
                    @if($po->status === 'APPROVED_FINANCE' && $po->bukti_transfer)
                        <p class="mt-1 text-gray-700">
                            Bukti Transfer: 
                            <a href="{{ asset($po->bukti_transfer) }}" target="_blank" class="text-gray-600 text-sm font-medium underline">Lihat File</a>
                        </p>
                    @endif
                </div>
                <div class="ml-auto">
                    <button type="button" id="edit_status_btn" class="mt-3 px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit Status</button>
                </div>
            </div>
        @endif

        {{-- Form update status --}}
        <form action="{{ route('purchase-orders.finance.update', $po) }}" method="POST" enctype="multipart/form-data" 
            class="bg-white p-4 rounded shadow-md" 
            id="finance_form" 
            style="{{ $po->status ? 'display:none;' : '' }}">
            @csrf

            <div class="mb-4">
                <label for="status" class="block font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded shadow-sm">
                    <option value="">-- Pilih Status --</option>
                    <option value="APPROVED_FINANCE" {{ old('status', $po->status) == 'APPROVED_FINANCE' ? 'selected' : '' }}>Approved</option>
                    <option value="REJECTED" {{ old('status', $po->status) == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
                </select>
                @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div id="bukti_transfer_container" class="mb-4" style="display:none;">
                <label for="bukti_transfer" class="block font-medium text-gray-700">Upload Bukti Transfer</label>
                <input type="file" name="bukti_transfer" id="bukti_transfer" class="mt-1 block w-full border-gray-300 rounded">
                @error('bukti_transfer') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div id="rejected_note_container" class="mb-4" style="display:none;">
                <label for="rejected_note" class="block font-medium text-gray-700">Alasan Rejected</label>
                <textarea name="rejected_note" id="rejected_note" rows="3" class="mt-1 block w-full border-gray-300 rounded">{{ old('rejected_note', $po->rejected_note) }}</textarea>
                @error('rejected_note') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update Status</button>
                <button type="button" id="cancel_edit_btn" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Batal</button>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const buktiContainer = document.getElementById('bukti_transfer_container');
        const rejectedContainer = document.getElementById('rejected_note_container');
        const editBtn = document.getElementById('edit_status_btn');
        const cancelBtn = document.getElementById('cancel_edit_btn');
        const financeForm = document.getElementById('finance_form');

        function toggleFields() {
            const value = statusSelect.value;
            if (value === 'APPROVED_FINANCE') {
                buktiContainer.style.display = 'block';
                rejectedContainer.style.display = 'none';
            } else if (value === 'REJECTED') {
                buktiContainer.style.display = 'none';
                rejectedContainer.style.display = 'block';
            } else {
                buktiContainer.style.display = 'none';
                rejectedContainer.style.display = 'none';
            }
        }

        toggleFields();
        statusSelect.addEventListener('change', toggleFields);

        if(editBtn) {
            editBtn.addEventListener('click', function() {
                financeForm.style.display = 'block';
                editBtn.parentElement.style.display = 'none';
            });
        }

        if(cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                financeForm.style.display = 'none';
                if(editBtn) {
                    editBtn.parentElement.style.display = 'block';
                }
            });
        }
    });
    </script>
    @endcan


    <!-- PRODUCTION SELECT ACTIONS -->

    @can('production-actions')
    <div class="mt-6">
        {{-- Card status produksi saat ini --}}
        @if($po->production_status)
            <div class="mb-4 p-4 bg-gray-100 rounded border shadow-sm">
                <p class="font-medium">Status Produksi saat ini: 
                    <span class="font-bold text-xs py-1 px-2 bg-green-100 text-green-800 rounded">
                        {{ str_replace('_',' ', $po->production_status) }}
                    </span>
                </p>
                @if($po->production_status === 'PENDING_PRODUCTION' && $po->production_note)
                    <p class="mt-1 text-gray-700">Catatan Produksi: {{ $po->production_note }}</p>
                @endif
                
                @if($po->production_status === 'QUEUE_PRODUCTION' || $po->production_status === 'IN_PRODUCTION' || $po->production_status === 'PENDING_PRODUCTION')
                <button type="button" id="edit_production_status_btn" class="mt-3 px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit Status Produksi</button>
                @endif
            </div>
        @endif

        {{-- Form update status produksi --}}
        <form action="{{ route('purchase-orders.production.update', $po) }}" method="POST" class="bg-white p-4 rounded shadow-md" id="production_form" style="{{ $po->production_status ? 'display:none;' : '' }}">
            @csrf

            <div class="mb-4">
                <label for="production_status" class="block font-medium text-gray-700">Status Produksi</label>
                <select name="production_status" id="production_status" class="mt-1 block w-full border-gray-300 rounded shadow-sm">
                    <option value="">-- Pilih Status --</option>
                    <option value="QUEUE_PRODUCTION" {{ old('production_status', $po->production_status) == 'QUEUE_PRODUCTION' ? 'selected' : '' }}>Queue Produksi</option>
                    <option value="PENDING_PRODUCTION" {{ old('production_status', $po->production_status) == 'PENDING_PRODUCTION' ? 'selected' : '' }}>Pending Produksi</option>
                    <option value="IN_PRODUCTION" {{ old('production_status', $po->production_status) == 'IN_PRODUCTION' ? 'selected' : '' }}>In Production</option>
                    <option value="DONE_PRODUCTION" {{ old('production_status', $po->production_status) == 'DONE_PRODUCTION' ? 'selected' : '' }}>Done Production</option>
                </select>
                @error('production_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Catatan produksi --}}
            <div id="production_note_container" class="mb-4" style="display:none;">
                <label for="production_note" class="block font-medium text-gray-700">Catatan Produksi</label>
                <textarea name="production_note" id="production_note" rows="3" class="mt-1 block w-full border-gray-300 rounded">{{ old('production_note', $po->production_note) }}</textarea>
                @error('production_note') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update Status Produksi</button>
                <button type="button" id="cancel_production_btn" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Batal</button>
            </div>
        </form>
    </div>



    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const productionSelect = document.getElementById('production_status');
        const noteContainer = document.getElementById('production_note_container');
        const editBtn = document.getElementById('edit_production_status_btn');
        const cancelBtn = document.getElementById('cancel_production_btn');
        const productionForm = document.getElementById('production_form');

        function toggleNote() {
            if (productionSelect.value === 'PENDING_PRODUCTION') {
                noteContainer.style.display = 'block';
            } else {
                noteContainer.style.display = 'none';
            }
        }

        toggleNote();
        productionSelect.addEventListener('change', toggleNote);

        if(editBtn) {
            editBtn.addEventListener('click', function() {
                productionForm.style.display = 'block';
                editBtn.parentElement.style.display = 'none';
            });
        }

        if(cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                productionForm.style.display = 'none';
                if(editBtn) {
                    editBtn.parentElement.style.display = 'block';
                }
            });
        }
    });
    </script>
    @endcan

    <!-- END PRODUCTION SELECT ACTIONS -->

    <!-- SHIPPING SELECT ACTION -->
    @can('shipping-actions')
    <div class="mt-6">
        {{-- Card status shipping --}}
        @if($po->shipping_status)
            <div class="mb-4 p-4 bg-gray-100 rounded border shadow-sm text-md" id="shipping_card">
                <p class="font-bold mb-1">Status Pengiriman saat ini: 
                    <span class="font-bold text-sm p-1 bg-green-100 text-green-800">
                        {{ str_replace('_', ' ', $po->shipping_status) ?? 'N/A' }}
                    </span>
                </p>
                <p class="font-bold mb-1">No Invoice: <span class="font-normal">{{ $po->no_invoice ?? 'N/A' }}</span></p>
                <p class="font-bold mb-1">Tanggal Kirim: <span class="font-normal">{{ $po->tanggal_kirim ? $po->tanggal_kirim->format('d M Y H:i') : '-' }}</span></p>
                <p class="font-bold mb-1">Alamat Pengiriman: <span class="font-normal">{{ $po->alamat_pengiriman ?? 'N/A' }}</span></p>

                {{-- tombol edit hanya muncul kalau belum ada no_invoice --}}
                @if(!$po->no_invoice)
                    <button type="button" id="edit_shipping_status_btn" 
                        class="mt-3 px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                        Edit Status Pengiriman
                    </button>
                @endif
            </div>
        @endif

        {{-- Form update shipping --}}
        <form action="{{ route('purchase-orders.shipping.update', $po) }}" method="POST" 
            class="bg-white p-4 rounded shadow-md" id="shipping_form" 
            style="{{ $po->shipping_status ? 'display:none;' : '' }}">
            @csrf

            <div class="mb-4">
                <label for="shipping_status" class="block font-medium text-gray-700">Status Pengiriman</label>
                <select name="shipping_status" id="shipping_status" class="mt-1 block w-full border-gray-300 rounded shadow-sm">
                    <option value="">-- Pilih Status --</option>
                    <option value="READY_TO_SHIP" {{ old('shipping_status', $po->shipping_status) == 'READY_TO_SHIP' ? 'selected' : '' }}>Ready to Ship</option>
                    <option value="SHIPPED" {{ old('shipping_status', $po->shipping_status) == 'SHIPPED' ? 'selected' : '' }}>Shipped</option>
                </select>
                @error('shipping_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div id="shipping_fields" style="display:none;">
                {{-- Invoice otomatis tampil readonly --}}
                <div class="mb-4" id="invoice_field" style="display:none;">
                    <label for="no_invoice" class="block font-medium text-gray-700">No Invoice</label>
                    <input type="text" name="no_invoice" id="no_invoice" 
                        class="mt-1 block w-full border-gray-300 rounded bg-gray-100 text-gray-700" 
                        value="{{ old('no_invoice', $po->no_invoice) }}" readonly>
                </div>
                <div class="mb-4" id="tanggal_kirim_wrapper">
                    <label for="tanggal_kirim" class="block font-medium text-gray-700">Tanggal Kirim</label>
                    <input type="date" name="tanggal_kirim" id="tanggal_kirim" 
                        min="{{ now()->toDateString() }}" 
                        class="mt-1 block w-full border-gray-300 rounded" 
                        value="{{ old('tanggal_kirim', $po->tanggal_kirim) }}">
                </div>
                <div class="mb-4" id="alamat_pengiriman_wrapper">
                    <label for="alamat_pengiriman" class="block font-medium text-gray-700">Alamat Pengiriman</label>
                    <textarea name="alamat_pengiriman" id="alamat_pengiriman" rows="3" 
                        class="mt-1 block w-full border-gray-300 rounded">{{ old('alamat_pengiriman', $po->alamat_pengiriman) }}</textarea>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update Status Pengiriman</button>
                <button type="button" id="cancel_shipping_btn" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Batal</button>
            </div>
        </form>

        {{-- Script gabungan --}}
        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const editBtn   = document.getElementById("edit_shipping_status_btn");
            const cancelBtn = document.getElementById("cancel_shipping_btn");
            const form      = document.getElementById("shipping_form");
            const card      = document.getElementById("shipping_card");

            const statusSelect = document.getElementById("shipping_status");
            const shippingFields = document.getElementById("shipping_fields");
            //const invoiceField   = document.getElementById("invoice_field");

            function toggleFields() {
                if (statusSelect.value === "SHIPPED") {
                    shippingFields.style.display = "block";
                    //invoiceField.style.display = "block";
                } else if (statusSelect.value === "READY_TO_SHIP") {
                    shippingFields.style.display = "block";
                    //invoiceField.style.display = "none";
                } else {
                    shippingFields.style.display = "none";
                }
            }

            // klik edit → tampilkan form
            if (editBtn) {
                editBtn.addEventListener("click", function () {
                    form.style.display = "block";
                    card.style.display = "none";
                    toggleFields();
                });
            }

            // klik batal → sembunyikan form
            if (cancelBtn) {
                cancelBtn.addEventListener("click", function () {
                    form.style.display = "none";
                    card.style.display = "block";
                });
            }

            // jalankan awal
            if (statusSelect) {
                statusSelect.addEventListener("change", toggleFields);
                toggleFields();
            }
        });
        </script>
    @endcan

</div>
@endsection