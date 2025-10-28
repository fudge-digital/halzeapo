<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Marketing - {{ $po->no_spk }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { font-size: 12px; font-weight: bold; margin-bottom: 20px; text-align:left; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; word-wrap: break-word; }
        th { background-color: #f9f9f9; }
        .logo { width: 100px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('storage/images/LogoHalz-Small.png') }}" alt="Logo" class="logo">
        <br>
        Customer Invoice
    </div>

    <p><strong>No SPK:</strong> {{ $po->no_spk ?? '-' }}</p>
    <p><strong>Nama Customer:</strong> {{ $po->customer ?? 'N/A' }}</p>
    <p><strong>Tanggal Invoice:</strong> {{ optional($po->created_at)->format('d-m-Y') ?? '-' }}</p>
    <p></p>

    <table>
        <colgroup>
            <col style="width: 15%">
            <col style="width: 15%">
            <col style="width: 15%">
            <col style="width: 10%">
            <col style="width: 20%">
            <col style="width: 15%">
            <col style="width: 10%">
        </colgroup>
        <thead>
            <tr>
                <th>Jenis Produk</th>
                <th>Kode Desain</th>
                <th>Bahan</th>
                <th>Ukuran</th>
                <th>Produksi</th>
                <th>Finishing-QC</th>
                <th>Harga Satuan</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->items as $item)
            <tr>
                <td>{{ $item->jenis_produksi ?? 'N/A' }}</td>
                <td>{{ $item->kode_desain ?? 'N/A' }}</td>
                <td>{{ $item->bahan ?? 'N/A' }}</td>
                <td>{{ $item->ukuran ?? 'N/A' }}</td>
                <td>
                    {{ $item->po_press ? 'Press: '.$item->po_press.' ' : '' }}
                    {{ $item->po_print ? 'Print: '.$item->po_print.' ' : '' }}
                    {{ $item->po_press_print ? 'Press/Print: '.$item->po_press_print : '' }}
                </td>
                <td>
                    {{ $item->fqc_us ? $item->fqc_us.' ' : '' }}
                    {{ $item->fqc_la ? $item->fqc_la.' ' : '' }}
                    {{ $item->fqc_jt ? $item->fqc_jt : '' }}
                </td>
                <td>Rp {{ number_format($item->harga_jual) ?? '0' }}</td>
                <td>{{ $item->quantity ?? 'N/A' }} pcs</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr></tr>
            <tr>
                <th colspan="7" style="text-align: right;">Harga Total</th>
                <th style="text-align:right;">
                    Rp. {{ number_format($po->total_harga_jual) ?? '0' }}
                </th>
            </tr>
            <tr>
                <th colspan="7" style="text-align: right;">Down Payment</th>
                <th style="text-align:right;">
                    @if ($po->down_payment_type === 'persen')
                        @php
                            $persenDPHPP = $po->total_hpp > 0 
                                ? round(($po->down_payment / $po->total_hpp) * 100, 2)
                                : 0;
                        @endphp
                        {{ $persenDPHPP }}%
                    @else
                        Rp {{ number_format($po->down_payment, 0, ',', '.') }}
                    @endif
                </th>
            </tr>
            <tr>
                <th colspan="7" style="text-align: right;">Sisa Pembayaran</th>
                <th style="text-align:right;">
                    Rp. {{ number_format($po->sisa_pembayaran_hargajual) ?? '0' }}
                </th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
