<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - UC Internship Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* Menggunakan gambar dari folder public/img/ */
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
            /* Overlay gelap agar gambar tidak terlalu terang/mengganggu teks */
            background-color: rgba(0, 0, 0, 0.45);
            background-blend-mode: overlay;
        }

        .glass-card {
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            /* Glassmorphism effect */
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
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

        .alert-glass {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
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

    <div class="glass-card">
        <h2 class="text-center">Login</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-glass">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <!-- Tambahkan id pada form -->
        <form id="loginForm">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-4">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login">Sign In</button>
        </form>

        <!-- Tambahkan id pada alert dan sembunyikan secara default -->
        <div id="alertBox" class="alert alert-glass" style="display: none;"></div>
    </div>
    <script src="<?= base_url("js/app.js") ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = getCookie('token');

            if (token) {
                // Kirim logout ke server sebelum menghapus cookie
                fetch('<?= base_url("auth/logout"); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            token: token
                        }),
                        keepalive: true // Memastikan request terkirim meski halaman berpindah
                    })
                    .then(() => {
                        console.log("Logged out from server");
                        deleteCookie('token');
                    })
                    .catch(err => console.error("Logout failed", err));
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah reload halaman

            const alertBox = document.getElementById('alertBox');
            const formData = new FormData(this);

            fetch('<?= base_url("auth/login"); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        setCookie('token', data.token);
                        window.location.href = '<?= base_url("/admin/dashboard"); ?>';
                    } else {
                        // Tampilkan error di dalam alert-glass
                        alertBox.innerHTML = data.message;
                        alertBox.style.display = 'block';
                    }
                })
                .catch(error => {
                    alertBox.innerHTML = 'An error occurred. Please try again.';
                    alertBox.style.display = 'block';
                });
        });
    </script>
</body>
