<?php

namespace App\Helpers;

class WhatsAppHelper
{
    public static function formatMessage(array $data)
    {
        $enter = "\n";
        $div = '=============================';

        $gender = $data[0];
        $name = $data[1];
        $departemen = $data[2];
        $seksi = $data[3];
        $jumlah = $data[4];
        $url = $data[5];

        $message = '🔴 *AFC APP NOTIFICATION:* ' . $enter . $enter . $enter .
            'Dear ' . $gender .' *' . $name . '*,' . $enter . $enter.
            'Sebagai informasi, terdapat *Data Permit* yang akan *Expired* dan perlu ditindak lanjuti dengan detail sebagai berikut:' . $enter . $enter .
            $div . $enter . $enter .
            '*Departemen :*' . $enter .
            $departemen . $enter . $enter .
            '*Seksi :*' . $enter .
            $seksi . $enter . $enter .
            '*Jumlah :*' . $enter .
            $jumlah . ' permit' .  $enter . $enter .
            '*URL :*' . $enter .
            $url . $enter . $enter .
            $div . $enter . $enter .
            '_Regards,_' . $enter . $enter .
            '*ExoBOT*' .
            $enter . $enter . $enter . $enter;

        return $message;
    }

    public static function sendNotification($phoneNumber, $message)
    {
        $apiUrl = env('WHATSAPP_API_URL');
        $token = env('WHATSAPP_API_TOKEN');

        $data = [
            'target' => $phoneNumber,
            'message' => $message,
            'countryCode' => '62',
        ];

        $headers = [
            'Authorization: ' . $token,
        ];

        return self::sendRequest($apiUrl, 'POST', $data, $headers);
    }

    private static function sendRequest($url, $method, $data = [], $headers = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
