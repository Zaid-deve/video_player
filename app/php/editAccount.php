<?php

require "config.php";

session_start();
if (!isset($_SESSION['user_id'])) {
    echo 'Plese Login Again And Continue !';
    die();
}
$uid = $_SESSION['user_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    require "../db/db_conn.php";

    $uname = $conn->real_escape_string(htmlentities($_POST['uname']));
    $bio = $conn->real_escape_string(htmlentities($_POST['bio']));

    if (isset($_FILES['profile'])) {
        $profile = $_FILES['profile'];
    }

    // Validate username
    if (!preg_match('/^[a-zA-Z0-9_]{1,20}$/', $uname)) {
        $errors[] = "Invalid Username. It must be 1 to 20 characters and can only contain letters, numbers, or underscores.";
    }

    // Validate username
    if (!empty($bio) && strlen($bio) > 500) {
        $errors[] = "Invalid Bio. It must be 1 to 500 characters";
    }

    // validate profile picture
    $uploadHttpPath = "";
    if (isset($_FILES['profile'])) {
        $profile = $_FILES['profile'];
        if ($profile['error'] == UPLOAD_ERR_OK) {
            $size = $profile['size'];
            $type = $profile['type'];
            $maxSize = (1024 * 1024) * 2;
            if (!str_starts_with($type,"image")) {
                $errors[] = "Invalid Profile Picture, Please Select Appropriate Picture !";
            }

            if ($size > $maxSize) {
                $errors[] = "Profile Picture Cannot Be Larger Than 2Mb !";
            }

            $uploadPath = $root . "profiles/" . $profile['name'];
            $uploadHttpPath = $baseUrl . "profiles/" . $profile['name'];
        } else $errors[] = 'Failed To Add Profile Pictuere !';
    }


    if (empty($errors)) {
        $enc_uname = base64_encode($uname);
        $enc_bio = base64_encode($bio);


        try {
            $sql = "UPDATE users SET user_name = ?, user_bio = ?";
            if ($uploadHttpPath) {
                $sql .= ",user_profile=?";
            }

            $stmt = $conn->prepare("$sql WHERE user_id = $uid");
            if (!$uploadHttpPath) $stmt->bind_param("ss", $enc_uname, $enc_bio);
            else {
                $stmt->bind_param("sss", $enc_uname, $enc_bio, $uploadHttpPath);
            }
            $stmt->execute();
            if (isset($uploadPath)) {
                move_uploaded_file($profile['tmp_name'], $uploadPath);
            }
            echo "success";
            die();
        } catch (Exception $e) {
            if ($e->getCode() === 1064) $errors[] = "Username Already Exists!";
            else $errors = "Failed To Update Account Info.";
        }
    }

    echo implode("<br>", $errors);
}
