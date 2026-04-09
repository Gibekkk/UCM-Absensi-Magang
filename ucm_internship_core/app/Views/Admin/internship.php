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
                complete: (xhr) => {
    const res = JSON.parse(xhr.responseText);
                    if (res.status !== "success") {
                        showAlert("Error", res.message || "Unknown Error Occurred");
                        element.checked = !element.checked; // Revert checkbox
                    }
                }
            });
        }

        $(document).ready(function() {
            $('#internshipTable').DataTable({
                ajax: {
                    url: '<?= base_url("admin/api/internships"); ?>',
                    "beforeSend": function(xhr) {
                        xhr.setRequestHeader('token', getCookie('token'));
                        xhr.setRequestHeader('RequestType', 'API');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '<?= csrf_hash() ?>');
                    },
                    dataSrc: function(res) {
                        if (res.status === "success") {
                            return res.internships;
                        } else {
                            showAlert("Error", res.message || "Unknown Error Occurred");
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
        });

        $('#internshipModal #internshipForm').on('submit', function(e) {
            e.preventDefault();
            let formData = Object.fromEntries(new FormData($('#internshipModal #internshipForm')[0]));
            let id = $('#internshipModal #internshipForm #internshipId').val();
            
            let type = id ? 'PUT' : 'POST';
            let url = id ? '<?= base_url("admin/api/internships/"); ?>' + id : '<?= base_url("admin/api/internships"); ?>';

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
                complete: (xhr) => {
    const res = JSON.parse(xhr.responseText);
                    if (res.status === "success") {
                        $('#internshipModal').modal('hide');
                        $('#internshipModal #internshipForm')[0].reset();
                        $('#internshipTable').DataTable().ajax.reload();
                    } else {
                        showAlert("Error Saving", res.message || "Unknown Error Occurred");
                    }
                }
            });
        });

        function openAddModal() {
            $('#internshipModal #modalTitle').text('Add Internship');
            $('#internshipModal #internshipForm')[0].reset();
            $('#internshipModal #internshipId').val('');
            $('#internshipModal').modal('show');
        }

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
                complete: (xhr) => {
    const res = JSON.parse(xhr.responseText);
                    if (res.status === "success") {
                        $('#internshipModal #modalTitle').text('Edit Internship');
                        $('#internshipModal #internshipId').val(res.internships.id);
                        $('#internshipModal input[name="name"]').val(res.internships.name);
                        $('#internshipModal input[name="department"]').val(res.internships.department);
                        $('#internshipModal input[name="head_department"]').val(res.internships.head_department);
                        $('#internshipModal input[name="start_date"]').val(res.internships.start_date.date.split(" ")[0]);
                        $('#internshipModal input[name="end_date"]').val(res.internships.end_date.date.split(" ")[0]);
                        $('#internshipModal').modal('show');
                    } else {
                        showAlert("Error", res.message || "Unknown Error Occurred");
                    }
                }
            });
        }

        function importData(input) {
            let formData = new FormData();
            formData.append('file', input.files[0]);
            $.ajax({
                url: '<?= base_url("api/internships/import"); ?>',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                complete: (xhr) => {
    const res = JSON.parse(xhr.responseText);
                    if (res.status === "success") {
                        $('#internshipTable').DataTable().ajax.reload();
                    } else {
                        showAlert("Import Error", res.message || "Unknown Error Occurred");
                    }
                }
            });
        }
    </script>
</body>

</html>