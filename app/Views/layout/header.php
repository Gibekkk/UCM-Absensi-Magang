<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
                    <img class="bi me-2 d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none" width="40" height="40" role="img" aria-label="UC Makassar" src="<?= base_url('img/logo.png') ?>">

                    <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0" id="navbar">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: '<?= base_url("auth/me"); ?>',
                type: 'GET',
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                success: (res) => {
                    $('#username').text(res.username);
                    if (res.is_super_admin == 1) {
                        $('#navbar').append(`
                    <li>
                        <a href="<?= base_url('admin/users') ?>" 
                           class="nav-link px-2 link-<?= ($page == "users") ? "dark" : "secondary" ?>">
                           Users
                        </a>
                    </li>
                `);
                    }
                },
                error: (err) => console.error("Error Fetching Profile")
            });
        });
    </script>

</body>

</html>