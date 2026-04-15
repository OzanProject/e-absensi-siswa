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
        
        // Format spesifik untuk Fonnte API
        // Catatan: Karena kita sudah memaksa ganti awalan 08 jadi 628, 
        // JANGAN MENGIRIMKAN param 'countryCode', supaya API tidak double nambah 62 jadi 62628...
        $payload = [
            'target' => $cleanNumber,
            'message' => $message,
        ];

        try {
            // Fonnte biasanya merespon dengan kode 200 meskipun gagal (misal: device disconnect/token salah)
            // Jadi kita harus membaca isi JSON-nya.
            // Selain itu agar stabil, kita kirimkan data sbg url-encoded/form.
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey
            ])->asForm()->post($this->endpoint, $payload);

            $resData = $response->json();

            // Cek apakah HTTP Sukses DAN status dari Fonnte adalah true
            if ($response->successful() && isset($resData['status']) && $resData['status'] === true) {
                Log::info("WhatsApp Notification sent successfully to: {$toPhoneNumber}");
                return true;
            }

            // Jika statusnya false atau ada error reason, catat alasan lengkapnya
            $errorReason = $resData['reason'] ?? $resData['detail'] ?? $response->body();
            Log::error("WhatsApp Notification failed. Reason: " . $errorReason);
            
            // Lemparkan exception supaya bisa ditangkap di controller jika diperlukan
            throw new \Exception("Fonnte Error: " . $errorReason);

        } catch (\Exception $e) {
            Log::error("WhatsApp API connection error: " . $e->getMessage());
            return false;
        }
    }
}