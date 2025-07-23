<?php

namespace XoceYouTube;

use Exception;

class Streamer {
    public function stream($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 8192);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: Mozilla/5.0']);

        // Reenviar headers básicos
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $header) {
            $len = strlen($header);
            $header = trim($header);
            if (stripos($header, 'content-type:') === 0 ||
                stripos($header, 'content-length:') === 0 ||
                stripos($header, 'accept-ranges:') === 0) {
                header($header);
            }
            return $len;
        });

        $success = curl_exec($ch);
        if ($success === false) {
            http_response_code(502);
            echo "Error al transmitir video";
        }
        curl_close($ch);
    }
}