<?php

use GuzzleHttp\Promise\Is;
use GuzzleHttp\Psr7\UploadedFile;



session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location:login.php");
    die();
}
$uid = $_SESSION['user_id'];


$errors = [];
$isFileUploaded = false;
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once "../php/config.php";
    require_once "../db/db_conn.php";
    require_once "../../backblaze/client.php";
    require_once "../php/functions.php";

    // files
    $file = $_FILES['file'];
    $thumbnail = $_FILES['thumbnail'];
    $title = $conn->real_escape_string(htmlentities($_POST['title']));
    $des = $conn->real_escape_string(htmlentities($_POST['des']));
    $duration = $conn->real_escape_string($_POST['fileDuration']);

    $title = trim(substr($title, 0, 100));
    $des = trim(substr($des, 0, 500));

    // validate
    $uploadErr = "";
    $maxFileSize = (1024 * 1024) * 10;
    $maxThumbnailSize = (1024 * 1024) * 2;

    if ($file['error'] != UPLOAD_ERR_OK) {
        $uploadErr = "File Not Uploaded, Please Try Again !";
    } else if (!str_starts_with($file['type'], 'video')) {
        $uploadErr = "Invalid File Format, Only Video Uploads Allowed !";
    } else if ($file['size'] > $maxFileSize) {
        $uploadErr = "File Too Large, Max 10MB Uploads Allowed !";
    } else {
        if (empty($title)) {
            $errors[] = "Title Cannot Be Empty !";
        }

        $thumbnailUploadErr = '';
        if ($thumbnail['error'] == UPLOAD_ERR_OK) {
            if ($thumbnail['size'] > $maxThumbnailSize) {
                $thumbnailUploadErr = "Too Large Thumbnail !, Max 2MB Allowed";
            }

            if (!str_starts_with($thumbnail['type'], 'image')) {
                $thumbnailUploadErr = "Invalid File Format, Only Image Allowed For Thumbnails!";
            }
        }

        if (!$thumbnailUploadErr) {
            $fileName = json_encode($file['name']);
            $thumbnailId = "";
            $fileId = uploadToB2($client, "authuserfiles", $file);
            if ($thumbnail['error'] === UPLOAD_ERR_OK) {
                $thumbnailId = uploadToB2($client, 'authuserthumbnails', $thumbnail);
            }

            if ($fileId) {
                try {
                    $sql = "INSERT INTO uploads (uploader_id, upload_title,upload_des, upload_pathid, upload_thumbnail,upload_duration) VALUES(?,?,?,?,?,?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ssssss', $uid, $title, $des, $fileId, $thumbnailId, $duration);
                    $stmt->execute();
                    $isFileUploaded = true;
                } catch (Exception $e) {
                    $errors[] = "Failed To Upload File !, [" . $e->getMessage() . "]";
                }
            }
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video</title>

    <!-- cdns -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- stylesheets -->
    <link rel="stylesheet" href="../css/config.css">
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="../css/upload.css">
</head>

<body class="d-flex position-relative">
    <div class="position-absolute top-0 start-0 h-100 w-100 bg-white d-none" id="loader">
        <div class="m-auto text-center">
            <svg style="height: 80px;" version="1.1" id="L1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                <circle fill="none" stroke="#000" stroke-width="6" stroke-miterlimit="15" stroke-dasharray="14.2472,14.2472" cx="50" cy="50" r="47">
                    <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="5s" from="0 50 50" to="360 50 50" repeatCount="indefinite" />
                </circle>
                <circle fill="none" stroke="#000" stroke-width="1" stroke-miterlimit="10" stroke-dasharray="10,10" cx="50" cy="50" r="39">
                    <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="5s" from="0 50 50" to="-360 50 50" repeatCount="indefinite" />
                </circle>
                <g fill="#000">
                    <rect x="30" y="35" width="5" height="30">
                        <animateTransform attributeName="transform" dur="1s" type="translate" values="0 5 ; 0 -5; 0 5" repeatCount="indefinite" begin="0.1" />
                    </rect>
                    <rect x="40" y="35" width="5" height="30">
                        <animateTransform attributeName="transform" dur="1s" type="translate" values="0 5 ; 0 -5; 0 5" repeatCount="indefinite" begin="0.2" />
                    </rect>
                    <rect x="50" y="35" width="5" height="30">
                        <animateTransform attributeName="transform" dur="1s" type="translate" values="0 5 ; 0 -5; 0 5" repeatCount="indefinite" begin="0.3" />
                    </rect>
                    <rect x="60" y="35" width="5" height="30">
                        <animateTransform attributeName="transform" dur="1s" type="translate" values="0 5 ; 0 -5; 0 5" repeatCount="indefinite" begin="0.4" />
                    </rect>
                    <rect x="70" y="35" width="5" height="30">
                        <animateTransform attributeName="transform" dur="1s" type="translate" values="0 5 ; 0 -5; 0 5" repeatCount="indefinite" begin="0.5" />
                    </rect>
                </g>
            </svg>


            <h6 class="mt-3">Uploading File, Please Wait...</h6>
        </div>
    </div>

    <?php if ($isFileUploaded) { ?>
        <div class="position-absolute top-0 start-0 vh-100 w-100 bg-white d-flex" style="z-index: 1004;">
            <div class="m-auto text-center text-secondary">
                <i class="fa-solid fa-circle-check" style="color: #63E6BE;font-size:100px"></i>
                <h4 class="mt-4">Video Uploaded Successfully</h4>
                <a href="<?php echo $baseUrl . 'app/user/upload.php' ?>" class="btn btn-dark px-4 py-2 mt-4 rounded-5">
                    <b>upload new video</b>
                </a> <br>
                <button class="btn p-0 mt-1" onclick="navigator.clipboard.writeText(<?php echo 'http:/' . '/localhost/video_player/player.php?v=' . $fileId ?>)">
                    <b class="text-primary">copy video link</b>
                </button>
            </div>
        </div>
    <?php } ?>

    <!-- BODY -->
    <div class="container pt-3 m-auto position-relative">


        <form id="uploadForm" class="m-auto" enctype="multipart/form-data" action="#" method="POST">

            <div class="err-container <?php if (!empty($errors)) echo 'show' ?>">
                <div class="alert alert-danger d-flex gap-3 align-items-center" role="alert">
                    <i class="fa-solid fa-triangle-exclamation flex-shrink-0"></i>
                    <div class="alert-text">
                        <?php echo implode("<br>", $errors); ?>
                    </div>
                </div>
            </div>
            <div class="d-flex upload-form-row">
                <div class="upload-container text-center flex-shrink-0 w-100 pt-5">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <h3 class="text-muted mt-4">Upload Video</h3>
                    <p class="form-text">Be Sure That Your Video Dosent Contains Any Nude or Pornographic Contents, If Found Video Will Be Take Down And Upload Restrictions Will Be Appended</p>
                    <button class="btn btn-dark rounded-2 p-0" type="button">
                        <label for="fileSelInp">
                            <b class="px-5 py-2 d-block">Browse</b>
                        </label>
                    </button>
                    <input type="file" id="fileSelInp" name="file" accept="video/*" hidden>
                    <div class="mt-2 text-secondary">
                        <?php

                        if ($_SERVER['REQUEST_METHOD'] === "POST" && !$isFileUploaded) {
                            echo "Something Went Wrong, Faile To Upload File <br>, Please Try Again !";
                        } else {
                            echo "Select Max 10mb Video";
                        }

                        ?>
                    </div>
                </div>

                <div class="upload-info-container flex-shrink-0 w-100 pb-3">
                    <div class="form-header">
                        <h3>Add Video Info</h3>
                        <p class="text-muted">this info will be used to search and recognize the video</p>
                        <hr>
                    </div>

                    <div class="err-container my-2">
                        <div class="alert alert-danger d-flex gap-3 align-items-center" role="alert">
                            <i class="fa-solid fa-triangle-exclamation flex-shrink-0"></i>
                            <div class="alert-text"></div>
                        </div>
                    </div>

                    <button type="reset" class="btn border-1 border-secondary px-5 py-2 rounded-3 mt-2 mb-3"><i class="fa-solid fa-angle-left"></i><b class="ms-2">Upload New Video Instead</b></button>

                    <div class="text-danger thumbnail-err mb-2"></div>
                    <div class="position-relative">
                        <div class="upload-prev">
                            <video src="#" preload="metadata" id="uploadPrevVideo" class="rounded-3 bg-light"></video>
                        </div>
                        <div class="position-absolute top-50 start-50 translate-middle prev-control">
                            <button class="btn rounded-5 bg-dark btn-play" type="button">
                                <i class="fa-solid fa-play text-light"></i>
                            </button>
                        </div>
                        <div class="position-absolute bottom-0 end-0 m-2 thumbnail-container rounded-2">
                            <img src="../../images/download.jfif" alt="#" class="rounded-2" id="uploadPrevThumbnail">
                        </div>
                    </div>
                    <div class="text-end text-primary">
                        <label for="selThumbnailInp">
                            <i class="fa-regular fa-image"></i>&nbsp;&nbsp;<span>Add custom thumbnail</span>
                        </label>
                    </div>
                    <input type="file" accept="image/*" name="thumbnail" id="selThumbnailInp" hidden>

                    <div class="form-group mt-2">
                        <label for="_vtitle">Video Title</label>
                        <input type="text" class="form-control" name="title" id="_vtitle" placeholder="Enter Video Title">
                        <div class="text-danger"></div>
                        <div class="form-text text-end">Max: 0 of 100</div>
                    </div>
                    <div class="form-group mt-1">
                        <label for="_vdes">Video Description</label>
                        <textarea class="form-control" id="_vdes" name="des" placeholder="Enter Video Description"></textarea>
                        <div class="text-danger"></div>
                        <div class="form-text text-end">Max: 0 of 500</div>
                    </div>
                    <button type="submit" class="btn btn-dark px-5 py-2 rounded-3 mt-2 w-100"><i class="fa-solid fa-cloud"></i><b class="ms-2">Continue</b></button>
                </div>
            </div>
        </form>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../js/functions.js"></script>
    <script src="../js/upload.js"></script>

</body>

</html>