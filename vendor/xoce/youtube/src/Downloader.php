<?php

namespace XoceYouTube;

use Exception;

class Downloader {
    private $apiUrl = "https://www.youtube.com/youtubei/v1/player?key=";
    private $apiKey; // Pública
	private $clientVersion = "19.08.35"; // Actualizada
    //private $clientVersion = "7.20220318.01.00"; // Actualizada
	
    function __construct($apiKey= null){
        if (is_null($apiKey) || $apiKey == '') {
            throw new Exception("Please Key pass a youtube video to fetch.");
            return (false);
        }
        $this->apiKey = $apiKey;
    }

    public function getVideoInfo($videoId) {
        $postData = [
            "context" => [
                "client" => [
				    "clientName" => "ANDROID",
                    //"clientName" => "ANDROID_EMBEDDED_PLAYER",
                    "clientVersion" => $this->clientVersion
                ]
            ],
            "videoId" => $videoId
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        $response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
		
        if (!$response) return false;
        $data = json_decode($response, true);

        if (($data['playabilityStatus']['status'] ?? '') !== "OK") return false;

        return $data;
    }

    public function getBestPlayableUrl($data) {
        if (empty($data['streamingData']['formats'])) return false;

        foreach ($data['streamingData']['formats'] as $format) {
            if (!empty($format['url'])) {
                return $format['url'];
            }

            if (!empty($format['signatureCipher'])) {
                parse_str($format['signatureCipher'], $cipher);
                if (!empty($cipher['url']) && !empty($cipher['s'])) {
                    return $cipher['url'] . "&sig=" . $cipher['s'];
                }
            }
        }

        return false;
    }
}
