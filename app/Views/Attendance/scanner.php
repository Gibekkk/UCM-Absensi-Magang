<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Scanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        /* Menghilangkan ikon panah sort bawaan DataTables */
        table.dataTable thead .sorting:before,
        table.dataTable thead .sorting:after,
        table.dataTable thead .sorting_asc:before,
        table.dataTable thead .sorting_asc:after,
        table.dataTable thead .sorting_desc:before,
        table.dataTable thead .sorting_desc:after {
            display: none !important;
        }

        /* Memastikan header tidak terlihat seperti tombol (tidak ada kursor pointer) */
        table.dataTable thead th {
            cursor: default !important;
        }

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
            font-family: 'Outfit', sans-serif;
            margin: 0;
            background-color: rgba(0, 0, 0, 0.5);
            background-blend-mode: overlay;
        }

        /* Memastikan angka memiliki lebar yang sama agar tidak bergeser */
        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            border-radius: 16px;
        }

        .content-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            width: 90%;
            height: 85vh;
        }

        .right-container {
            padding: 1.5rem;
            overflow: hidden;
        }

        #attendanceTable {
            color: white !important;
            border-collapse: collapse !important;
        }

        #attendanceTable,
        #attendanceTable thead,
        #attendanceTable tbody,
        #attendanceTable tr,
        #attendanceTable td,
        #attendanceTable th {
            background-color: transparent !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        #attendanceTable thead th {
            color: #aaa;
            font-weight: 500;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2) !important;
        }

        #attendanceTable tbody td {
            padding: 12px 8px !important;
            color: rgb(173, 173, 173);
        }

        .dataTables_wrapper {
            background: transparent !important;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: none !important;
        }

        .left-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .top-container {
            flex: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .bottom-container {
            display: flex;
            gap: 2rem;
            flex: 1;
        }

        .flex-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .title {
            font-size: 2rem;
            color: #ccc;
            margin: 0;
        }

        .count {
            font-size: 6rem;
            color: #ffffff;
            font-weight: bold;
            margin: 0;
        }

        .day {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            margin: 0;
        }

        .date {
            font-size: 1.2rem;
            color: #aaa;
            margin: 0;
        }

        .time {
            font-size: 3rem;
            font-weight: 700;
            color: white;
            margin: 0;
        }

        .greeting {
            font-size: 1.2rem;
            color: #aaa;
            margin: 0;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            padding: 2rem;
            text-align: center;
            color: white;
            width: 300px;
            animation: fadeIn 0.3s ease-out;
        }

        .icon-box {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .progress-container {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            margin-top: 15px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 100%;
            background: #00ff88;
            animation: shrink 3s linear forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes shrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }
    </style>
</head>

<body>
    <?= view('layout/modals') ?>
    <!-- Modal Container -->
    <div id="notifModal" class="modal-overlay" style="display: none;">
        <div class="glass-card modal-content">
            <!-- Icon Container -->
            <div id="modalIcon" class="icon-box">
                <!-- SVG akan diisi via JS -->
            </div>
            <h3 id="modalTitle">Success</h3>
            <p id="modalMessage">Attendance recorded successfully!</p>

            <!-- Duration Bar -->
            <div class="progress-container">
                <div id="progressBar" class="progress-bar"></div>
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="left-container">
            <div class="top-container glass-card">
                <p class="title">Students</p>
                <p class="count tabular-nums" id="inCount">0</p>
            </div>
            <div class="bottom-container">
                <div class="date-container glass-card flex-container flex-fill">
                    <p class="day" id="dayDisplay">-</p>
                    <p class="date" id="dateDisplay">-</p>
                </div>
                <div class="time-container glass-card flex-container flex-fill">
                    <p class="time tabular-nums" id="timeDisplay">00:00:00</p>
                    <p class="greeting" id="greetingDisplay">-</p>
                </div>
            </div>
        </div>
        <div class="right-container glass-card">
            <table id="attendanceTable" class="table">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Time</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script>
        let table;
        let scannerBuffer = "";
        let isModalOpen = false;

        $(document).on('keypress', function(e) {
            if (isModalOpen) scannerBuffer = "";
            if (e.which === 13) {
                if (scannerBuffer.length > 0) {
                    sendAttendance(scannerBuffer);
                    scannerBuffer = "";
                }
            } else {
                scannerBuffer += String.fromCharCode(e.which);
            }
        });


        // Modifikasi fungsi sendAttendance
        function sendAttendance(nim) {
            if (isModalOpen) return;

            $.ajax({
                url: '<?= base_url("api/attend") ?>',
                method: 'POST',
                headers: {
                    'RequestType': 'API'
                },
                contentType: 'application/json',
                data: JSON.stringify({
                    input: nim
                }),

                // Menangani berbagai status code
                statusCode: {
                    200: function(res) {
                        // Berhasil
                        showModal(true, "Success", (res.status == "IN" ? "Welcome, " : "See You Later, ") + res.name.split(" ")[0]);
                        refreshDataTable();
                    },
                    400: function(xhr) {
                        // Bad Request (misal: Absensi terlalu cepat)
                        const res = xhr.responseJSON;
                        showModal(false, "Failed", res.message || "Please Wait Before Scanning Again.");
                    },
                    404: function(xhr) {
                        // Bad Request (misal: NIM tidak ditemukan)
                        const res = xhr.responseJSON;
                        showModal(false, "Failed", res.message || "NIM not found or invalid.");
                    },
                    500: function() {
                        // Server Error
                        showModal(false, "Server Error", "Something went wrong.");
                    }
                },

                // Menangani error umum (yang tidak tercover di statusCode)
                error: function(xhr) {
                    if (xhr.status !== 200 && xhr.status !== 404 && xhr.status !== 403 && xhr.status !== 500) {
                        showModal(false, "Error", "Something went wrong.");
                    }
                }
            });
        }

        function showModal(isSuccess, title, message) {
            isModalOpen = true;
            const modal = $('#notifModal');
            const icon = isSuccess ? '✅' : '❌';
            const color = isSuccess ? '#00ff88' : '#ff4444';

            $('#notifModal #modalIcon').text(icon);
            $('#notifModal #modalTitle').text(title);
            $('#notifModal #modalMessage').text(message);
            $('#notifModal #progressBar').css('background', color);

            modal.fadeIn(200);

            setTimeout(() => {
                modal.fadeOut(200, () => {
                    isModalOpen = false;
                });
            }, 3000);
        }

        function updateClock() {
            const now = dayjs();
            $('#timeDisplay').text(now.format('HH:mm:ss'));
            $('#dayDisplay').text(now.format('dddd'));
            $('#dateDisplay').text(now.format('MMMM D, YYYY'));

            const hour = now.hour();
            let greeting = hour < 12 ? "Good Morning" : hour < 15 ? "Good Afternoon" : hour < 19 ? "Good Evening" : "Good Night";
            $('#greetingDisplay').text(greeting);
        }

        function refreshDataTable() {
            $.ajax({
                url: '<?= base_url("api/attendance/viewToday"); ?>',
                headers: {
                    'RequestType': 'API'
                },
                success: function(res) {
                    const data = res.attendances;

                    // Logika Hitung: Jika status terakhir NIM adalah 'IN', maka hitung.
                    // Asumsi: Data sudah terurut berdasarkan waktu (paling baru di bawah/atas)
                    const statusMap = {};
                    data.forEach(item => {
                        statusMap[item.nim] = item.scan_time_type;
                    });
                    const inCount = Object.values(statusMap).filter(status => status === 'IN').length;
                    $('#inCount').text(inCount);

                    if (table) table.destroy();
                    table = $('#attendanceTable').DataTable({
                        data: data,
                        paging: true,
                        pageLength: 25,
                        ordering: true,
                        searching: false,
                        info: false,
                        order: [1, 'desc'],
                        columns: [{
                                orderable: false,
                                data: 'nim'
                            },
                            {
                                orderable: false,
                                data: 'created_date',
                                render: (data, type, row) => {
                                    // Cek apakah data berupa objek (seperti format CI4 Entity)
                                    const dateVal = (typeof data === 'object' && data !== null) ? data.date : data;
                                    return dayjs(dateVal).format('HH:mm:ss');
                                },
                            },
                            {
                                orderable: false,
                                data: 'scan_time_type'
                            }
                        ]
                    });
                }
            });
        }

        $(document).ready(() => {
            updateClock();
            setInterval(updateClock, 1000);
            refreshDataTable();
            setInterval(refreshDataTable, 60000);
        });
    </script>
</body>

</html>