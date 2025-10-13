@extends('layouts.dashboard')

@section('content')
<div class="max-w-8xl mx-auto p-6">
    <div class="mb-4">
        <p class="text-gray-700">
            Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span>
        </p>
    </div>

    <div class="flex items-center align-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Daftar Purchase Orders</h1>
        <div>
            @if(Auth::user()->role === 'MARKETING')
                <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded uppercase hover:bg-green-700 transition tracking-wider">
                    + Buat Purchase Order
                </a>
            @endif
                <!-- <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition uppercase tracking-wider">
                    Export
                </a> -->
        </div>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700 text-sm tracking-wide">
                    <th class="px-4 py-3 text-left">PO Dibuat</th>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left">Tempat Produksi</th>
                    <th class="px-4 py-3 text-left">No SPK</th>
                    <th class="px-4 py-3 text-left">Dibuat Oleh</th>
                    <th class="px-4 py-3 text-left">Status Finance</th>
                    <th class="px-4 py-3 text-left">Status Production</th>
                    <th class="px-4 py-3 text-left">Status Shipping</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                @forelse ($pos as $po)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">{{ $po->create ?? $po->created_at->format('d-m-Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $po->customer }}</td>
                        <td class="px-4 py-3">{{ $po->tempat_produksi ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $po->no_spk ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $po->creator->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-lg text-xs font-medium
                                @if($po->status === 'PENDING_FINANCE') bg-yellow-100 text-yellow-700
                                @elseif($po->status === 'APPROVED_FINANCE') bg-green-100 text-green-700
                                @elseif($po->status === 'REJECTED') bg-red-100 text-red-700
                                @endif">
                                {{ str_replace('_',' ',$po->status) ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-lg text-xs font-medium
                                @if($po->production_status === 'IN_PRODUCTION') bg-blue-100 text-blue-700
                                @elseif($po->production_status === 'QUEUE_PRODUCTION') bg-yellow-100 text-yellow-700
                                @elseif($po->production_status === 'PENDING_PRODUCTION') bg-purple-100 text-purple-700
                                @elseif($po->production_status === 'DONE_PRODUCTION') bg-green-200 text-green-700
                                @endif">
                                {{ str_replace('_',' ',$po->production_status) ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-lg text-xs font-medium
                                @if($po->shipping_status === 'READY_TO_SHIP') bg-yellow-100 text-yellow-700
                                @elseif($po->shipping_status === 'SHIPPED') bg-green-100 text-green-700
                                @endif">
                                {{ str_replace('_',' ',$po->shipping_status) ?? '-' }}
                            </span>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('purchase-orders.show', $po) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs font-medium rounded hover:bg-indigo-700 transition">
                                Detail
                            </a>
                            <a href="{{ route('purchase-orders.edit', $po->id) }}" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-6 text-center text-gray-500">Belum ada Purchase Order</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
