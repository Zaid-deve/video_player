<?php

require "config.php";

session_start();
$errors = [];

if(isset($_SESSION['user_id'])){
    echo "User Is Already Loged In !";
    die();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    require "../db/db_conn.php";

    // Validate email
    $email = $conn->real_escape_string(htmlentities($_POST['email']));
    $pass = $conn->real_escape_string(htmlentities($_POST['pass']));
    $rem = $conn->real_escape_string(htmlentities($_POST['remember_me']));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email Address !";
    }

    if (empty($pass)) {
        $errors[] = "Invalid Password, It Cannot Be Empty";
    }


    if (empty($errors)) {
        $enc_email = base64_encode($email);

        try {
            $stmt = $conn->prepare("SELECT user_id,user_pass FROM users WHERE user_email = ?");
            $stmt->bind_param("s", $enc_email);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows > 0) {
                $data = $res->fetch_assoc();
                if (password_verify($pass, $data['user_pass'])) {
                    $_SESSION['user_id'] = $data['user_id'];
                    echo "success";
                } else {
                    echo "Invalid Credentials, Please Check And Try Again !";
                }
            } else {
                echo "Invalid Credentials, Please Check And Try Again !";
            }
            die();
        } catch (Exception $e) {
            $errors[] = "Something Went Wrong - " . $e->getCode();
        }

    }

    echo implode("<br>", $errors);
}
