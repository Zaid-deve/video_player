<?php

// includes
require_once "../php/config.php";
require_once "../db/db_conn.php";
require_once "../php/functions.php";
require_once "../../backblaze/client.php";

$uname = "";
$profile_img = $uid = "";
$totalVideos = 0;

// get uid
session_start();
$clouse = "";
if (isset($_GET['user'])) {
    $uname = $_GET['user'];
    if (!base64_decode($uname, true)) {
        die("Something Went Wrong Or User Not Found !");
    }
    $clouse = "user_name = '" . base64_decode($uname) . "'";
} else {
    if (!isset($_SESSION['user_id'])) {
        header("Location:login.php?r=videos");
        die();
    }
    $uid = $_SESSION['user_id'];
    $clouse = "user_id = '" . $uid . "'";
}

// get user
$qry = $conn->query("SELECT user_id,user_name,user_profile FROM users WHERE $clouse");
if (!$qry || !$qry->num_rows) {
    die("Something Went Wrong");
}
$data = $qry->fetch_assoc();
$upid = $data['user_id'];
$username = base64_decode($data['user_name']);
$profile =  $baseUrl . "images/default.jpg";

$recentVideos = 0;
$qry = $conn->query("SELECT * FROM uploads WHERE uploader_id = $upid ORDER BY upload_id DESC LIMIT 4");
$recentVideosGrid = "";
$otherVideosGrid = "";

if ($qry && $qry->num_rows > 0) {
    $recentVideos = $qry->num_rows;
    $recentVideosGrid = "<div class='d-flex gap-3 pt-3 recent-videos-flex'>";
    while ($row = $qry->fetch_assoc()) {
        $title = $row['upload_title'];
        $views = $row['upload_views'];
        $fileid = $row['upload_pathid'];
        $duration = formatVideoDuration($row['upload_duration']);
        $thumbnail = $row['upload_thumbnail'];
        $diff = calcDiff($row['upload_timestamp']);

        // get thumbnail from server
        $thumbURL = $baseUrl . 'images/default.jpg';
        // if (!empty($thumbnail)) {
        //     $thumbURL = downloadFile($client, $thumbnail) ?? 'images/default.jpg';
        // }

        $recentVideosGrid .= "<a class='flex-shrink-0 text-dark' href='../../player.php?fileId={$fileid}'>
                                 <div class='video-thumbnail position-relative'>
                                     <img src='$thumbURL' alt='#' id='thumbnail-src' class='rounded-4'>
                                     <div class='position-absolute bottom-0 end-0 p-2'>
                                         <div class='bg-dark text-light rounded-2 px-2'><b><small>$duration</small></b></div>
                                     </div>
                                 </div>
                                 <div class='video-info pt-3'>
                                     <div class='d-flex gap-2'>
                                         <div class='video-uploader'>
                                             <img src='$profile' alt='#' id='uploader-src' class='rounded-circle'>
                                         </div>
                                         <div class='uploader-info w-100'>
                                             <div class='video-title'>$title</div>
                                             <div class='uploader-channel-name text-secondary'>$username</div>
                                             <div class='d-flex gap-1'>
                                                 <small class='video-views'>{$views} views</small>
                                                 <span>&bullet;</span>
                                                 <small class='video-upload-time'>$diff</small>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </a>";
    }
    $recentVideosGrid .= "</div>";
} else {
    $recentVideosGrid = "<h3>No Recent Videos</h3>";
}

// fetch other videos
$qry = $conn->query("SELECT * FROM uploads WHERE uploader_id = $upid ORDER BY upload_id DESC LIMIT $recentVideos,10");

if ($qry && $qry->num_rows > 0) {
    $otherVideosGrid = "<div class='row row-gap-4 pt-3 videos-grid'>";
    while ($row = $qry->fetch_assoc()) {
        $title = $row['upload_title'];
        $views = $row['upload_views'];
        $fileid = $row['upload_pathid'];
        $duration = formatVideoDuration($row['upload_duration']);
        $thumbnail = $row['upload_thumbnail'];
        $diff = calcDiff($row['upload_timestamp']);

        $otherVideosGrid .= "<a class='col-12 col-sm-6 col-lg-4 text-dark' href='../../player.php?fileId={$fileid}'>
                                 <div class='video-thumbnail position-relative'>
                                     <img src='{$baseUrl}images/default.jpg' alt='#' id='thumbnail-src' class='rounded-4'>
                                     <div class='position-absolute bottom-0 end-0 p-2'>
                                         <div class='bg-dark text-light rounded-2 px-2'><b><small>$duration</small></b></div>
                                     </div>
                                 </div>
                                 <div class='video-info pt-3'>
                                     <div class='d-flex gap-2'>
                                         <div class='video-uploader'>
                                             <img src='$profile' alt='#' id='uploader-src' class='rounded-circle'>
                                         </div>
                                         <div class='uploader-info w-100'>
                                             <div class='video-title'>$title</div>
                                             <div class='uploader-channel-name text-secondary'>$username</div>
                                             <div class='d-flex gap-1'>
                                                 <small class='video-views'>{$views} views</small>
                                                 <span>&bullet;</span>
                                                 <small class='video-upload-time'>$diff</small>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </a>";
    }
    $otherVideosGrid .= "</div>";
} else {
    $otherVideosGrid = "<h3>No Videos Yet !</h3>";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>videos</title>

    <!-- cdns -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- stylesheets -->
    <link rel="stylesheet" href="../css/config.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/videos.css">
</head>

<body>

    <?php require_once "../includes/header.php"; ?>

    <main>
        <div class="container-fluid">
            <div class="row m-0 g-0 row-gap-4">
                <div class="col-md-4">
                    <?php require_once "../includes/menu.php"; ?>
                </div>
                <div class="col-md-8 ps-md-4">
                    <label><b>Profile</b></label>
                    <div class="d-block rounded-3 mt-3 py-2 px-3 bg-dark">
                        <div class="d-flex align-items-center">
                            <div class="video-uploader-img p-1 rounded-circle">
                                <img src="../../images/Black-YouTube-logo.png" alt="#" class="bg-light rounded-circle" height="45px" width="45px">
                            </div>
                            <div class="w-100 text-light video-uploader-info">
                                <div class="m-0 video-uploader-name"><?php echo $uname ?></div>
                                <small><?php echo $totalVideos ?> videos</small>
                            </div>
                            <!-- <i class="fa-solid fa-angle-right text-light"></i> -->
                        </div>
                    </div>

                    <!-- videos recent section -->
                    <div class="mt-3">
                        <label><b>Recent</b></label>
                        <?php echo $recentVideosGrid; ?>
                    </div>

                    <div class="mt-3">
                        <label><b>Other Videos</b></label>
                        <?php echo $otherVideosGrid; ?>
                    </div>
                </div>

                <!-- videos others section -->
            </div>
        </div>
        </div>
    </main>

</body>

</html>