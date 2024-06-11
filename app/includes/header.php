<div class="fixed-top bg-white py-3">
    <div class="container-fluid">
        <nav class="d-flex flex-wrap align-items-center">
            <button class="btn border-0  rounded-circle btn-menu-toggler" onclick="toggleMenu()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="header-brand pe-5 me-auto">
                <h1 class="m-0 p-0">Video Player</h1>
            </div>

            <button class="btn search-toggler rounded-5 nav-btn p-0 d-lg-none d-block me-2">
                <i class="fa-solid fa-search"></i>
            </button>

            <div class="nav-right order-md-4">
                <?php if (!isset($_SESSION['user_id'])) { ?>
                    <a href="<?php echo $baseUrl ?>app/user/signin.php" title="login to your account" class="btn btn-secondary px-4 py-2 rounded-5">
                        <i class="fa-solid fa-user"></i> <b class="ms-2 d-none d-sm-inline-block">Sign In</b>
                    </a>
                <?php } else { ?>
                    <a class="btn btn-secondary rounded-5 me-2 bg-light border-0 py-2" href="<?php echo $baseUrl . "app/user/upload.php" ?>">
                        <i class="fa-solid fa-cloud-arrow-up text-dark"></i> <b class="text-dark ms-2 d-none d-sm-inline-block">Upload Video</b>
                    </a>

                    <button class="btn btn-header-menu-toggler p-0 border-0">
                        <img src="<?php echo $baseUrl ?>images/blank-profile-picture-973460_960_720.webp" alt="#" class="bg-light rounded-5">
                    </button>

                    <div class="header-menu position-absolute top-100 end-0 me-3 bg-white rounded-3">
                        <ul class="list-group">
                            <li class="list-group-item p-0">
                                <a href="<?php echo $baseUrl . "app/user/account.php" ?>"><i class="fa-solid fa-box-open"></i>&nbsp; My Account</a>
                            </li>
                            <li class="list-group-item p-0">
                                <a href="<?php echo $baseUrl . "app/user/videos.php" ?>"><i class="fa-solid fa-file-import"></i>&nbsp; My Uploads</a>
                            </li>
                            <li class="list-group-item p-0">
                                <a href="<?php echo $baseUrl . "app/user/logout.php" ?>"><i class="fa-solid fa-right-from-bracket"></i>&nbsp; Logout</a>
                            </li>
                        </ul>
                    </div>
                <?php } ?>
            </div>


            <div class="search-bar position-relative order-md-3 d-lg-block d-none flex-grow-1 me-md-3">
                <div class="search-box">
                    <input type="text" placeholder="Tap To Search Videos,Trendings,Most Viewed And More...." id="header-search-inp" class="form-control bg-light rounded-5 border-0 px-4">
                </div>
                <div class="search-results position-absolute top-100 start-0 w-100 rounded-3 py-3 bg-white d-none"></div>
            </div>
        </nav>
    </div>
</div>