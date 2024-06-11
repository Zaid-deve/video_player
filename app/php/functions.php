<?php

// get file from b2
function getFile($client, $filename)
{
    $fileList = $client->listFiles([
        'BucketId' => 'f76a1771e9c7b9dd82f30812'
    ]);

    foreach ($fileList as $file) {
        $fname = $file->getName();
        if ($fname == $filename) {
            return $fname;
        }
    }
}

// upload file to b2
function uploadToB2($client, $bucket, $file)
{
    try {
        $fileContents = file_get_contents($file['tmp_name']);

        $fileObj = $client->upload([
            'BucketName' => $bucket,
            'FileName' => $file['name'],
            'Body' => $fileContents,
        ]);
        return $fileObj->getId();
    } catch (Exception $e) {
        return false;
    }
}

function fetchFiles($client, $filter = null)
{
    $files = $client->listFiles([
        'BucketId' => 'f76a1771e9c7b9dd82f30812'
    ]);

    if (!$files) return [];

    if ($filter) {
        return array_filter($files, function ($file) use ($filter) {
            return $file->getName() === $filter;
        });
    }

    return $files;
}

function calcDiff($timestamp)
{
    $curr = new DateTime('now');
    $new = new DateTime($timestamp);
    $diff = $new->diff($curr);
    $formats = [
        'h' => 'hours',
        'm' => 'months',
        'y' => 'years',
        'd' => 'days',
        'i' => 'minutes',
        's' => 'seconds'
    ];

    foreach ($diff as $d => $di) {
        if ($di > 0 || $d == 's') return $di . " {$formats[$d]}" . ' ago';
    }
}

function downloadFile($client, $id)
{
    global $baseUrl;
    try {
        $thumb = $client->download(['FileId' => $id]);
        $data = base64_encode($thumb);
        $dataurl = "data:image/jpeg;base64," . $data;
        return $dataurl;
    } catch (Exception $e) {
        return null;
    }
}


function formatVideoDuration($durationInSeconds) {
    $hours = floor($durationInSeconds / 3600);
    $minutes = floor(($durationInSeconds % 3600) / 60);
    $seconds = $durationInSeconds % 60;

    $formattedDuration = "";
    if ($hours > 0) {
        $formattedDuration .= str_pad($hours, 2, '0', STR_PAD_LEFT) . ':';
    }


    $formattedDuration .= str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':';
    $formattedDuration .= str_pad($seconds, 2, '0', STR_PAD_LEFT);

    return $formattedDuration;
}