@extends('layouts.dashboard')

@section('content')
<div class="max-w-8xl mx-auto p-6">
    <div class="mb-4">
        <p class="text-gray-700">
            Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8 mb-8">

        <div class="card">
            <h3 class="font-semibold mb-2">Jumlah PO / Bulan</h3>
            <canvas id="poChart"></canvas>
        </div>

        <div class="card">
            <h3 class="font-semibold mb-2">Total Sales / Bulan</h3>
            <canvas id="salesChart"></canvas>
        </div>

        <div class="card">
            <h3 class="font-semibold mb-2">Approved vs Pending</h3>
            <canvas id="statusChart"></canvas>
        </div>

        <div class="card">
            <h3 class="font-semibold mb-2">DONE PRODUCTION / Bulan</h3>
            <canvas id="productionChart"></canvas>
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Purchase Order dibuat</h2>
            <p class="text-3xl font-bold text-blue-600">{{ $totalPO ?? '0' }}</p>
            <!-- <button href="{{ route('purchase-orders.index') }}" class="bg-sky-500 hover:bg-sky-600 mt-5 p-2 text-white text-sm rounded">Lihat Purchase Order</button> -->
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">PO Pending oleh Finance</h2>
            <p class="text-3xl font-bold text-red-600">{{ $pendingPO ?? '0' }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">PO Approved oleh Finance</h2>
            <p class="text-3xl font-bold text-green-600">{{ $approvedPO ?? '0' }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">PO Rejected oleh Finance</h2>
            <p class="text-3xl font-bold text-green-600">{{ $rejectedPO ?? '0' }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Dalam Antrian Produksi</h2>
            <p class="text-3xl font-bold text-green-600">{{ $queuePO ?? '0' }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mt-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">PO Pending Produksi</h2>
            <p class="text-3xl font-bold text-yellow-500">{{ $approvedFinancePOs ?? '0' }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">PO Dalam Proses Produksi</h2>
            <p class="text-3xl font-bold text-yellow-500">{{ $inprogPO ?? '0' }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">PO Selesai Produksi</h2>
            <p class="text-3xl font-bold text-green-600">{{ $completedPO ?? '0' }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">PO Siap Dikirim</h2>
            <p class="text-3xl font-bold text-green-600">{{ $readyshipPO ?? '0' }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">PO Terkirim</h2>
            <p class="text-3xl font-bold text-green-600">{{ $shippedPO ?? '0' }}</p>
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ======================
// JUMLAH PO
// ======================
new Chart(document.getElementById('poChart'), {
    type: 'line',
    data: {
        labels: @json($poPerBulan->pluck('bulan')),
        datasets: [{
            label: 'Jumlah PO',
            data: @json($poPerBulan->pluck('total')),
            tension: 0.4,
            borderWidth: 2
        }]
    }
});

// ======================
// TOTAL SALES
// ======================
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: @json($salesPerBulan->pluck('bulan')),
        datasets: [{
            label: 'Total Sales',
            data: @json($salesPerBulan->pluck('total_sales')),
            tension: 0.4,
            borderWidth: 2
        }]
    }
});

// ======================
// APPROVED vs PENDING
// ======================
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Approved', 'Pending'],
        datasets: [{
            data: [
                {{ $statusFinance->approved ?? 0 }},
                {{ $statusFinance->pending ?? 0 }}
            ]
        }]
    }
});

// ======================
// DONE PRODUCTION
// ======================
new Chart(document.getElementById('productionChart'), {
    type: 'bar',
    data: {
        labels: @json($doneProductionPerBulan->pluck('bulan')),
        datasets: [{
            label: 'DONE PRODUCTION',
            data: @json($doneProductionPerBulan->pluck('total'))
        }]
    }
});
</script>

<style>
    .card {
        background: white;
        padding: 20px;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,.06);
    }
</style>

@endsection
