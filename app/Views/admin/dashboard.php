<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Mahasiswa</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS (Font Inter) -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

    <style>
        /* Tambahan sedikit agar tabel terlihat lebih lega */
        .table p {
            margin-bottom: 0;
        }

        .btn-sm {
            border-radius: 6px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Sistem Mahasiswa</a>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-white me-3 d-none d-md-block">
                    Halo, <strong><?= $admin->nama_admin ?></strong>
                </span>
                <a href="<?= base_url('logout') ?>" class="btn btn-light btn-sm fw-semibold text-primary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <!-- Header Section -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h4 class="fw-bold mb-0">Data Mahasiswa</h4>
                <p class="text-muted small">Kelola informasi mahasiswa aktif di sini.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <!-- Tombol Export -->
                <a href="<?= base_url('admin/export') ?>" class="btn btn-outline-primary btn-sm me-1">
                    Export Excel
                </a>

                <!-- Tombol Import (Memicu input file tersembunyi) -->
                <button type="button" class="btn btn-outline-success btn-sm me-1"
                    onclick="document.getElementById('inputExcel').click()">
                    Import Excel
                </button>

                <a href="<?= base_url('admin/scan') ?>" class="btn btn-outline-secondary btn-sm me-1">
                    Scan QR
                </a>

                <button type="button" class="btn btn-success btn-sm" onclick="showAddModal()">
                    + Tambah
                </button>

                <!-- Form Import Tersembunyi -->
                <form action="<?= base_url('admin/import') ?>" method="POST" enctype="multipart/form-data"
                    id="formImport" class="d-none">
                    <?= csrf_field() ?>
                    <input type="file" name="file_excel" id="inputExcel" accept=".xlsx, .xls"
                        onchange="document.getElementById('formImport').submit()">
                </form>
            </div>
        </div>

        <!-- Alert Success/Error -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Table Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="tableMahasiswa" class="table table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Angkatan</th>
                                <th>Spesialisasi</th>
                                <th>Jurusan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mahasiswa as $m): ?>
                                <tr>
                                    <td><?= $m->nim ?></td>
                                    <td><?= $m->nama ?></td>
                                    <td><?= $m->angkatan ?></td>
                                    <td><?= $m->spesialisasi ?></td>
                                    <td><?= $m->jurusan ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm"
                                            onclick="showEditModal('<?= $m->id ?>', '<?= $m->nim ?>', '<?= $m->nama ?>', '<?= $m->jurusan ?>', '<?= $m->spesialisasi ?>', '<?= $m->angkatan ?>')">
                                            Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmDelete('<?= $m->id ?>', '<?= $m->nama ?>')">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL (INSERT & EDIT) -->
    <div class="modal fade" id="mhsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="" method="POST" id="formMhs" class="modal-content border-0 shadow">
                <?= csrf_field() ?>
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">NIM</label>
                        <input type="text" name="nim" id="inputNim" class="form-control"
                            placeholder="Contoh: 0806022310001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" id="inputNama" class="form-control"
                            placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Jurusan</label>
                        <input type="text" name="jurusan" id="inputJurusan" class="form-control"
                            placeholder="Contoh: Teknik Informatika" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Spesialisasi</label>
                        <input type="text" name="spesialisasi" id="inputSpesialisasi" class="form-control"
                            placeholder="Contoh: Web Development" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Angkatan</label>
                        <input type="text" name="angkatan" id="inputAngkatan" class="form-control"
                            placeholder="Contoh: 2021" required>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Hapus Data?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Anda yakin ingin menghapus <strong id="deleteName"></strong>? Data
                        ini
                        akan dipindahkan ke sampah.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="btnConfirmDelete" class="btn btn-danger btn-sm px-3">Ya, Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
            <!-- Menampilkan error dalam bentuk list jika ada banyak -->
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inisialisasi DataTables
            $('#tableMahasiswa').DataTable({
                "columnDefs": [
                    {
                        "orderable": false,
                        "searchable": false,
                        "targets": [5]
                    }
                ],
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": {
                        "next": "Lanjut",
                        "previous": "Kembali"
                    }
                }
            });
        });

        // Inisialisasi Modal Bootstrap
        const mhsModal = new bootstrap.Modal(document.getElementById('mhsModal'));
        const formMhs = document.getElementById('formMhs');
        const modalTitle = document.getElementById('modalTitle');

        // Fungsi Tampilkan Modal Tambah
        function showAddModal() {
            modalTitle.innerText = "Tambah Mahasiswa";
            formMhs.action = "<?= base_url('admin/insert') ?>";
            formMhs.reset();
            mhsModal.show();
        }

        // Fungsi Tampilkan Modal Edit
        function showEditModal(id, nim, nama, jurusan, spesialisasi, angkatan) {
            modalTitle.innerText = "Edit Mahasiswa";
            formMhs.action = "<?= base_url('admin/edit/') ?>" + id;

            document.getElementById('inputNim').value = nim;
            document.getElementById('inputNama').value = nama;
            document.getElementById('inputJurusan').value = jurusan;
            document.getElementById('inputSpesialisasi').value = spesialisasi;
            document.getElementById('inputAngkatan').value = angkatan;

            mhsModal.show();
        }

        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const btnConfirmDelete = document.getElementById('btnConfirmDelete');
        const deleteName = document.getElementById('deleteName');

        function confirmDelete(id, name) {
            // Set nama mahasiswa di teks modal
            deleteName.innerText = name;

            // Set URL hapus ke tombol "Ya, Hapus"
            btnConfirmDelete.href = "<?= base_url('admin/delete/') ?>" + id;

            // Tampilkan modal
            deleteModal.show();
        }
    </script>

</body>

</html>