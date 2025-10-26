@extends('layouts.dashboard')

@section('content')
<div class="max-w-8xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-6">Purchase Order Logs</h1>

    <table class="min-w-full bg-white border">
        <thead>
            <tr class="bg-gray-100 border-b">
                <th class="py-2 px-4 text-left">Tanggal</th>
                <th class="py-2 px-4 text-left">User</th>
                <th class="py-2 px-4 text-left">Nomor SPK</th>
                <th class="py-2 px-4 text-left">Aksi</th>
                <th class="py-2 px-4 text-left">Perubahan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
            <tr class="border-b">
                <td class="py-2 px-4">{{ $log->created_at->format('d M Y H:i') }}</td>
                <td class="py-2 px-4">{{ $log->user->name ?? '-' }}</td>
                <td class="py-2 px-4">{{ $log->new->no_spk ?? '-' }}</td>
                <td class="py-2 px-4">-</td>
                <td class="py-2 px-4">
                    <!-- @if ($log->action === 'updated')
                        <details class="bg-gray-50 p-2 rounded text-xs">
                            <summary class="cursor-pointer font-medium">Lihat detail</summary>
                            <div class="mt-2">
                                <strong>Old:</strong>
                                <pre>{{ json_encode($log->old, JSON_PRETTY_PRINT) }}</pre>
                                <strong>New:</strong>
                                <pre>{{ json_encode($log->new, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </details>
                    @else
                        <pre class="bg-gray-50 p-2 rounded text-xs">{{ json_encode($log->new ?? $log->old, JSON_PRETTY_PRINT) }}</pre>
                    @endif -->
                    <span class="bg-red-500 text-white rounded-lg py-2 px-2">{{ $log->action }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
