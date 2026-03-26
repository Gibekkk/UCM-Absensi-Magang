<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Scanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css" rel="stylesheet" />
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>

<body>
    <div class="dashboard-container">
        <!-- Header/Info Kiri -->
        <div class="info-panel glass-panel">
            <h2 id="date">Monday, 16 March 2026</h2>
            <h1 id="time">08:10:21</h1>
            <p>Good morning</p>
            <div class="location">Lobby UCM - Ciputra School of Business</div>
        </div>

        <!-- Statistik Tengah -->
        <div class="stats-panel glass-panel">
            <h3>Total Visitors</h3>
            <h1 class="big-number">64</h1>
            <div class="grid-stats">
                <div class="stat-item">Employees <span>64</span></div>
                <div class="stat-item">Students <span>0</span></div>
                <div class="stat-item">Outsources <span>0</span></div>
                <div class="stat-item">Guests <span>0</span></div>
            </div>
        </div>

        <!-- Riwayat Kanan -->
        <div class="history-panel glass-panel">
            <h3>Recent Records</h3>
            <table class="table table-borderless text-white">
                <thead>
                    <tr>
                        <th>ID No</th>
                        <th>Scan Time</th>
                    </tr>
                </thead>
                <tbody id="recentRecords">
                    <!-- Data diisi via JS -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('time').innerText = now.toLocaleTimeString();
            document.getElementById('date').innerText = now.toDateString();
        }
        setInterval(updateTime, 1000);

        // Fetch data riwayat terbaru setiap 5 detik
        function fetchRecentRecords() {
            $.get('<?= base_url("api/attendance/recent"); ?>', function(res) {
                let html = '';
                res.data.forEach(item => {
                    html += `<tr><td>${item.nim}</td><td>${item.scan_time}</td></tr>`;
                });
                $('#recentRecords').html(html);
            });
        }
        setInterval(fetchRecentRecords, 5000);
    </script>
</body>

</html>