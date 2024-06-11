<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location:signin.php?r=account');
    die();
}


require_once "../db/db_conn.php";
$uid = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("SELECT user_email,user_name,user_bio,user_profile FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows) {
        $user = $result->fetch_assoc();
        $email = base64_decode($user['user_email']);
        $uname = base64_decode($user['user_name']);
        $ubio = base64_decode($user['user_bio']);
        $profile = empty($user['user_profile']) ? '../../images/blank-profile-picture-973460_960_720.webp' : $user['user_profile'];
    } else {
        die("Something Went Wrong !");
    }
} catch (Exception $e) {
    die("Something Went Wrong - " . $e->getCode());
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
    <link rel="stylesheet" href="../css/config.css">
    <link rel="stylesheet" href="../css/form.css">
</head>

<body>

    <div class="container pt-3 position-relative">
        <div class="position-absolute top-0 start-0 bg-white h-100 w-100 d-flex hide" id="form-loader">
            <div class="m-auto text-center">
                <i class="fa-solid fa-circle-notch loader-icon"></i>
                <h6 class="mt-3">Working On Your Request <br> Please Wait</h6>
            </div>
        </div>
        <form class="mx-auto py-3 px-4 rounded-3" id="accountForm">
            <div class="form-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3>My Account</h3>
                        <p class="text-muted">your account details!</p>
                    </div>
                    <div class="position-relative">
                        <button class="btn btn-light rounded-5 btn-toggle-menu" type="button">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <div class="header-menu position-absolute top-100 end-0 me-3 bg-white rounded-3">
                            <ul class="list-group">
                                <li class="list-group-item p-0">
                                    <a href="videos.php"><i class="fa-solid fa-file-import"></i>&nbsp; My Uploads</a>
                                </li>
                                <li class="list-group-item p-0">
                                    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i>&nbsp; Logout</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div class="err-container">
                <div class="alert alert-danger d-flex gap-3 align-items-center" role="alert">
                    <i class="fa-solid fa-triangle-exclamation flex-shrink-0"></i>
                    <div class="alert-text"></div>
                </div>
            </div>

            <div class="d-flex align-items-center flex-column">
                <img src="<?php echo $profile ?>" alt="#" class="profileSelPrev rounded-circle bg-light" height="100px" width="100px">
                <label for="profileSelInp" class="mt-2">
                    <div class="btn text-info p-0">Change Profile</div>
                </label>
                <input type="file" id="profileSelInp" accept="image/*" hidden>
                <div class="text-danger file-err"></div>
            </div>

            <div class="form-group">
                <label for="_uname">Username</label>
                <input type="text" class="form-control" id="_uname" placeholder="Enter username" value="<?php echo $uname ?>">
                <div class="text-danger"></div>
            </div>

            <div class="form-group mt-2">
                <label for="_bio">Bio</label>
                <textarea class="form-control" id="_bio" placeholder="Enter your bio"><?php echo $ubio ?></textarea>
                <div class="text-danger"></div>
                <div class="form-text text-end">Max: 0 of 500</div>
            </div>

            <div class="form-group mt-2">
                <label for="_email">Email Address</label>
                <input type="text" class="form-control" id="_email" placeholder="Enter email" value="<?php echo $email ?>" readonly>
                <div class="text-danger"></div>
            </div>

            <button type="submit" class="btn btn-dark px-5 py-2 rounded-3 mt-4 w-100 d-none btn-submit">
                <i class="fa-solid fa-paper-plane"></i>
                <b class="ms-2">Continue</b>
            </button>
        </form>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../js/functions.js"></script>
    <script src="../js/account.js"></script>

</body>

</html>