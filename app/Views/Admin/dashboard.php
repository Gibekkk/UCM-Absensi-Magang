<!DOCTYPE html>
<html lang="en">

<head>
    <title>Data Mahasiswa - UC Internship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.4/datatables.min.css" rel="stylesheet" />
    <link href="<?= base_url('css/app.css') ?>" rel="stylesheet">
</head>

<body>
    <?= view('layout/modals') ?>
    <div class="d-flex">
        <?= view('layout/sidebar') ?>
        <div class="flex-grow-1">
            <?= view('layout/header') ?>
            <div class="p-4">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between mb-3">
                    <h3>Students Data</h3>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentModal">Add Student</button>
                        <button class="btn btn-success" onclick="$('#importFile').click()">Import</button>
                        <input type="file" id="importFile" class="d-none" accept=".csv, .xlsx" onchange="importData(this)">
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
    <script src="<?= base_url('js/app.js') ?>"></script>

    <script>
        $(document).ready(function() {
            // 1. RESTful DataTables Initialization
            $('#mhsTable').DataTable({
                ajax: {
                    url: '<?= base_url("admin/students"); ?>',
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
                        data: null,
                        render: (data, type, row) => `
                            <button class="btn btn-sm btn-outline-primary" onclick="editStudent('${row.id}')">Edit</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteStudent('${row.id}')">Delete</button>
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

        // 2. Handle Submit Form (Add & Edit)
        $('#studentForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '<?= base_url("api/students/save"); ?>',
                type: 'POST',
                data: $(this).serialize(),
                success: (res) => {
                    $('#studentModal').modal('hide');
                    $('#studentForm')[0].reset();
                    $('#mhsTable').DataTable().ajax.reload();
                },
                error: () => alert('Error saving data')
            });
        });

        // 3. Fungsi Buka Modal Add
        function openAddModal() {
            $('#modalTitle').text('Add Student');
            $('#studentForm')[0].reset();
            $('#studentId').val('');
            $('#studentModal').modal('show');
        }

        // 4. Fungsi Edit (Get Data)
        function editStudent(id) {
            $.get('<?= base_url("api/students/get/"); ?>' + id, function(data) {
                $('#modalTitle').text('Edit Student');
                $('#studentId').val(data.id);
                $('input[name="nim"]').val(data.nim);
                $('input[name="full_name"]').val(data.full_name);
                $('input[name="major"]').val(data.major);
                $('input[name="sub_major"]').val(data.sub_major);
                $('#studentModal').modal('show');
            });
        }

        // 5. Fungsi Delete (Trigger Modal)
        let studentIdToDelete = null;

        function confirmDelete(id) {
            studentIdToDelete = id;
            $('#deleteModal').modal('show');
        }

        // 6. Eksekusi Delete
        $('#confirmDeleteBtn').on('click', function() {
            if (studentIdToDelete) {
                $.ajax({
                    url: '<?= base_url("api/students/delete/"); ?>' + studentIdToDelete,
                    type: 'DELETE',
                    success: () => {
                        $('#deleteModal').modal('hide');
                        $('#mhsTable').DataTable().ajax.reload();
                        studentIdToDelete = null;
                    }
                });
            }
        });

        // 7. RESTful Import
        function importData(input) {
            let formData = new FormData();
            formData.append('file', input.files[0]);
            $.ajax({
                url: '<?= base_url("api/students/import"); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: () => {
                    alert('Import Success');
                    $('#mhsTable').DataTable().ajax.reload();
                }
            });
        }
    </script>
</body>

</html>