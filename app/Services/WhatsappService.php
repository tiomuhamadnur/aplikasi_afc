<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected $apiUrl;
    protected $accessToken;

    public function __construct()
    {
        $this->apiUrl = env('WHATSAPP_API_URL');
        $this->accessToken = env('WHATSAPP_API_TOKEN');
    }

    public function sendMessage(string $phoneNumber, string $messages)
    {
        $payload = [
            'to' => $phoneNumber,
            'type' => 'text',
            'text' => $messages,
            'caption' => 'Caption test',
            'url' => 'https://www.fnordware.com/superpng/pnggrad16rgb.png',
            'dataUri' => 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==',
            'fileName' => 'file.gif',
            'useTyping' => true,
            'skipBusy' => true,
            'replyId' => 'string',
            'access_token' => $this->accessToken, // akses token di body
        ];

        $response = Http::withToken($this->accessToken)
            ->acceptJson()
            ->post($this->apiUrl, $payload);

        if ($response->failed()) {
            return false;
        }

        return $response->body();
    }
}
