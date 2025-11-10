<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('wa_notification_button')) {
    /**
     * Generate WhatsApp notification button based on PO and user role.
     */
    function wa_notification_button($po)
    {
        $user = Auth::user();

        // Urutan pengiriman notifikasi antar role
        $roles = [
            'MARKETING' => 'FINANCE',
            'FINANCE'   => 'PRODUKSI',
            'PRODUKSI'  => 'SHIPPER',
            // 'SHIPPER' => 'CUSTOMER', // opsional
        ];

        $currentRole = strtoupper($user->role ?? '');
        $nextRole = $roles[$currentRole] ?? null;

        if (!$nextRole) {
            return ''; // tidak ada role tujuan
        }

        // Ambil user tujuan
        $targetUser = \App\Models\User::where('role', $nextRole)->first();

        // Jika user tujuan tidak ada
        if (!$targetUser) {
            return <<<HTML
                <div class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-600 rounded-lg border border-gray-300 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20h.01M12 4h.01M4 12h.01M20 12h.01M12 12h.01"/>
                    </svg>
                    User dengan role {$nextRole} belum terdaftar.
                </div>
            HTML;
        }

        // Jika user ada tapi belum punya nomor HP
        if (empty($targetUser->phone)) {
            return <<<HTML
                <div class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg border border-yellow-300 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0z"/>
                    </svg>
                    Nomor WhatsApp {$nextRole} belum tersedia.
                </div>
            HTML;
        }

        // Pesan sesuai role (tidak diubah sama sekali)
        $statusText = match ($currentRole) {
            'MARKETING' => 'telah dibuat pada',
            'FINANCE'   => 'telah disetujui Finance',
            'PRODUKSI'  => 'telah selesai diproduksi',
            'SHIPPER'   => 'telah dikirim',
            default     => 'telah diperbarui',
        };

        $statusProses = match ($currentRole) {
            'MARKETING' => 'disetujui untuk bisa dilanjutkan ke proses berikutnya.',
            'FINANCE'   => 'diproduksi dengan spesifikasi yang telah disetujui.',
            'PRODUKSI'  => 'telah siap untuk dikirim',
            default     => 'siap di proses ke tahap selanjutnya.',
        };

        // Format nomor & pesan WA
        $phone = '62' . ltrim($targetUser->phone, '0');
        $message = "Dear {$nextRole}, ada PO {$statusText} No SPK *{$po->no_spk}*. "
                    . "Mohon untuk dicek dan {$statusProses}. Terima kasih.";
        $waUrl = "https://wa.me/{$phone}?text=" . urlencode($message);

        // Tombol Tailwind CSS
        return <<<HTML
            <a href="{$waUrl}" target="_blank"
                class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow transition duration-150 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 fill-current" viewBox="0 0 24 24">
                    <path d="M20.52 3.48A11.86 11.86 0 0 0 12 0C5.37 0 .03 5.34.03 11.94c0 2.1.55 4.16 1.6 5.97L0 24l6.33-1.65a11.92 11.92 0 0 0 5.67 1.44h.01c6.63 0 11.97-5.34 11.97-11.94 0-3.19-1.25-6.18-3.46-8.37zM12 21.5a9.49 9.49 0 0 1-4.83-1.31l-.35-.2-3.76.98 1-3.67-.24-.38a9.48 9.48 0 0 1-1.44-5.08c0-5.24 4.27-9.51 9.52-9.51 2.54 0 4.93.99 6.72 2.77A9.48 9.48 0 0 1 21.5 12c0 5.24-4.27 9.5-9.5 9.5zm5.19-7.11c-.28-.14-1.66-.82-1.92-.91-.26-.1-.45-.14-.63.14-.18.27-.72.9-.88 1.09-.16.18-.32.21-.6.07-.28-.14-1.19-.44-2.26-1.4-.83-.74-1.39-1.64-1.55-1.92-.16-.27-.02-.41.12-.55.12-.12.28-.32.42-.48.14-.16.18-.27.28-.45.1-.18.05-.34-.02-.48-.07-.14-.63-1.52-.86-2.08-.22-.54-.45-.47-.63-.48h-.54c-.18 0-.48.07-.73.34-.25.27-.96.93-.96 2.26s.99 2.63 1.13 2.81c.14.18 1.96 2.99 4.75 4.19.67.29 1.19.46 1.6.59.67.21 1.28.18 1.77.11.54-.08 1.66-.68 1.9-1.34.24-.66.24-1.23.17-1.34-.07-.11-.25-.18-.52-.31z"/>
                </svg>
                Kirim WA ke {$nextRole}
            </a>
        HTML;
    }
}
