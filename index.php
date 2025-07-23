<?php
require "vendor/autoload.php";

use XoceYouTube\Downloader;
use XoceYouTube\Streamer;

header('Content-Type: application/json');

try {
    if (empty($_GET['id'])) {
        throw new Exception("Falta parámetro id");
    }

    $videoId = $_GET['id'];

    $youtube = new Downloader("API KEY");

    $videoInfo = $youtube->getVideoInfo($videoId);

    if (!$videoInfo) {
        throw new Exception("No se pudo obtener información del video");
    }

    $url = $youtube->getBestPlayableUrl($videoInfo);
    if (!$url) {
        throw new Exception("No se encontró un formato reproducible");
    }

    // ¿Stream directo o proxy?
    if (!empty($_GET['stream']) && $_GET['stream'] == "1") {
        $streamer = new Streamer();
        $streamer->stream($url);
        exit;
    }

    // ¿Stream directo o proxy?
    if (!empty($_GET['stream']) && $_GET['stream'] == "1") {
        $streamer = new Streamer();
        $streamer->stream($url);
        exit;
    }

    echo json_encode([
        "status" => "ok",
        "videoId" => $videoId,
        "title" => $videoInfo['videoDetails']['title'] ?? "Desconocido",
        "url" => $url
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}