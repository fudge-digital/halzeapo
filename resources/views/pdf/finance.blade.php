<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Finance - {{ $po->no_spk }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { font-size: 16px; font-weight: bold; margin-bottom: 20px; text-align:left; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; word-wrap: break-word; }
        th { background-color: #f9f9f9; }
        .logo { width: 120px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('storage/images/HALZEA-LOGO.png') }}" alt="Logo" class="logo">
    </div>

    <p><strong>No SPK:</strong> {{ $po->no_spk ?? '-' }}</p>
    <p><strong>No Invoice:</strong> {{ $po->no_invoice ?? '-' }}</p>
    <p><strong>Customer:</strong> {{ $po->customer }}</p>
    <p><strong>Tempat Produksi:</strong> {{ $po->tempat_produksi ?? 'N/A' }}</p>

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
                <td style="text-align:center;">{{ $item->quantity ?? 'N/A' }} pcs</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" style="text-align: right;">Total Quantity</th>
                <th style="text-align:center;">
                    {{ $po->items->sum('quantity') ?? '0' }} pcs
                </th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
