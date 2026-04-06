<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Data - UC Internship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css" rel="stylesheet" />
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>

<body>
    <?= view('layout/modals') ?>
    <div class="d-flex">
        <?= view('layout/sidebar') ?>
        <div class="flex-grow-1">
            <?= view('layout/header', ["page" => "students"]) ?>
            <div class="p-4">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between mb-3">
                    <h3>Students Data</h3>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" onclick="openAddModal()">Add Student</button>
                        <input type="file" id="fileInput" accept=".xlsx, .xls" class="d-none" onchange="importData(this)">
                        <button class="btn btn-success" onclick="document.getElementById('fileInput').click()">
                            Import Excel
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="mhsTable" class="table table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Name</th>
                                <th>Major</th>
                                <th>Sub Major</th>
                                <th>Internship</th>
                                <th>Department</th>
                                <th>Is Active</th>
                                <th>Action</th>
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
        function updateIsActive(id, element) {
            $.ajax({
                url: '<?= base_url("admin/api/students/setIsActive/"); ?>' + id + '/' + (element.checked ? "1" : "0"),
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'PATCH',
                success: (res) => {
                    if (res.status === 'success') {
                    } else {
                        showAlert("Error Saving", res.message || "Unknown Error Occurred.");
                        element.checked = !element.checked; // Revert toggle
                    }
                }
            });
        }

        function populateDepartment(name = null, id = null) {
            $.ajax({
                url: '<?= base_url("admin/api/internships/department"); ?>',
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'GET',
                success: (res) => {
                    if (res.status === 'success') {
                        $('#studentModal #studentForm #department_name').empty();
                        if (name == null) $('#studentModal #studentForm #department_name').append(`<option value="" ${name == null ? "selected" : ""}>Select Department</Option>`);
                        res.departments.forEach(department => {
                            $('#studentModal #studentForm #department_name').append(`<option value="${department.name}" ${name != null && name == department.name ? "selected" : ""}>${department.name}</Option>`);
                        });
                        populateInternship(id);
                    } else {
                        showAlert("Error", res.message || "Unknown Error Occurred.");
                    }
                }
            });
        }

        function populateInternship(id = null) {
            $.ajax({
                url: '<?= base_url("admin/api/internships/department/"); ?>' + $('#studentModal #studentForm #department_name').val(),
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'GET',
                success: (res) => {
                    if (res.status === 'success') {
                        $('#studentModal #studentForm #internship_id').empty();
                        if (id == null) $('#studentModal #studentForm #internship_id').append(`<option value="" ${id == null ? "selected" : ""}>Select Internship</Option>`);
                        res.internships.forEach(internship => {
                            $('#studentModal #studentForm #internship_id').append(`<option value="${internship.id}" ${id != null && id == internship.id ? "selected" : ""}>${internship.name}</Option>`);
                        });
                    } else {
                        showAlert("Error", res.message || "Unknown Error Occurred.");
                    }
                }
            });
        }

        $(document).ready(function() {
            $('#mhsTable').DataTable({
                ajax: {
                    url: '<?= base_url("admin/api/students"); ?>',
                    "beforeSend": function(xhr) {
                        xhr.setRequestHeader('token', getCookie('token'));
                        xhr.setRequestHeader('RequestType', 'API');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '<?= csrf_hash() ?>');
                    },
                    dataSrc: function(res) {
                        if (res.status === 'success') {
                            return res.students;
                        } else {
                            showAlert("Error", res.message || "Unknown Error Occurred.");
                            return [];
                        }
                    }
                },
                ordering: true,
                order: [
                    [2, 'asc']
                ],
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'nim'
                    },
                    {
                        data: 'full_name'
                    },
                    {
                        data: 'major'
                    },
                    {
                        data: 'sub_major'
                    },
                    {
                        data: 'internship_name'
                    },
                    {
                        data: 'internship_department'
                    },
                    {
                        data: null,
                        render: (data, type, row) => `
                            <input type="checkbox" onclick="updateIsActive('${row.id}', this)" ${row.is_active == "1" ? "checked" : ""}>
                        `
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: (data, type, row) => `
                            <button class="btn btn-sm btn-outline-primary" onclick="openEditModal('${row.id}')">Edit</button>
                        `
                    }
                ],
                "drawCallback": function(settings) {
                    var api = this.api();
                    api.column(0, {
                        search: 'applied',
                        order: 'applied'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
            });

            $('#department_name').on('change', function() {
                populateInternship();
            });
        });

        $('#studentModal #studentForm').on('submit', function(e) {
            e.preventDefault();
            let formData = Object.fromEntries(new FormData($('#studentModal #studentForm')[0]));
            let studentId = $('#studentModal #studentForm #studentId').val();

            let type = studentId ? 'PUT' : 'POST';
            let url = studentId ? '<?= base_url("admin/api/students/"); ?>' + studentId : '<?= base_url("admin/api/students"); ?>';

            $.ajax({
                url: url,
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: type,
                data: JSON.stringify(formData),
                success: (res) => {
                    if (res.status === 'success') {
                        $('#studentModal').modal('hide');
                        $('#studentModal #studentForm')[0].reset();
                        $('#mhsTable').DataTable().ajax.reload();
                    } else {
                        $('#studentModal').modal('hide');
                        showAlert("Error Saving", res.message || "Unknown Error Occurred.");
                    }
                }
            });
        });

        function openAddModal() {
            $('#studentModal #modalTitle').text('Add Student');
            $('#studentModal #studentForm')[0].reset();
            $('#studentModal #studentId').val('');
            populateDepartment();
            $('#studentModal').modal('show');
        }

        function openEditModal(id) {
            $.ajax({
                url: '<?= base_url("admin/api/students/"); ?>' + id,
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'GET',
                success: (res) => {
                    if (res.status === 'success') {
                        $('#studentModal #modalTitle').text('Edit Student');
                        $('#studentModal #studentId').val(res.students.id);
                        $('#studentModal input[name="nim"]').val(res.students.nim);
                        $('#studentModal input[name="full_name"]').val(res.students.full_name);
                        $('#studentModal input[name="major"]').val(res.students.major);
                        $('#studentModal input[name="sub_major"]').val(res.students.sub_major);
                        populateDepartment(res.students.internship_department, res.students.internship_id);
                        $('#studentModal').modal('show');
                    } else {
                        showAlert("Error", res.message || "Unknown Error Occured.");
                    }
                }
            });
        }

        function importData(input) {
            let file = input.files[0];
            if (!file) return;

            let formData = new FormData();
            formData.append('file_excel', file);

            $.ajax({
                url: '<?= base_url("admin/api/students/import"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                success: (res) => {
                    if (res.status === 'success') {
                        showAlert(`Import Success!`, `Imported: ${res.data.success}, Failed: ${res.data.failed}`);
                        $('#mhsTable').DataTable().ajax.reload();
                    } else {
                        showAlert("Import Error", res.message || "Unknown Error Occurred.");
                    }
                }
            });
        }
    </script>
</body>

</html>