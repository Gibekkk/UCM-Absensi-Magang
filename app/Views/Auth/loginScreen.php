<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - UC Internship Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
    <style>
        body {
            background-image: url('<?= base_url("img/loginBackground.jpg"); ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, sans-serif;
            margin: 0;
            background-color: rgba(0, 0, 0, 0.45);
            background-blend-mode: overlay;
        }

        .glass-card {
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            position: relative;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
            padding: 0.8rem 1rem;
            border-radius: 12px;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.25) !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
            box-shadow: none;
        }

        .btn-login {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            font-weight: 600;
            padding: 0.8rem;
            border-radius: 12px;
            border: none;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>
    <?= view('layout/modals') ?>
    <div class="glass-card glass-panel">
        <h2 class="text-center">Login</h2>

        <form id="loginForm">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-4">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login">Sign In</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="<?= base_url("js/app.js") ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = getCookie('token');

            if (token) {
                fetch('<?= base_url("auth/logout"); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            token: token
                        }),
                        keepalive: true
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.status === 'success') {
                            console.log("Logged out from server");
                        } else {
                            console.error(res.message || "Unknown Error Occurred");
                        }
                        deleteCookie('token');
                    });
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('<?= base_url("auth/login"); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        setCookie('token', res.token);
                        window.location.href = '<?= base_url("/admin/students"); ?>';
                    } else {
                        showAlert('Login Error', res.message || "Unknown Error Occurred");
                    }
                });
        });
    </script>
</body>

</html>