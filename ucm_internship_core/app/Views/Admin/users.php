<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Data - UC Internship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css" rel="stylesheet" />
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>

<body>
    <?= view('layout/modals') ?>
    <div class="d-flex">
        <?= view('layout/sidebar') ?>
        <div class="flex-grow-1">
            <?= view('layout/header', ["page" => "users"]) ?>
            <div class="p-4">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between mb-3">
                    <h3>User Data</h3>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" onclick="openAddModal()">Add User</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="userTable" class="table table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone Number</th>
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
                url: '<?= base_url("admin/api/users/setIsActive/"); ?>' + id + '/' + (element.checked ? "1" : "0"),
                contentType: 'application/json',
                headers: {
                    'token': getCookie('token'),
                    'RequestType': 'API',
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                type: 'PATCH',
                complete: (xhr) => {
    const res = JSON.parse(xhr.responseText);
                    if (res.status == 'success') {
                        showAlert("Error Saving", res.message || "Unknown Error Occurred.");
                        element.checked = !element.checked; // Revert toggle
                    }
                }
            });
        }

        $(document).ready(function() {
            $('#userTable').DataTable({
                ajax: {
                    url: '<?= base_url("admin/api/users"); ?>',
                    "beforeSend": function(xhr) {
                        xhr.setRequestHeader('token', getCookie('token'));
                        xhr.setRequestHeader('RequestType', 'API');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '<?= csrf_hash() ?>');
                    },
                    dataSrc: function(res) {
                        if (res.status == 'success') {
                            return res.users;
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
                        data: 'full_name'
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'phone_number'
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

        $('#userModal #userForm').on('submit', function(e) {
            e.preventDefault();
            let formData = Object.fromEntries(new FormData($('#userModal #userForm')[0]));
            let userId = $('#userModal #userForm #userId').val();
            
            let type = userId ? 'PUT' : 'POST';
            let url = userId ? '<?= base_url("admin/api/users/"); ?>' + userId : '<?= base_url("admin/api/users"); ?>';

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
                    if (res.status == 'success') {
                        $('#userModal').modal('hide');
                        $('#userForm')[0].reset();
                        $('#userTable').DataTable().ajax.reload();
                    } else {
                        $('#userModal').modal('hide');
                        showAlert("Error Saving", res.message || "Unknown Error Occurred.");
                    }
                }
            });
        });

        function openAddModal() {
            $('#userModal #modalTitle').text('Add User');
            $('#userModal #userForm')[0].reset();
            $('#userModal #userId').val('');
            $('#userModal #password').prop('required', true);
            $('#userModal').modal('show');
        }

        function openEditModal(id) {
            $.ajax({
                url: '<?= base_url("admin/api/users/"); ?>' + id,
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
                        $('#userModal #modalTitle').text('Edit User');
                        $('#userModal #userId').val(res.users.id);
                        $('#userModal #password').prop('required', false);
                        $('#userModal input[name="full_name"]').val(res.users.full_name);
                        $('#userModal input[name="username"]').val(res.users.username);
                        $('#userModal input[name="email"]').val(res.users.email);
                        $('#userModal input[name="phone_number"]').val(res.users.phone_number);
                        $('#userModal').modal('show');
                    } else {
                        showAlert("Error", res.message || "Unknown Error Occurred.");
                    }
                }
            });
        }

        function importData(input) {
            let formData = new FormData();
            formData.append('file', input.files[0]);
            $.ajax({
                url: '<?= base_url("api/users/import"); ?>',
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
                    if (res.status == 'success') {
                        $('#userTable').DataTable().ajax.reload();
                    } else {
                        showAlert("Import Error", res.message || "Unknown Error Occurred.");
                    }
                }
            });
        }
    </script>
</body>

</html>