<?php

session_start();
if (!isset($_GET['fileId'])) {
    echo "An Un-Expected Error !";
    die();
}

// file id
$fileId = htmlentities($_GET['fileId']);

// includes
require_once "app/php/config.php";
require_once "app/db/db_conn.php";
require_once "app/php/functions.php";
require_once "backblaze/client.php";


$fileInfo = $client->getFile([
    'FileId' => $fileId
]);

$fileSize = $fileInfo->getSize();
if (!$fileSize) {
    echo "Video Deleted Or Not Found !";
    die();
}

// get video info
$stmt = $conn->prepare("SELECT u.upload_id,u.upload_title, u.upload_des, u.upload_views, u.upload_thumbnail, u.upload_timestamp, ul.user_name, ul.user_profile, COUNT(ul.user_id) AS total_videos FROM uploads u JOIN users ul ON u.uploader_id = ul.user_id WHERE u.upload_pathid = '{$fileId}'");
$title = $des = $stat = $uname = "";
$totalVideos = 0;
$profileImg = "";
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
    $data = $res->fetch_assoc();
    $title = $data['upload_title'];
    $des = $data['upload_des'];
    $stat = "{$data['upload_views']} views &bullet; " . calcDiff($data['upload_timestamp'] . " ago");
    $uname = base64_decode($data['user_name']);
    $profileImg = $data['user_profile'];
    $totalVideos = $data['total_videos'];
}

function updateView($conn,$upload_id)
{
    $stmt = $conn->prepare("UPDATE uploads SET upload_views = upload_views + 1 WHERE upload_id = ?");
    $stmt->bind_param('s', $upload_id);
    $stmt->execute();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player</title>

    <!-- cdns -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- stylesheets -->
    <link rel="stylesheet" href="app/css/config.css">
    <link rel="stylesheet" href="app/css/header.css">
    <link rel="stylesheet" href="app/css/player.css">
</head>

<body>

    <!-- BODY -->
    <?php require_once "app/includes/header.php"; ?>
    <main class="vh-100">
        <div class="container-fluid">
            <div id="player">
                <div class="row m-0 g-0 row-gap-2">
                    <div class="col-md-9">
                        <div class="video-container position-relative bg-light">
                            <div class="position-absolute top-0 start-0 h-100 w-100 d-flex bg-dark loader-top">
                                <div class="m-auto text-center">
                                    <div class="buffer-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="84" height="84" fill="#eee">
                                            <path d="M18.364 5.63604L16.9497 7.05025C15.683 5.7835 13.933 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19C15.866 19 19 15.866 19 12H21C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C14.4853 3 16.7353 4.00736 18.364 5.63604Z"></path>
                                        </svg>
                                    </div>
                                    <h6 class="text-light">Loading Your Video, Please Wait</h6>
                                </div>
                            </div>
                            <video src="<?php echo "http://localhost/video_player/app/php/fetchVideo.php?fileId=$fileId&fileSize=$fileSize"
                                        ?>" id="video-src"></video>
                            <div class="video-controls position-absolute top-0 start-0 h-100 w-100 d-none">
                                <div class="h-100 d-flex justify-content-between flex-column">
                                    <div class="video-control-top p-2">
                                        <button class="btn player-btn d-block ms-auto rounded-5">
                                            <i class="fa-solid fa-angle-down"></i>
                                        </button>
                                    </div>
                                    <div class="video-control-btns">
                                        <div class="mx-auto">
                                            <div class="d-flex align-items-center justify-content-center gap-4 video-control-btns">
                                                <button class="btn btn-seek-back player-btn rounded-5">
                                                    <i class="fa-solid fa-backward"></i>
                                                    &nbsp;<span>+10</span>
                                                </button>
                                                <div class="buffer-icon d-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="84" height="84" fill="#eee">
                                                        <path d="M18.364 5.63604L16.9497 7.05025C15.683 5.7835 13.933 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19C15.866 19 19 15.866 19 12H21C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C14.4853 3 16.7353 4.00736 18.364 5.63604Z"></path>
                                                    </svg>
                                                </div>
                                                <button class="btn btn-play-video player-btn rounded-5">
                                                    <i class="fa-solid fa-play"></i>
                                                </button>
                                                <button class="btn btn-seek-forward player-btn rounded-5">
                                                    <span>+10</span>&nbsp;
                                                    <i class="fa-solid fa-forward"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="video-control-bottom text-light p-3">
                                        <div class="progress-outer bg-white position-relative">
                                            <div class="progress-inner h-100 position-absolute top-0 start-0"></div>
                                            <div class="porgress-end h-100 bg-light"></div>
                                        </div>

                                        <div class="d-flex justify-content-between pt-2">
                                            <div class="d-flex gap-1 video-time-control">
                                                <span class="video-current-time">0:00</span>
                                                <span>/</span>
                                                <span class="video-duration">0:00</span>
                                            </div>
                                            <div class="position-relative">
                                                <button class="btn btn-req-fullscreen player-btn d-block ms-auto rounded-5">
                                                    <i class="fa-solid fa-expand"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 ps-md-2">
                        <div class="video-title"><?php echo $title ?></div>
                        <div class="video-info text-muted pt-1">
                            <small><?php echo $stat ?></small>
                        </div>
                        <a href="http://localhost/video_player/app/user/videos.php?uname=<?php echo base64_encode($data['user_name']) ?>" class="d-block rounded-3 mt-3 py-2 px-3 bg-dark">
                            <div class="d-flex align-items-center">
                                <div class="video-uploader-img p-1 rounded-circle">
                                    <img src="images/Black-YouTube-logo.png" alt="#" class="bg-light rounded-circle" height="38px" width="38px">
                                </div>
                                <div class="w-100 text-light video-uploader-info">
                                    <div class="m-0 video-uploader-name"><?php echo $uname ?></div>
                                    <small><?php echo $totalVideos ?> videos</small>
                                </div>
                                <i class="fa-solid fa-angle-right text-light"></i>
                            </div>
                        </a>
                        <?php if (!empty($des)) {
                            echo "
                                <hr>
                                <div class='rounded-3 bg-light p-3' ondblclick='$(`.video-description`)[0].classList.toggle(`showless`)>
                                    <label class='text-muted'><b>Description</b></label>
                                    <div class='video-description showless'>$des</div>
                                </div>";
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="app/js/functions.js"></script>
    <script src="app/js/player/playerEvents.js"></script>
    <script src="app/js/player/dragEvents.js"></script>
    <script src="app/js/player/player.js"></script>
    <script src="app/js/search.js"></script>

    <!-- update views -->
    <?php updateView($conn, $data['upload_id']); ?>
</body>

</html>