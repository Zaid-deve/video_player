<?php

session_start();
$uid = $_SESSION['user_id'] ?? null;
if (!$uid) {
    die();
}

if (isset($_GET['fileId'])) {
    require_once "../db/db_conn.php";

    $fileId = $conn->real_escape_string($_GET['fileId']);
    $duration = $conn->real_escape_string($_GET['duration']);
    $watch = $conn->real_escape_string($_GET['watch'] ?? '') || null;
    if ($watch) {
        $watch = formatVideoDuration(intval($watch));
        if (intval($duration) - $watch) {
            $watch = $duration;
        }
    }

    $qry = "INSERT INTO watch_history (hupload_id, huser_id, hwatch_time) 
            VALUES ((SELECT uploads.upload_id FROM uploads WHERE uploads.upload_pathid = ?),?,?);";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("sss", $fileId, $uid, $watch);
    $stmt->execute();
}
