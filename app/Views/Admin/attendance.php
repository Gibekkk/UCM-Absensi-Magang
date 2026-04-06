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
                <div class="mb-4">
                    <h3>Attendance Data</h3>
                    <div class="d-flex align-items-center gap-2 bg-light p-3 rounded shadow-sm">
                        <div class="d-flex align-items-center gap-2">
                            <label class="fw-bold">Filter By:</label>
                            <select id="filterSelector" class="form-select w-auto">
                                <option value="Date">Date Range</option>
                                <option value="NIM">NIM</option>
                                <option value="Department">Department</option>
                                <option value="Internship">Internship</option>
                            </select>
                        </div>

                        <form id="searchForm" class="d-flex align-items-center gap-2 flex-grow-1">
                            <!-- Container untuk input yang berubah-ubah -->
                            <div id="dynamicInputContainer" class="d-flex gap-2 flex-grow-1">
                                <!-- Default: Date Range -->
                                <input type="date" id="startDate" class="form-control flex-grow-1" required>
                                <input type="date" id="endDate" class="form-control flex-grow-1" required>
                            </div>

                            <!-- Selector Jenis Absensi (Selalu Ada) -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="fw-bold">Type:</label>
                                <select id="attendanceType" class="form-select w-auto">
                                    <option value="both">Both (In/Out)</option>
                                    <option value="in">In Only</option>
                                    <option value="out">Out Only</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary px-4">Search</button>
                        </form>
                    </div>
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
        let table;

        function populateDepartment() {
            $.ajax({
                url: '<?= base_url("admin/api/internships/department"); ?>',
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'GET',
                complete: (xhr) => {
    const res = JSON.parse(xhr.responseText);
                    if (res.status == 'success') {
                        $('#deptInput').empty();
                        $('#deptInput').append(`<option value="" selected>Select Department</Option>`);
                        res.departments.forEach(department => {
                            $('#deptInput').append(`<option value="${department.name}">${department.name}</Option>`);
                        });
                    } else {
                        showAlert("Error", res.message || "Unknown Error Occurred");
                    }
                }
            });
        }

        function populateInternship() {
            $.ajax({
                url: '<?= base_url("admin/api/internships"); ?>',
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'GET',
                complete: (xhr) => {
    const res = JSON.parse(xhr.responseText);
                    if (res.status == 'success') {
                        $('#internshipInput').empty();
                        $('#internshipInput').append(`<option value="" selected>Select Internship</Option>`);
                        res.internships.forEach(internship => {
                            $('#internshipInput').append(`<option value="${internship.id}">${internship.name}</Option>`);
                        });
                    } else {
                        showAlert("Error", res.message || "Unknown Error Occurred");
                    }
                }
            });
        }

        // Fungsi untuk mengganti input field berdasarkan filterSelector
        function updateFilterUI(filterType) {
            const container = $('#dynamicInputContainer');
            container.empty();

            switch (filterType) {
                case 'Date':
                    container.append(`
                        <input type="date" id="startDate" class="form-control flex-grow-1" required>
                        <input type="date" id="endDate" class="form-control flex-grow-1" required>
                    `);
                    break;
                case 'NIM':
                    container.append(`
                        <input type="text" id="nimInput" class="form-control" placeholder="Enter NIM (e.g. 070601...)" required>
                    `);
                    break;
                case 'Department':
                    container.append(`
                        <select id="deptInput" class="form-select" required>
                        </select>
                    `);
                    populateDepartment();
                    break;
                case 'Internship':
                    container.append(`
                        <select id="internshipInput" class="form-select" required>
                        </select>
                    `);
                    populateInternship();
                    break;
            }
        }

        function refreshDataTable(params = null) {
            if (table) {
                table.destroy();
            }

            let url = '<?= base_url("api/attendance"); ?>';
            
            if (params) {
                url += `/${params}`;
            }

            table = $('#attendanceTable').DataTable({
                ajax: {
                    url: url,
                    "beforeSend": function(xhr) {
                        xhr.setRequestHeader('token', getCookie('token'));
                        xhr.setRequestHeader('RequestType', 'API');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '<?= csrf_hash() ?>');
                    },
                    dataSrc: function(res) {
                        if (res.status == 'success') {
                            return res.attendances;
                        } else {
                            showAlert("Error", res.message || "Unknown Error Occurred");
                            return [];
                        }
                    }
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
            // Event listener saat filter selector berubah
            $('#filterSelector').on('change', function() {
                updateFilterUI($(this).val());
            });

            // Event listener saat form disubmit
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                
                const filterType = $('#filterSelector').val();
                const attendanceType = $('#attendanceType').val(); // in/out/both
                let filterValue = "";

                // Mengambil value berdasarkan input yang sedang aktif
                if (filterType === 'Date') {
                    filterValue = "dateRange/";
                    const start = $('#startDate').val().replace(/-/g, '/');
                    const end = $('#endDate').val().replace(/-/g, '/');
                    filterValue += `${start}/${end}`;
                } else if (filterType === 'NIM') {
                    filterValue = "nim/";
                    filterValue += $('#nimInput').val();
                } else if (filterType === 'Department') {
                    filterValue = "department/";
                    filterValue += $('#deptInput').val();
                } else if (filterType === 'Internship') {
                    filterValue = "internship/";
                    filterValue += $('#internshipInput').val();
                }
                    filterValue += "/" + attendanceType;
                refreshDataTable(filterValue); 
            });
        });
    </script>
</body>

</html>