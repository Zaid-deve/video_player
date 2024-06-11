<?php

require "config.php";

session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    require "../db/db_conn.php";

    // Validate email
    $email = $conn->real_escape_string(htmlentities($_POST['email']));
    $pass = $conn->real_escape_string(htmlentities($_POST['pass']));
    $uname = $conn->real_escape_string(htmlentities($_POST['uname']));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email Address !";
    }

    // Validate password
    if (strlen($pass) < 8) {
        $errors[] = "Invalid Password, It Must be Of Minimum 8 Characters !";
    }

    // Validate username
    if (!preg_match('/^[a-zA-Z0-9_]{1,20}$/', $uname)) {
        $errors[] = "Invalid Username. It must be 1 to 20 characters and can only contain letters, numbers, or underscores.";
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
        $enc_email = base64_encode($email);
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO `users` (`user_name`, `user_email`, `user_pass`, `user_profile`) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $enc_uname, $enc_email, $hashed_pass, $uploadHttpPath);
            $stmt->execute();
            $_SESSION['user_id'] = $conn->insert_id;
            if (isset($uploadPath)) {
                move_uploaded_file($profile['tmp_name'], $uploadPath);
            }

            echo "success";
            die();
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), 'user_name') !== false) {
                    $errors[] = "Username already exists.";
                }
                if (strpos($e->getMessage(), 'user_email') !== false) {
                    $errors[] = "Email address already exists.";
                }
            } else $errors[] = "Failed To Create User !";
        }
    }

    echo implode("<br>", $errors);
}
