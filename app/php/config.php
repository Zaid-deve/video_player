<?php

$baseUrl = "";
$serverName = $_SERVER['SERVER_NAME'];
$root = $_SERVER['DOCUMENT_ROOT'] . '/video_player/';

if ($serverName === "localhost") {
    $baseUrl = "http://localhost/video_player/";
} else {
    $baseUrl = "https://" . $serverName . "/";
}

?>