<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonteeWhatsAppService
{
    protected $apiUrl;
    protected $apiKey;
    protected $from;

    public function __construct()
    {
        $this->apiUrl = env('FONTEE_API_URL');
        $this->apiKey = env('FONTEE_API_KEY');
        $this->from = env('FONTEE_WHATSAPP_FROM');
    }

    public function sendMessage($to, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($this->apiUrl, [
            'from' => $this->from,
            'to' => $to,
            'message' => $message,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to send WhatsApp message: ' . $response->body());
        }
    
        return true;
    }

    public function sendMessageCurl($to, $message) 
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
        'target' => $to,
        'message' => $message, 
        'countryCode' => '62', //optional
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: '. $this->apiKey,
        ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            throw new \Exception('Failed to send WhatsApp message: ' . $error_msg);
        }
    
        return true;
    }
}