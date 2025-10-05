@extends('layouts.dashboard')

@section('content')
<div class="max-w-8xl mx-auto p-6">
    <div class="mb-4">
        <p class="text-gray-700">
            Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span>
        </p>
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
@endsection
