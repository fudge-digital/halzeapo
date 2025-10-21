@extends('layouts.dashboard')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow"
     x-data="purchaseOrderForm({{ $po->toJson() }})">
    <h1 class="text-xl font-bold mb-6">Edit Purchase Order</h1>

    <form x-ref="poForm"
          action="{{ route('purchase-orders.update', $po->id) }}"
          @submit.prevent="validateForm"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">

        @csrf
        @method('PUT')

        {{-- Nomor SPK --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">No SPK</label>
            <input type="text" name="no_spk"
                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-300 shadow-sm"
                   :value="form.no_spk" readonly>
        </div>

        {{-- Customer --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Customer</label>
            <input type="text" name="customer"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                   x-model="form.customer" required>
        </div>

        {{-- Tempat Produksi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Tempat Produksi</label>
            <input type="text" name="tempat_produksi"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                   x-model="form.tempat_produksi" required>
        </div>

        {{-- Repeater Items --}}
        <div>
            <h2 class="text-lg font-semibold mb-2">Detail Produksi</h2>

            <template x-for="(item, index) in items" :key="item.id ?? index">
                <div class="border rounded-lg p-4 mb-4 bg-gray-50">
                    <input type="hidden" :name="`items[${index}][id]`" x-model="item.id">

                    {{-- Jenis Produksi, Kode Desain, Upload Desain --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Jenis Produksi</label>
                            <input type="text" :name="`items[${index}][jenis_produksi]`"
                                   x-model="item.jenis_produksi"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Kode Desain</label>
                            <input type="text" :name="`items[${index}][kode_desain]`"
                                   x-model="item.kode_desain"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Upload Desain Approve</label>
                            <input type="file" :name="`items[${index}][desain_approve]`"
                                   accept="image/png,image/jpeg"
                                   class="mt-1 block w-full text-sm text-gray-600">
                            <template x-if="item.desain_approve">
                                <button type="button" @click="$store.imageModal.show('/' + item.desain_approve)"
                                   target="_blank"
                                   class="text-blue-500 text-sm block mt-1">Lihat Desain Lama</button>
                            </template>
                        </div>
                    </div>

                    {{-- Bahan & Ukuran --}}
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <label class="block text-sm font-medium">Bahan</label>
                            <input type="text" :name="`items[${index}][bahan]`"
                                   x-model="item.bahan"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Ukuran</label>
                            <input type="text" :name="`items[${index}][ukuran]`"
                                   x-model="item.ukuran"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <!-- PO Press / Print -->
                    <div class="grid grid-cols-3 gap-4 mt-3">
                        <div>
                            <label class="block text-sm font-medium">PO Press</label>
                            <input type="text" :name="`items[${index}][po_press]`" x-model="item.po_press" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">PO Print</label>
                            <input type="text" :name="`items[${index}][po_print]`" x-model="item.po_print" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">PO Press + Print</label>
                            <input type="text" :name="`items[${index}][po_press_print]`" x-model="item.po_press_print" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <!-- FQC Options -->
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium">FQC Ultrasonic</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    :name="`items[${index}][fqc_us]`"
                                    x-model="item.fqc_us"
                                    @change="enforceExclusive(index, 'us')"
                                    :disabled="item.fqc_la === 'Ya' || item.fqc_jt === 'Ya'">
                                <option value="">-- Pilih --</option>
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                            <textarea class="w-full rounded-md border-gray-300 mt-1"
                                    :name="`items[${index}][fqc_us_note]`"
                                    x-show="item.fqc_us === 'Ya'"
                                    x-model="item.fqc_us_note"
                                    placeholder="Keterangan Ultrasonic"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">FQC Laser Api</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    :name="`items[${index}][fqc_la]`"
                                    x-model="item.fqc_la"
                                    @change="enforceExclusive(index, 'la')"
                                    :disabled="item.fqc_us === 'Ya' || item.fqc_jt === 'Ya'">
                                <option value="">-- Pilih --</option>
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                            <textarea class="w-full rounded-md border-gray-300 mt-1"
                                    :name="`items[${index}][fqc_la_note]`"
                                    x-show="item.fqc_la === 'Ya'"
                                    x-model="item.fqc_la_note"
                                    placeholder="Keterangan Laser Api"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">FQC Jahit Tepi</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    :name="`items[${index}][fqc_jt]`"
                                    x-model="item.fqc_jt"
                                    @change="enforceExclusive(index, 'jt')"
                                    :disabled="item.fqc_us === 'Ya' || item.fqc_la === 'Ya'">
                                <option value="">-- Pilih --</option>
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                            <textarea class="w-full rounded-md border-gray-300 mt-1"
                                    :name="`items[${index}][fqc_jt_note]`"
                                    x-show="item.fqc_jt === 'Ya'"
                                    x-model="item.fqc_jt_note"
                                    placeholder="Keterangan Jahit Tepi"></textarea>
                        </div>
                    </div>

                    {{-- Quantity & Harga --}}
                    <div class="grid grid-cols-5 gap-4 mt-3">
                        <div>
                            <label>Qty</label>
                            <input type="number" min="1" :name="`items[${index}][quantity]`"
                                   x-model="item.quantity"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label>HPP</label>
                            <input type="number" step="0.01"
                                   :name="`items[${index}][harga_pokok_penjualan]`"
                                   x-model="item.harga_pokok_penjualan"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label>Total HPP</label>
                            <input type="text"
                                   x-model="(item.quantity * item.harga_pokok_penjualan).toLocaleString('id-ID')"
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                            <input type="hidden" :name="`items[${index}][total_hpp]`"
                                   :value="item.quantity * item.harga_pokok_penjualan">
                        </div>

                        <div>
                            <label>Harga Jual</label>
                            <input type="number" step="0.01"
                                   :name="`items[${index}][harga_jual]`"
                                   x-model="item.harga_jual"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label>Total Harga Jual</label>
                            <input type="text"
                                   x-model="(item.quantity * item.harga_jual).toLocaleString('id-ID')"
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                            <input type="hidden" :name="`items[${index}][total_harga_jual]`"
                                   :value="item.quantity * item.harga_jual">
                        </div>
                    </div>

                    <div class="mt-3 text-right">
                        <button type="button" class="text-red-600 text-sm" @click="removeItem(index)">Hapus Item</button>
                    </div>
                </div>
            </template>

            <button type="button"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm"
                    @click="addItem">+ Tambah Item</button>
        </div>

        {{-- DP & Sisa Pembayaran --}}
        <div class="grid grid-cols-2 gap-6 mt-6">
            <div>
                <label class="block text-sm font-medium">Tipe Down Payment</label>
                <select x-model="dpType"
                        x-init="dpType = '{{ old('down_payment_type', $po->down_payment_type ?? 'nominal') }}'"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="nominal">Nominal (Rp)</option>
                    <option value="persen">Persentase (%)</option>
                </select>

                <!-- ✅ Hidden input yang benar-benar dikirim -->
                <input type="hidden" name="down_payment_type" :value="dpType">
            </div>
            <div>
                <label class="block text-sm font-medium">Down Payment</label>
                <input type="number" step="0.01" name="down_payment"
                    x-model="downPayment"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" :placeholder="dpType === 'persen' ? 'Masukkan persen (contoh: 10)' : 'Masukkan nominal (contoh: 1000000)'">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mt-4">
            <div>
                <label>Total HPP</label>
                <input type="text" x-model="totalHPPDisplay"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
            </div>
            <div>
                <label>Total Harga Jual</label>
                <input type="text" x-model="totalHargaJualDisplay"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mt-4">
            <div>
                <label>Sisa Pembayaran (HPP)</label>
                <input type="text" x-model="sisaHPPDisplay"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                <input type="hidden" name="sisa_pembayaran_hpp" :value="sisaHPP">
            </div>

            <div>
                <label>Sisa Pembayaran (Harga Jual)</label>
                <input type="text" x-model="sisaHargaJualDisplay"
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                <input type="hidden" name="sisa_pembayaran_hargajual" :value="sisaHargaJual">
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="mt-6">
            <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700">
                Update Purchase Order
            </button>
        </div>
    </form>
</div>

<script>
function purchaseOrderForm(existingData = null) {
    const emptyItem = () => ({
        id: null,
        jenis_produksi: '',
        kode_desain: '',
        bahan: '',
        ukuran: '',
        quantity: 1,
        harga_pokok_penjualan: 0,
        harga_jual: 0,
        desain_approve: '',
        po_press: '',
        po_print: '',
        po_press_print: '',
        fqc_us: '',
        fqc_us_note: '',
        fqc_la: '',
        fqc_la_note: '',
        fqc_jt: '',
        fqc_jt_note: '',
    });

    const base = {
        no_spk: '',
        customer: '',
        tempat_produksi: '',
        items: [],
        dpType: 'nominal',
        downPayment: 0,
    };

    // Isi data dari server (mode edit)
    if (existingData) {
        base.no_spk = existingData.no_spk || '';
        base.customer = existingData.customer || '';
        base.tempat_produksi = existingData.tempat_produksi || '';
        base.downPayment = existingData.down_payment || 0;
        base.dpType = existingData.dp_type || 'nominal';

        // ✅ Pastikan tiap item punya `id` agar bisa di-update
        base.items = (existingData.items || []).map(i => ({
            id: i.id || null,
            jenis_produksi: i.jenis_produksi || '',
            kode_desain: i.kode_desain || '',
            bahan: i.bahan || '',
            ukuran: i.ukuran || '',
            quantity: i.quantity || 1,
            harga_pokok_penjualan: parseFloat(i.harga_pokok_penjualan) || 0,
            harga_jual: parseFloat(i.harga_jual) || 0,
            desain_approve: i.desain_approve || '',
            po_print: i.po_print || '',
            po_press: i.po_press || '',
            po_press_print: i.po_press_print || '',
            fqc_us: i.fqc_us_option || '',
            fqc_la: i.fqc_la_option || '',
            fqc_jt: i.fqc_jt_option || '',
        }));
    }

    return {
        form: base,
        items: base.items.length ? base.items : [emptyItem()],
        dpType: base.dpType,
        downPayment: base.downPayment,

        // ✅ Tambah item baru tanpa `id`
        addItem() {
            this.items.push(emptyItem());
        },

        // ✅ Hapus item dari array UI
        // (controller akan hapus dari DB karena id-nya hilang dari request)
        removeItem(i) {
            this.items.splice(i, 1);
        },

        enforceExclusive(index, field) {
            const item = this.items[index];
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

        // ===== Kalkulasi Dinamis =====
        get totalHPPAll() {
            return this.items.reduce((s, i) => s + (Number(i.quantity) * Number(i.harga_pokok_penjualan)), 0);
        },
        get totalHargaJualAll() {
            return this.items.reduce((s, i) => s + (Number(i.quantity) * Number(i.harga_jual)), 0);
        },

        get downPaymentHPP() {
            return this.dpType === 'persen'
                ? (this.totalHPPAll * (this.downPayment / 100))
                : this.downPayment;
        },
        get downPaymentHargaJual() {
            return this.dpType === 'persen'
                ? (this.totalHargaJualAll * (this.downPayment / 100))
                : this.downPayment;
        },

        parseDP() {
            const v = parseFloat(this.downPayment);
            return isNaN(v) ? 0 : v;
        },

        // 🔹 DP dalam nominal (Rupiah)
        get downPaymentValue() {
            const dp = this.parseDP();
            if (this.dpType === 'persen') {
                // Jika persen → hitung dari totalHargaJual
                return (this.totalHargaJualAll * (dp / 100)) || 0;
            }
            // Jika nominal → pakai langsung
            return dp;
        },

        get sisaHPP() {
            const dp = this.parseDP();

            if (this.dpType === 'persen') {
                // Persen: kurangi berdasarkan totalHargaJual × persentase
                return Math.max(this.totalHPPAll - (this.totalHargaJualAll * (dp / 100)), 0);
            }

            // Nominal: kurangi langsung nilai DP
            return Math.max(this.totalHPPAll - dp, 0);
        },
        get sisaHargaJual() {
            const dp = this.parseDP();

            if (this.dpType === 'persen') {
                return Math.max(this.totalHargaJualAll - (this.totalHargaJualAll * (dp / 100)), 0);
            }

            return Math.max(this.totalHargaJualAll - dp, 0);
        },

        // ===== Display Formatting =====
        get totalHPPDisplay() { return this.totalHPPAll.toLocaleString('id-ID'); },
        get totalHargaJualDisplay() { return this.totalHargaJualAll.toLocaleString('id-ID'); },
        get sisaHPPDisplay() { return this.sisaHPP.toLocaleString('id-ID'); },
        get sisaHargaJualDisplay() { return this.sisaHargaJual.toLocaleString('id-ID'); },

        // ===== Submit =====
        validateForm() {
            this.$refs.poForm.submit();
        }
    };
}

</script>
@endsection
