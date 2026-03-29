<!DOCTYPE html>
<html lang="en">

<head>
    <title>Internship Data - UC Internship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css" rel="stylesheet" />
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>

<body>
    <?= view('layout/modals') ?>
    <div class="d-flex">
        <?= view('layout/sidebar') ?>
        <div class="flex-grow-1">
            <?= view('layout/header', ["page" => "internships"]) ?>
            <div class="p-4">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between mb-3">
                    <h3>Internship Data</h3>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" onclick="openAddModal()">Add Internship</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="internshipTable" class="table table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Head of Department</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Number of Students</th>
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
        $(document).ready(function() {
            // 1. RESTful DataTables Initialization
            $('#internshipTable').DataTable({
                ajax: {
                    url: '<?= base_url("admin/api/internships"); ?>',
                    "beforeSend": function(xhr) {
                        xhr.setRequestHeader('token', getCookie('token'));
                        xhr.setRequestHeader('RequestType', 'API');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '<?= csrf_hash() ?>');
                    },
                    dataSrc: 'internships'
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
                        data: 'name'
                    },
                    {
                        data: 'department'
                    },
                    {
                        data: 'head_department'
                    },
                    {
                        data: null,
                        render: (data, type, row) => `
                            ${new Date(row.start_date.date.split(" ")[0]).toDateString()}
                        `
                    },
                    {
                        data: null,
                        render: (data, type, row) => `
                            ${new Date(row.end_date.date.split(" ")[0]).toDateString()}
                        `
                    },
                    {
                        data: 'students_count'
                    },
                    {
                        data: null,
                        render: (data, type, row) => `
                        ${row.is_active == "1" ? "True" : "False"}
                        `
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: (data, type, row) => `
                            <button class="btn btn-sm btn-outline-primary" onclick="openEditModal('${row.id}')">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('${row.id}')">Delete</button>
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
        });

        // 2. Handle Submit Form (Add & Edit)
        $('#internshipModal #internshipForm').on('submit', function(e) {
            e.preventDefault();
            let formData = Object.fromEntries(new FormData($('#internshipModal #internshipForm')[0]));
            if (!$('#internshipModal #internshipForm #internshipId').val()) {
                $.ajax({
                    url: '<?= base_url("admin/api/internships"); ?>',
                    contentType: 'application/json',
                    headers: {
                        'token': getCookie('token'),
                        'RequestType': 'API',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    type: 'POST',
                    data: JSON.stringify(formData),
                    success: (res) => {
                        $('#internshipModal').modal('hide');
                        $('#internshipModal #internshipForm')[0].reset();
                        $('#internshipTable').DataTable().ajax.reload();
                    },
                    error: (res) => {
                        $('#internshipModal').modal('hide');
                        showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
                    }
                });
            } else {
                $.ajax({
                    url: '<?= base_url("admin/api/internships/"); ?>' + $('#internshipModal #internshipForm #internshipId').val(),
                    contentType: 'application/json',
                    headers: {
                        'token': getCookie('token'),
                        'RequestType': 'API',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    type: 'PUT',
                    data: JSON.stringify(formData),
                    success: (res) => {
                        $('#internshipModal').modal('hide');
                        $('#internshipForm')[0].reset();
                        $('#internshipTable').DataTable().ajax.reload();
                    },
                    error: (res) => {
                        $('#internshipModal').modal('hide');
                        showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
                    }
                });
            }
        });

        // 3. Fungsi Buka Modal Add
        function openAddModal() {
            $('#internshipModal #modalTitle').text('Add Internship');
            $('#internshipModal #internshipForm')[0].reset();
            $('#internshipModal #internshipId').val('');
            $('#internshipModal').modal('show');
        }

        // 4. Fungsi Edit (Get Data)
        function openEditModal(id) {
            $.ajax({
                url: '<?= base_url("admin/api/internships/"); ?>' + id,
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'GET',
                success: (data) => {
                    $('#internshipModal #modalTitle').text('Edit Internship');
                    $('#internshipModal #internshipId').val(data.internships.id);
                    $('#internshipModal input[name="name"]').val(data.internships.name);
                    $('#internshipModal input[name="department"]').val(data.internships.department);
                    $('#internshipModal input[name="head_department"]').val(data.internships.head_department);
                    $('#internshipModal input[name="start_date"]').val(data.internships.start_date.date.split(" ")[0]);
                    $('#internshipModal input[name="end_date"]').val(data.internships.end_date.date.split(" ")[0]);
                    $('#internshipModal input[name="is_active"]').prop("checked", data.internships.is_active == "1" ? true : false);
                    $('#internshipModal').modal('show');
                },
                error: (res) => {
                    $('#internshipModal').modal('hide');
                    showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
                }
            });
        }

        // 5. Fungsi Delete (Trigger Modal)
        let internshipIdToDelete = null;

        function confirmDelete(id) {
            internshipIdToDelete = id;
            $('#deleteModalBody').html("Are you sure you want to delete this internship? This action cannot be undone.");
            $('#deleteModal').modal('show');
        }

        // 6. Eksekusi Delete
        $('#confirmDeleteBtn').on('click', function() {
            if (internshipIdToDelete) {
                $.ajax({
                    url: '<?= base_url("admin/api/internships/"); ?>' + internshipIdToDelete,
                    contentType: 'application/json',
                    headers: {
                        'token': getCookie('token'),
                        'RequestType': 'API',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    type: 'DELETE',
                    success: () => {
                        $('#deleteModal').modal('hide');
                        $('#internshipTable').DataTable().ajax.reload();
                        internshipIdToDelete = null;
                    }
                });
            }
        });

        // 7. RESTful Import
        function importData(input) {
            let formData = new FormData();
            formData.append('file', input.files[0]);
            $.ajax({
                url: '<?= base_url("api/internships/import"); ?>',
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: () => {
                    console.error('Import Success');
                    $('#internshipTable').DataTable().ajax.reload();
                }
            });
        }
    </script>
</body>

</html>