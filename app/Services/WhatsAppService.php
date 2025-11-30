<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $endpoint;
    protected $apiKey;

    public function __construct()
    {
        // Ambil pengaturan API saat Service diinisialisasi
        $settings = Setting::whereIn('key', ['wa_api_endpoint', 'wa_api_key'])
                           ->pluck('value', 'key');
                           
        $this->endpoint = $settings['wa_api_endpoint'] ?? null;
        $this->apiKey = $settings['wa_api_key'] ?? null;
    }

    /**
     * Mengirim notifikasi WhatsApp ke nomor tujuan.
     *
     * @param string $toPhoneNumber Nomor telepon tujuan (misal: 62812xxxxxx)
     * @param string $message Isi pesan yang akan dikirim
     * @return bool
     */
    public function sendNotification(string $toPhoneNumber, string $message): bool
    {
        if (empty($this->endpoint) || empty($this->apiKey)) {
            Log::warning("WhatsApp API settings are incomplete. Skipping notification.");
            return false; // Gagal jika endpoint/key belum diatur
        }
        
        // Bersihkan nomor telepon dan pastikan format internasional (62xxxx)
        $cleanNumber = preg_replace('/^08/', '628', $toPhoneNumber);
        
        // Struktur data yang dikirim ke API WhatsApp (Sesuaikan jika API Anda berbeda)
        $payload = [
            'to' => $cleanNumber,
            'message' => $message,
            'api_key' => $this->apiKey,
            // Tambahkan parameter lain sesuai kebutuhan API Anda
        ];

        try {
            $response = Http::post($this->endpoint, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp Notification sent successfully to: {$toPhoneNumber}");
                return true;
            }

            Log::error("WhatsApp Notification failed. Response: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp API connection error: " . $e->getMessage());
            return false;
        }
    }
}