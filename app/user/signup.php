<?php

session_start();
$errors = [];

if (isset($_SESSION['user_id'])) {
    header("Location:account.php");
    die();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>

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
        <form class="mx-auto py-3 px-4 rounded-3" id="signupForm">
            <div class="form-header">
                <h3>Sign Up</h3>
                <p class="text-muted">join with us !</p>
                <hr>
            </div>
            <div class="err-container">
                <div class="alert alert-danger d-flex gap-3 align-items-center" role="alert">
                    <i class="fa-solid fa-triangle-exclamation flex-shrink-0"></i>
                    <div class="alert-text"></div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="profile-sel position-relative flex-shrink-0 mt-3">
                    <label for="profileSelInp">
                        <img src="../../images/blank-profile-picture-973460_960_720.webp" alt="#" class="profileSelPrev rounded-circle bg-light" height="50px" width="50px">
                        <div class="position-absolute bottom-0 end-0 rounded-4 bg-success px-2">
                            <i class="fa-solid fa-plus text-light profile-add-icon"></i>
                        </div>
                    </label>
                    <input type="file" id="profileSelInp" accept="image/*" hidden>
                </div>
                <div class="form-group w-100 mt-2">
                    <label for="_uname">Username</label>
                    <input type="text" class="form-control" id="_uname" placeholder="Enter username">
                    <div class="text-danger"></div>
                </div>
            </div>
            <div class="form-text text-danger file-err"></div>
            <div class="form-text">
                <ul class="list-group ps-3">
                    <li>
                        <small>Profile And Username Will be Visible When You Upload A Video</small>
                    </li>
                    <li>
                        <small>username Can Only Contains Letters,Numbers And _(underscore)</small>
                    </li>
                    <li>
                        <small>Profile Image Can Not Be Greater Than 2MB</small>
                    </li>
                </ul>
            </div>
            <div class="form-group mt-4">
                <label for="_email">Email address</label>
                <input type="text" class="form-control" id="_email" placeholder="Enter email">
                <div class="text-danger"></div>
            </div>
            <div class="form-group mt-2">
                <label for="_pass">Password</label>
                <input type="password" class="form-control" id="_pass" placeholder="Password">
                <div class="form-text">Must contain minimum 8 characters</div>
            </div>
            <button type="submit" class="btn btn-dark px-5 py-2 rounded-3 mt-3 w-100 btn-submit"><i class="fa-solid fa-paper-plane"></i><b class="ms-2">Continue</b></button>
            <div class="text-center mt-2">
                <a class="btn p-0 text-primary" href="signin.php"><b>I Already have and account</b></a>
            </div>
        </form>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- functions -->
    <script src="../js/functions.js"></script>
    <!-- signup script -->
    <script src="../js/signup.js"></script>

</body>

</html>