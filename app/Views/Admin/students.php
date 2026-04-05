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
                url: '<?= base_url("admin/api/internships/setIsActive/"); ?>' + id + '/' + (element.checked ? "1" : "0"),
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'PATCH',
                success: (data) => {},
                error: (res) => {
                    $('#internshipModal').modal('hide');
                    showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
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
                    $('#studentModal #studentForm #department_name').empty();
                    if (name == null) $('#studentModal #studentForm #department_name').append(`<option value="" ${name == null ? "selected" : ""}>Select Department</Option>`);
                    res.departments.forEach(department => {
                        $('#studentModal #studentForm #department_name').append(`<option value="${department.name}" ${name != null && name == department.name ? "selected" : ""}>${department.name}</Option>`);
                    });
                    populateInternship(id);
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
                    $('#studentModal #studentForm #internship_id').empty();
                    if (id == null) $('#studentModal #studentForm #internship_id').append(`<option value="" ${id == null ? "selected" : ""}>Select Internship</Option>`);
                    res.internships.forEach(internship => {
                        $('#studentModal #studentForm #internship_id').append(`<option value="${internship.id}" ${id != null && id == internship.id ? "selected" : ""}>${internship.name}</Option>`);
                    });
                }
            });
        }

        $(document).ready(function() {
            // 1. RESTful DataTables Initialization
            $('#mhsTable').DataTable({
                ajax: {
                    url: '<?= base_url("admin/api/students"); ?>',
                    "beforeSend": function(xhr) {
                        xhr.setRequestHeader('token', getCookie('token'));
                        xhr.setRequestHeader('RequestType', 'API');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '<?= csrf_hash() ?>');
                    },
                    dataSrc: 'students'
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
                        // <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('${row.id}')">Delete</button>
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

            $('#studentModal #studentForm #department_name').focus(populateDepartment());
            $('#studentModal #studentForm #department_name').change(populateInternship());
        });

        // 2. Handle Submit Form (Add & Edit)
        $('#studentModal #studentForm').on('submit', function(e) {
            e.preventDefault();
            let formData = Object.fromEntries(new FormData($('#studentModal #studentForm')[0]));
            if (!$('#studentModal #studentForm #studentId').val()) {
                $.ajax({
                    url: '<?= base_url("admin/api/students"); ?>',
                    contentType: 'application/json',
                    headers: {
                        'token': getCookie('token'),
                        'RequestType': 'API',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    type: 'POST',
                    data: JSON.stringify(formData),
                    success: (res) => {
                        $('#studentModal').modal('hide');
                        $('#studentModal #studentForm')[0].reset();
                        $('#mhsTable').DataTable().ajax.reload();
                    },
                    error: (res) => {
                        $('#studentModal').modal('hide');
                        showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
                    }
                });
            } else {
                $.ajax({
                    url: '<?= base_url("admin/api/students/"); ?>' + $('#studentModal #studentForm #studentId').val(),
                    contentType: 'application/json',
                    headers: {
                        'token': getCookie('token'),
                        'RequestType': 'API',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    type: 'PUT',
                    data: JSON.stringify(formData),
                    success: (res) => {
                        $('#studentModal').modal('hide');
                        $('#studentForm')[0].reset();
                        $('#mhsTable').DataTable().ajax.reload();
                    },
                    error: (res) => {
                        $('#studentModal').modal('hide');
                        showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
                    }
                });
            }
        });

        // 3. Fungsi Buka Modal Add
        function openAddModal() {
            populateDepartment();
            $('#studentModal #modalTitle').text('Add Student');
            $('#studentModal #studentForm')[0].reset();
            $('#studentModal #studentId').val('');
            $('#studentModal').modal('show');
        }

        // 4. Fungsi Edit (Get Data)
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
                success: (data) => {
                    $('#studentModal #modalTitle').text('Edit Student');
                    $('#studentModal #studentId').val(data.students.id);
                    $('#studentModal input[name="nim"]').val(data.students.nim);
                    $('#studentModal input[name="full_name"]').val(data.students.full_name);
                    $('#studentModal input[name="major"]').val(data.students.major);
                    $('#studentModal input[name="sub_major"]').val(data.students.sub_major);
                    populateDepartment(data.students.internship_department, data.students.internship_id);
                    $('#studentModal').modal('show');
                },
                error: (res) => {
                    $('#studentModal').modal('hide');
                    showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
                }
            });
        }

        // 5. Fungsi Delete (Trigger Modal)
        // let studentIdToDelete = null;

        // function confirmDelete(id) {
        //     studentIdToDelete = id;
        //     $('#deleteModalBody').html("Are you sure you want to delete this student? This action cannot be undone.");
        //     $('#deleteModal').modal('show');
        // }

        // 6. Eksekusi Delete
        // $('#confirmDeleteBtn').on('click', function() {
        //     if (studentIdToDelete) {
        //         $.ajax({
        //             url: '<?= base_url("admin/api/students/"); ?>' + studentIdToDelete,
        //             contentType: 'application/json',
        //             headers: {
        //                 'token': getCookie('token'),
        //                 'RequestType': 'API',
        //                 'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        //             },
        //             type: 'DELETE',
        //             success: () => {
        //                 $('#deleteModal').modal('hide');
        //                 $('#mhsTable').DataTable().ajax.reload();
        //                 studentIdToDelete = null;
        //             }
        //         });
        //     }
        // });

        // 7. RESTful Import
        function importData(input) {
            let file = input.files[0];
            if (!file) return;

            let formData = new FormData();
            formData.append('file_excel', file);

            $.ajax({
                url: '<?= base_url("admin/api/students/import"); ?>',
                type: 'POST',
                data: formData,
                processData: false, // WAJIB
                contentType: false, // WAJIB
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                success: (res) => {
                    if (res.status === 'success') {
                        console.log(`Import Success! Imported: ${res.data.success}, Failed: ${res.data.failed}`);
                        showAlert(`Import Success!`, `Imported: ${res.data.success}, Failed: ${res.data.failed}`);
                        $('#mhsTable').DataTable().ajax.reload();
                    } else {
                        console.error('Error: ' + res.message);
                    }
                },
                error: (xhr) => {
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem';
                    console.error('Import Failed: ' + msg);
                }
            });
        }
    </script>
</body>

</html>