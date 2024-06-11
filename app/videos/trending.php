<?php

session_start();

require_once "../php/config.php";
require_once "../db/db_conn.php";
// require_once "backblaze/client.php";
require_once "../php/functions.php";


// get trending files

$output = "";
$label = 1;
$labelText = " &bullet; #1";
try {
    $stmt = $conn->prepare("SELECT DISTINCT upload_title,upload_views,upload_pathid,upload_duration,upload_thumbnail,upload_timestamp,user_name,user_profile FROM uploads 
                            JOIN users
                            WHERE upload_timestamp >= NOW() - INTERVAL 1 DAY AND upload_views >= 5");
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $output = "<div class='row row-gap-4 videos-grid'>";

        while ($row = $res->fetch_assoc()) {
            $title = $row['upload_title'];
            $views = $row['upload_views'];
            $fileid = $row['upload_pathid'];
            $duration = formatVideoDuration($row['upload_duration']);
            $thumbnail = $row['upload_thumbnail'];
            $diff = calcDiff($row['upload_timestamp']);
            $username = base64_decode($row['user_name']);
            $profile = empty($row['user_profile']) ? $baseUrl . 'images/blank-profile-picture-973460_960_720.webp' : $row['user_profile'];
            $thumbURL = $baseUrl . 'images/default.jpg';

            $output .= "<a class='col-12 col-sm-6 col-lg-4 text-dark' href='../../player.php?fileId={$fileid}'>
                                <div class='video-thumbnail position-relative'>
                                    <img src='$thumbURL' alt='#' id='thumbnail-src' class='rounded-4'>
                                    <div class='position-absolute bottom-0 end-0 p-2'>
                                        <div class='bg-dark text-light rounded-2 px-2'><b><small>$duration <span class='text-warning'>$labelText</span></small></b></div>
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
            if ($label < 3) {
                $label++;
                $labelText = " &bullet; #$label";
            } else {
                $labelText = "";
            }
        }

        $output .= "</div>";
    } else {
        $output =
            "<div class='text-center text-secondary pt-5'>
               <i class='fa-solid fa-clipboard-question' style='font-size:100px'></i>
               <h4 class='mt-4'>Nothing To Show At A Movement !</h4>
            </div>";
    }
} catch (Exception $e) {
    $output = "Something Went Wrong ! [Error - " . $e->getCode() . "]";
}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending Videos</title>

    <!-- cdns -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- stylesheets -->
    <link rel="stylesheet" href="../css/config.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/home.css">

</head>

<body>

    <!-- BODY -->

    <?php require "../includes/header.php"; ?>

    <!-- MAIN -->
    <main>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 menu-left d-md-block d-none">
                    <?php require "../includes/menu.php" ?>
                </div>
                <div class="col pt-3 pt-md-0 content-grid">
                    <?php echo $output; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- SCRIPTS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../js/functions.js"></script>
    <script src="../js/search.js"></script>

</body>

</html>