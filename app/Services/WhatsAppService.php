<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    /**
     * Send a WhatsApp message.
     * 
     * @param string $to Phone number (e.g., 628123...)
     * @param string $message The message content
     * @return bool
     */
    public function sendMessage(string $to, string $message): bool
    {
        // For development, we log the message
        Log::info("WhatsApp Message to {$to}: {$message}");

        // Example: Integration with a provider like Fonnte (Mocked)
        // $response = Http::post('https://api.fonnte.com/send', [
        //     'target' => $to,
        //     'message' => $message,
        //     'token' => config('services.whatsapp.token'),
        // ]);
        
        // return $response->successful();

        return true;
    }
}
