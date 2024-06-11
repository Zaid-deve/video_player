<?php

session_start();
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
} else $uid = -1;

require_once "app/php/config.php";
require_once "app/db/db_conn.php";
// require_once "backblaze/client.php";
require_once "app/php/functions.php";

$filesArray = [];
$videoGrid = '';
try {
    $stmt = $conn->prepare("SELECT uploads.upload_views,users.user_profile,users.user_name,upload_title,upload_timestamp, upload_pathid,upload_thumbnail,upload_duration FROM uploads JOIN users
                            ON uploads.uploader_id = users.user_id
                            WHERE upload_id NOT IN (SELECT hupload_id FROM watch_history WHERE huser_id = ? AND hwatch_time <> uploads.upload_duration)
                            ORDER BY upload_id
                            DESC LIMIT 10");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $videoGrid = "<div class='row row-gap-4 videos-grid content-grid'>";

        while ($row = $res->fetch_assoc()) {
            $title = $row['upload_title'];
            $views = $row['upload_views'];
            $fileid = $row['upload_pathid'];
            $duration = formatVideoDuration($row['upload_duration']);
            $thumbnail = $row['upload_thumbnail'];
            $diff = calcDiff($row['upload_timestamp']);
            $username = base64_decode($row['user_name']);
            $profile = empty($row['user_profile']) ? $baseUrl . 'images/blank-profile-picture-973460_960_720.webp' : $row['user_profile'];

            // get thumbnail from server
            $thumbURL = $baseUrl . 'images/thumbnail_failed_to_load_with_icon.png';
            // if(!empty($thumbnail)){
            //     $thumbURL = downloadFile($client, $thumbnail) ?? 'images/default.jpg';
            // }

            $videoGrid .= "<a class='col-12 col-sm-6 col-lg-4 text-dark grid-item' href='player.php?fileId={$fileid}'>
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

        $videoGrid .= "</div>";
    } else {
        $videoGrid =
            "<div class='text-center text-secondary pt-5'>
        <i class='fa-solid fa-clipboard-question' style='font-size:100px'></i>
            <h4 class='mt-4'>Nothing To Show At A Movement !</h4>
         </div>";
    }
} catch (Exception $e) {
    echo "Something Went Wrong ! [Error - " . $e->getCode() . "]";
    die();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <!-- cdns -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- stylesheets -->
    <link rel="stylesheet" href="app/css/config.css">
    <link rel="stylesheet" href="app/css/header.css">
    <link rel="stylesheet" href="app/css/home.css">


</head>

<body>

    <!-- BODY -->
    <?php require_once "app/includes/header.php"; ?>

    <!-- main content -->
    <main>
        <div class="container-fluid p-0">
            <div class="row m-0">
                <div class="col-md-3 bg-white pt-3 pb-5 menu-left d-md-block d-none">
                    <?php require "app/includes/menu.php" ?>
                </div>
                <div class="col pb-4">
                    <?php echo $videoGrid; ?>
                </div>
            </div>
        </div>
    </main>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="app/js/functions.js"></script>
    <script src="app/js/search.js"></script>

</body>

</html>