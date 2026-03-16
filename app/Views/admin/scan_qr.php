<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR - Sistem Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <style>
        #reader {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            border: none !important;
        }

        #reader__scan_region {
            background: white !important;
        }

        .result-card {
            display: none;
            border-radius: 12px;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Absensi Mahasiswa</a>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-white me-3 d-none d-md-block">
                    Halo, <strong><?= $admin->nama_admin ?></strong>
                </span>
                <a href="<?= base_url('admin/home') ?>"
                    class="btn btn-light btn-sm fw-semibold text-primary">Kembali</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <!-- Area Kamera -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-3 text-center">
                        <h6 class="fw-bold mb-3">Arahkan Kamera ke QR Code</h6>
                        <div id="reader"></div>
                    </div>
                </div>

                <!-- Area Hasil Scan -->
                <div id="resultCard" class="card result-card shadow-sm border-0 bg-white p-4">
                    <div class="text-center mb-3">
                        <div class="badge bg-success mb-2">Data Ditemukan</div>
                        <h4 class="fw-bold mb-0" id="resNama">-</h4>
                        <p class="text-muted small" id="resNim">-</p>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <label class="text-muted small d-block">Angkatan</label>
                            <span class="fw-semibold" id="resAngkatan">-</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Jurusan</label>
                            <span class="fw-semibold" id="resJurusan">-</span>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm w-100 mt-4" onclick="resetScanner()">Scan
                        Lagi</button>
                </div>

                <!-- Area Jika Error -->
                <div id="errorMsg" class="alert alert-danger small d-none text-center">
                    Mahasiswa tidak terdaftar dalam sistem.
                </div>

            </div>
        </div>
    </div>

    <!-- Library QR Scanner -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: { width: 250, height: 250 } }
        );

        function onScanSuccess(decodedText, decodedResult) {
            // Hentikan scanner sementara agar tidak terus menerus memproses
            html5QrcodeScanner.clear();
            decodedText = decodedText.split('+')[0]; // Ambil bagian terakhir dari URL (NIM)
            console.log(decodedText);

            // Panggil API untuk ambil data mahasiswa
            fetch(`<?= base_url('admin/findMahasiswa/') ?>${decodedText}`)
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success') {
                        document.getElementById('resultCard').style.display = 'block';
                        document.getElementById('errorMsg').classList.add('d-none');

                        // Isi data ke UI
                        document.getElementById('resNama').innerText = res.data.nama;
                        document.getElementById('resNim').innerText = res.data.nim;
                        document.getElementById('resAngkatan').innerText = res.data.angkatan;
                        document.getElementById('resJurusan').innerText = res.data.jurusan;
                    } else {
                        document.getElementById('errorMsg').classList.remove('d-none');
                        setTimeout(resetScanner, 3000); // Reset otomatis setelah 3 detik jika error
                    }
                })
                .catch(err => {
                    alert("Terjadi kesalahan koneksi ke server.");
                    resetScanner();
                });
        }

        function resetScanner() {
            document.getElementById('resultCard').style.display = 'none';
            document.getElementById('errorMsg').classList.add('d-none');
            html5QrcodeScanner.render(onScanSuccess);
        }

        // Jalankan scanner pertama kali
        html5QrcodeScanner.render(onScanSuccess);
    </script>

</body>

</html>