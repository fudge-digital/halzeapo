@extends('layouts.dashboard')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow" x-data="purchaseOrderForm()">
    <h1 class="text-xl font-bold mb-6">Buat Purchase Order</h1>

    <form x-ref="poForm" action="{{ route('purchase-orders.store') }}" @submit.prevent="validateForm" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Nomor SPK --}}
        <div>
            <label for="no_spk" class="block text-sm font-medium text-gray-700">No SPK</label>
            <input type="text" name="no_spk" id="no_spk" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-300" value="{{ $previewNoSpk ?? old('no_spk') }}" readonly>
        </div>

        {{-- Customer --}}
        <div>
            <label for="customer" class="block text-sm font-medium text-gray-700">Customer</label>
            <input type="text" name="customer" id="customer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('customer') }}" required>
        </div>

        {{-- Repeater Items --}}
        <div>
            <h2 class="text-lg font-semibold mb-2">Detail Produksi</h2>
            <template x-for="(item, index) in items" :key="index">
                <div class="border rounded-lg p-4 mb-4 bg-gray-50">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Jenis Produksi</label>
                            <input type="text" :name="`items[${index}][jenis_produksi]`" x-model="item.jenis_produksi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Contoh: Hijab, Scarf, dll">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.jenis_produksi" x-show="errors[index]?.jenis_produksi"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Kode Desain</label>
                            <input type="text" :name="`items[${index}][kode_desain]`" x-model="item.kode_desain" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.kode_desain" x-show="errors[index]?.kode_desain"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Upload Desain Approve</label>
                            <input type="file" :name="`items[${index}][desain_approve]`" accept="image/png,image/jpeg" class="mt-1 block w-full text-sm text-gray-600">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.desain_approve" x-show="errors[index]?.desain_approve"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Bahan</label>
                            <input type="text" :name="`items[${index}][bahan]`" x-model="item.bahan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Contoh: Cotton, Wolfis, dll">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.bahan" x-show="errors[index]?.bahan"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Ukuran</label>
                            <input type="text" :name="`items[${index}][ukuran]`" x-model="item.ukuran" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Contoh: 100x200 cm">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.ukuran" x-show="errors[index]?.ukuran"></p>
                        </div>
                    </div>

                    {{-- PO Press/Print --}}
                    <div class="grid grid-cols-3 gap-4 mt-3">
                        <div>
                            <label class="block text-sm font-medium">PO Press</label>
                            <input type="text" :name="`items[${index}][po_press]`" x-model="item.po_press" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.po_press" x-show="errors[index]?.po_press"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">PO Print</label>
                            <input type="text" :name="`items[${index}][po_print]`" x-model="item.po_print" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.po_print" x-show="errors[index]?.po_print"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">PO Press + Print</label>
                            <input type="text" :name="`items[${index}][po_press_print]`" x-model="item.po_press_print" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.po_press_print" x-show="errors[index]?.po_press_print"></p>
                        </div>
                    </div>

                    {{-- FQC Options --}}
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium">FQC Ultrasonic</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :name="`items[${index}][fqc_us_option]`"
                                    x-model="item.fqc_us" @change="enforceExclusive(index, 'us')">
                                <option value="">-- Pilih --</option>
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                            <textarea class="w-full rounded-md border-gray-300 mt-1" :name="`items[${index}][fqc_us_note]`" x-show="item.fqc_us === 'Ya'" placeholder="Keterangan Ultrasonic"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">FQC Laser Api</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :name="`items[${index}][fqc_la_option]`"
                                    x-model="item.fqc_la" @change="enforceExclusive(index, 'us')">
                                <option value="">-- Pilih --</option>
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                            <textarea class="w-full rounded-md border-gray-300 mt-1" :name="`items[${index}][fqc_la_note]`" x-show="item.fqc_la === 'Ya'" placeholder="Keterangan Laser Api"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">FQC Jahit Tepi</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :name="`items[${index}][fqc_jt_option]`"
                                    x-model="item.fqc_jt" @change="enforceExclusive(index, 'us')">
                                <option value="">-- Pilih --</option>
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                            <textarea class="w-full rounded-md border-gray-300 mt-1" :name="`items[${index}][fqc_jt_note]`" x-show="item.fqc_jt === 'Ya'" placeholder="Keterangan Jahit Tepi"></textarea>
                        </div>
                    </div>

                    {{-- Quantity, Total Harga --}}
                    <div class="grid grid-cols-5 gap-4 mt-3">
                        <div>
                            <label class="block text-sm font-medium">Quantity</label>
                            <input type="number" min="1" :name="`items[${index}][quantity]`" x-model="item.quantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.quantity" x-show="errors[index]?.quantity"></p>
                        </div>
                        <div>
                            <label class="form-label">Harga Pokok Penjualan</label>
                            <input type="number" step="0.01" :name="`items[${index}][harga_pokok_penjualan]`" x-model="item.harga_pokok_penjualan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.harga_pokok_penjualan" x-show="errors[index]?.harga_pokok_penjualan"></p>
                        </div>
                        <div>
                            <label class="form-label">Total HPP</label>
                            <input type="text" x-model="(item.quantity * item.harga_pokok_penjualan).toLocaleString('id-ID')" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                            <input type="hidden" :name="`items[${index}][total_hpp]`" :value="item.quantity * item.harga_pokok_penjualan">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.total_hpp" x-show="errors[index]?.total_hpp"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Harga Jual</label>
                            <input type="number" step="0.01" :name="`items[${index}][harga_jual]`" x-model="item.harga_jual" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.harga_jual" x-show="errors[index]?.harga_jual"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Total Harga Jual</label>
                            <input type="text" x-model="(item.quantity * item.harga_jual).toLocaleString('id-ID')" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                            <input type="hidden" :name="`items[${index}][total_harga_jual]`" :value="item.quantity * item.harga_jual">
                            <p class="text-red-500 text-xs" x-text="errors[index]?.total_harga_jual" x-show="errors[index]?.total_harga_jual"></p>
                        </div>
                    </div>

                    {{-- Tombol hapus --}}
                    <div class="mt-3 text-right">
                        <button type="button" class="text-red-600 text-sm" @click="removeItem(index)">Hapus Item</button>
                    </div>
                </div>
            </template>

            <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm" @click="addItem">
                + Tambah Item
            </button>
        </div>

        {{-- DP & Sisa Pembayaran --}}
        <div class="grid grid-cols-2 gap-6 mt-6">
            <div>
                <label class="block text-sm font-medium">Down Payment</label>
                <input type="number" step="0.01" x-model="downPayment" name="down_payment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium">Sisa Pembayaran</label>
                <input type="text" x-model="sisaPembayaranDisplay" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                <input type="hidden" name="sisa_pembayaran" :value="sisaPembayaran">
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="mt-6">
            <button type="submit"
                class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700">
                Simpan Purchase Order
            </button>
        </div>
    </form>
