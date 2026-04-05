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
            <?= view('layout/header', ["page" => "users"]) ?>
            <div class="p-4">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between mb-3">
                    <h3>Internship Data</h3>
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
                success: (data) => {},
                error: (res) => {
                    $('#userModal').modal('hide');
                    showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
                }
            });
        }
        $(document).ready(function() {
            // 1. RESTful DataTables Initialization
            $('#userTable').DataTable({
                ajax: {
                    url: '<?= base_url("admin/api/users"); ?>',
                    "beforeSend": function(xhr) {
                        xhr.setRequestHeader('token', getCookie('token'));
                        xhr.setRequestHeader('RequestType', 'API');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '<?= csrf_hash() ?>');
                    },
                    dataSrc: 'users'
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
        });

        // 2. Handle Submit Form (Add & Edit)
        $('#userModal #userForm').on('submit', function(e) {
            e.preventDefault();
            let formData = Object.fromEntries(new FormData($('#userModal #userForm')[0]));
            if (!$('#userModal #userForm #userId').val()) {
                $.ajax({
                    url: '<?= base_url("admin/api/users"); ?>',
                    contentType: 'application/json',
                    headers: {
                        'token': getCookie('token'),
                        'RequestType': 'API',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    type: 'POST',
                    data: JSON.stringify(formData),
                    success: (res) => {
                        $('#userModal').modal('hide');
                        $('#userModal #userForm')[0].reset();
                        $('#userTable').DataTable().ajax.reload();
                    },
                    error: (res) => {
                        $('#userModal').modal('hide');
                        showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
                    }
                });
            } else {
                $.ajax({
                    url: '<?= base_url("admin/api/users/"); ?>' + $('#userModal #userForm #userId').val(),
                    contentType: 'application/json',
                    headers: {
                        'token': getCookie('token'),
                        'RequestType': 'API',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    },
                    type: 'PUT',
                    data: JSON.stringify(formData),
                    success: (res) => {
                        $('#userModal').modal('hide');
                        $('#userForm')[0].reset();
                        $('#userTable').DataTable().ajax.reload();
                    },
                    error: (res) => {
                        $('#userModal').modal('hide');
                        showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
                    }
                });
            }
        });

        // 3. Fungsi Buka Modal Add
        function openAddModal() {
            $('#userModal #modalTitle').text('Add Internship');
            $('#userModal #userForm')[0].reset();
            $('#userModal #userId').val('');
            $('#userModal #password').prop('required', true);
            $('#userModal').modal('show');
        }

        // 4. Fungsi Edit (Get Data)
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
                success: (data) => {
                    $('#userModal #modalTitle').text('Edit User');
                    $('#userModal #userId').val(data.users.id);
                    $('#userModal #password').prop('required', false);
                    $('#userModal input[name="full_name"]').val(data.users.full_name);
                    $('#userModal input[name="username"]').val(data.users.username);
                    $('#userModal input[name="email"]').val(data.users.email);
                    $('#userModal input[name="phone_number"]').val(data.users.phone_number);
                    $('#userModal').modal('show');
                },
                error: (res) => {
                    $('#userModal').modal('hide');
                    showAlert("Error Saving", res.message ?? "Unknown Error Occured.")
                }
            });
        }

        // 5. Fungsi Delete (Trigger Modal)
        let userIdToDelete = null;

        // function confirmDelete(id) {
        //     userIdToDelete = id;
        //     $('#deleteModalBody').html("Are you sure you want to delete this user? This action cannot be undone.");
        //     $('#deleteModal').modal('show');
        // }

        // 6. Eksekusi Delete
        // $('#confirmDeleteBtn').on('click', function() {
        //     if (userIdToDelete) {
        //         $.ajax({
        //             url: '<?= base_url("admin/api/users/"); ?>' + userIdToDelete,
        //             contentType: 'application/json',
        //             headers: {
        //                 'token': getCookie('token'),
        //                 'RequestType': 'API',
        //                 'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        //             },
        //             type: 'DELETE',
        //             success: () => {
        //                 $('#deleteModal').modal('hide');
        //                 $('#userTable').DataTable().ajax.reload();
        //                 userIdToDelete = null;
        //             }
        //         });
        //     }
        // });

        // 7. RESTful Import
        function importData(input) {
            let formData = new FormData();
            formData.append('file', input.files[0]);
            $.ajax({
                url: '<?= base_url("api/users/import"); ?>',
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
                    $('#userTable').DataTable().ajax.reload();
                }
            });
        }
    </script>
</body>

</html>