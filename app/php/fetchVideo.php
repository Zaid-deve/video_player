<?php


// includes
require_once "config.php";
require_once "../../backblaze/client.php";

// file
$fileId = $_GET['fileId'];
$fileSize = $_GET['fileSize'];

// range header
$rangeStart = 0;
$rangeEnd = min($rangeStart + 1024 * 1024, $fileSize);
if (isset($_SERVER['HTTP_RANGE'])) {
    $range = str_replace('bytes=', '', $_SERVER['HTTP_RANGE']);
    $range = explode("-", $range);
    $rangeStart = $range[0] ?? 0;
    $rangeEnd = min($rangeStart + (1024 * 1024), $fileSize - 1);
}

$rangeLength = $rangeEnd - $rangeStart;


// headers
header("HTTP/1.1 206 Partial Content");
header("Content-Type: video/mp4");
header("Content-Length: $rangeLength");
header("Content-Range: bytes $rangeStart-$rangeEnd/$fileSize");
header("Accept-Ranges: bytes");


// download video from b2
$content = $client->download([
    'FileId' => $fileId,
    'Headers' => [
        'Range' => "bytes=$rangeStart-$rangeEnd"
    ]
]);

echo $content;