<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/headers/">



    <!-- Bootstrap core CSS -->
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>


    <!-- Custom styles for this template -->
    <link href="<?= base_url('css/headers.css') ?>" rel="stylesheet">
</head>

<body>

    <main>

        <header class="p-3 mb-3 border-bottom">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                    <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                        <img class="bi me-2" width="40" height="40" role="img" aria-label="UC Makassar" src="<?= base_url('img/logo.png') ?>">
                    </a>

                    <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                        <li><a href="<?= base_url('admin/students') ?>" class="nav-link px-2 link-<?= ($page == "students") ? "dark" : "secondary" ?>">Students</a></li>
                        <li><a href="<?= base_url('admin/internships') ?>" class="nav-link px-2 link-<?= ($page == "internships") ? "dark" : "secondary" ?>">Internships</a></li>
                        <li><a href="<?= base_url('admin/attendance') ?>" class="nav-link px-2 link-<?= ($page == "attendance") ? "dark" : "secondary" ?>">Attendance</a></li>
                    </ul>

                    <div class="dropdown text-end">
                        <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <p href="#" class="d-inline px-2 link-dark" id="username"></p>
                        </a>
                        <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
                            <!-- <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li> -->
                            <li><a class="dropdown-item" href="<?= base_url('auth/login') ?>">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

    </main>


    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>