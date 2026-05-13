<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send WhatsApp Message via API
     */
    public static function sendMessage($phone, $message)
    {
        // Ganti dengan logic / endpoint API WhatsApp yang ada di environment
        $apiUrl = env('WA_API_URL', 'https://api.whatsapp.example.com/send');
        $apiKey = env('WA_API_KEY', 'your-api-key');

        try {
            // Contoh implementasi menggunakan HTTP Client
            $response = Http::post($apiUrl, [
                'api_key' => $apiKey,
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp message sent to {$phone}");
                return true;
            }

            Log::error("Failed to send WhatsApp message: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp API Exception: " . $e->getMessage());
            return false;
        }
    }
}
