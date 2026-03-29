<!DOCTYPE html>
<html lang="en">

<head>
    <title>Attendance Data - UC Internship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css" rel="stylesheet" />
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>

<body>
    <?= view('layout/modals') ?>
    <div class="d-flex">
        <?= view('layout/sidebar') ?>
        <div class="flex-grow-1">
            <?= view('layout/header', ["page" => "attendance"]) ?>
            <div class="p-4">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between mb-3">
                    <h3>Attendance Data</h3>
                    <input type="date" id="dateFilter" class="form-control w-auto">
                </div>

                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Student Name</th>
                                <th>Internship Name</th>
                                <th>Attendance Time</th>
                                <th>Attendance Type</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>

    <script>
        // Simpan referensi tabel agar bisa di-destroy
        let table;

        function refreshDataTable(dateLink = null) {
            // Jika tabel sudah ada, hancurkan dulu
            if (table) {
                table.destroy();
            }

            // Tentukan URL berdasarkan input tanggal
            let url = '<?= base_url("api/attendance"); ?>';
            if (dateLink) {
                url += `/${dateLink}`;
            }

            table = $('#attendanceTable').DataTable({
                ajax: {
                    url: url,
                    beforeSend: (xhr) => xhr.setRequestHeader('RequestType', 'API'),
                    dataSrc: 'attendances'
                },
                ordering: true,
                orderFixed: [4, 'desc'],
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'nim'
                    },
                    {
                        data: 'student_name'
                    },
                    {
                        data: 'internship_name'
                    },
                    {
                        data: 'created_date',
                        render: (data, type, row) => {
                            const dateVal = (typeof data === 'object' && data !== null) ? data.date : data;
                            return dayjs(dateVal).format('DD-MM-YYYY HH:mm');
                        }
                    },
                    {
                        data: 'scan_time_type'
                    },
                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    api.column(0, {
                        search: 'applied',
                        order: 'applied'
                    }).nodes().each((cell, i) => {
                        cell.innerHTML = i + 1;
                    });
                }
            });
        }

        $(document).ready(function() {
            // Inisialisasi awal
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
                    $('#username').text(res.user.username);
                },
                error: (err) => console.error("Error Fetching Profile")
            });

            $('#dateFilter').val(dayjs().format('YYYY-MM-DD'));
            refreshDataTable('today');

            // Event listener saat tanggal berubah
            $('#dateFilter').on('change', function() {
                const val = $(this).val(); // Format input date: YYYY-MM-DD
                if (val) {
                    // Ubah YYYY-MM-DD menjadi YYYY/MM/DD untuk route Anda
                    const dateLink = val.replace(/-/g, '/');
                    refreshDataTable(dateLink);
                } else {
                    // Jika tanggal dihapus, tampilkan semua
                    refreshDataTable();
                }
            });
        });
    </script>
</body>

</html>