</div>

<script>
function purchaseOrderForm() {
    return {
        items: [
            { kode_desain: '', desain_approve: '', po_press: '', po_print: '', po_press_print: '', bahan: '', ukuran: '', quantity: 1, harga_pokok_penjualan: 0, total_hpp: 0, harga_jual: 0, total_harga_jual: 0, fqc_us: '', fqc_la: '', fqc_jt: '', fqc_us_option: '', fqc_us_note: '', fqc_la_option: '', fqc_la_note: '', fqc_jt_option: '', fqc_jt_note: '' },
        ],
        downPayment: 0,
        errors: [],

        addItem() {
            this.items.push({ kode_desain: '', desain_approve: '', po_press: '', po_print: '', po_press_print: '', bahan: '', ukuran: '', quantity: 1, harga_pokok_penjualan: 0, total_hpp: 0, harga_jual: 0, total_harga_jual: 0, fqc_us: '', fqc_la: '', fqc_jt: '', fqc_us_option: '', fqc_us_note: '', fqc_la_option: '', fqc_la_note: '', fqc_jt_option: '', fqc_jt_note: '' });
            this.errors.push({});
        },
        removeItem(index) {
            this.items.splice(index, 1);
            this.errors.splice(index, 1);
        },
        validateForm() {
            this.errors = [];

            let valid = true;
            this.items.forEach((item, i) => {
                this.errors[i] = {};

                if (!item.kode_desain) {
                    this.errors[i].kode_desain = "Kode desain wajib diisi.";
                    valid = false;
                }
                if (!item.bahan) {
                    this.errors[i].bahan = "Bahan wajib diisi.";
                    valid = false;
                }
                if (!item.ukuran) {
                    this.errors[i].ukuran = "Ukuran wajib diisi.";
                    valid = false;
                }
                if (!item.quantity || item.quantity < 1) {
                    this.errors[i].quantity = "Quantity minimal 1.";
                    valid = false;
                }
            });

            if (valid) {
                this.$refs.poForm.submit(); // submit form kalau valid
            } else {
                alert("Mohon lengkapi semua field wajib sebelum menyimpan.");
            }
        },
        enforceExclusive(index, field) {
            let item = this.items[index];
            if (field === 'us' && item.fqc_us === 'Ya') {
                item.fqc_la = 'Tidak'; item.fqc_jt = 'Tidak';
            }
            if (field === 'la' && item.fqc_la === 'Ya') {
                item.fqc_us = 'Tidak'; item.fqc_jt = 'Tidak';
            }
            if (field === 'jt' && item.fqc_jt === 'Ya') {
                item.fqc_us = 'Tidak'; item.fqc_la = 'Tidak';
            }
        },
        get totalHPPAll() {
            return this.items.reduce((sum, item) => {
                return sum + (Number(item.quantity) * Number(item.harga_pokok_penjualan || 0));
            }, 0);
        },
        get totalHargaJualAll() {
            return this.items.reduce((sum, item) => {
                return sum + (Number(item.quantity) * Number(item.harga_jual || 0));
            }, 0);
        },
        get sisaPembayaran() {
            return this.totalHPPAll - Number(this.downPayment || 0);
        },
        get sisaPembayaranDisplay() {
            return this.sisaPembayaran.toLocaleString("id-ID", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }
}
</script>
@endsection
