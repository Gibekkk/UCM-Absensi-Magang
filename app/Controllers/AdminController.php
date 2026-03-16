<?php

namespace App\Controllers;

use App\Models\SessionModel;
use App\Models\MahasiswaModel;
use App\Entities\Mahasiswa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminController extends BaseController
{
    protected $mhsModel;

    public function __construct()
    {
        $this->mhsModel = new MahasiswaModel();
    }
    private function getSessionData()
    {
        $token = $_COOKIE['token'] ?? '';
        $sessionModel = new SessionModel();

        // 1. Cari session berdasarkan token (Hasilnya SessionEntity)
        return $sessionModel->find($token);
    }

    public function home()
    {
        $session = $this->getSessionData();

        // 2. Kita bisa akses admin dan user langsung dari object session
        // Ini akan memicu fungsi getAdmin() di SessionEntity 
        // dan getUser() di AdminEntity secara otomatis.
        $data = [
            'admin' => $session->getAdmin(),
            'mahasiswa' => (new MahasiswaModel())->findAll()
        ];

        return view('admin/dashboard', $data);
    }

    public function insert()
    {

        // 1. Tentukan Aturan Validasi
        $rules = [
            'nim' => 'required|is_unique[mahasiswa.nim]|exact_length[13]',
            'nama' => 'required|min_length[8]',
            'jurusan' => 'required',
            'spesialisasi' => 'required',
            'angkatan' => 'required|numeric|exact_length[4]',
        ];

        // 2. Jalankan Validasi
        if (!$this->validate($rules)) {
            // Jika gagal, kembali ke dashboard dengan pesan error dan inputan sebelumnya
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->listErrors());
        }

        // 3. Jika Valid, Masukkan data ke Entity
        // Kita hanya mengambil field yang diizinkan agar aman
        $mhs = new Mahasiswa();
        $mhs->nim = $this->request->getPost('nim');
        $mhs->nama = $this->request->getPost('nama');
        $mhs->jurusan = $this->request->getPost('jurusan');
        $mhs->spesialisasi = $this->request->getPost('spesialisasi');
        $mhs->angkatan = $this->request->getPost('angkatan');

        // 4. Simpan melalui Model
        // Callback 'beforeInsert' di MahasiswaModel akan otomatis membuat UUID
        if ($this->mhsModel->insert($mhs)) {
            return redirect()->to('/admin/home')->with('success', 'Mahasiswa baru berhasil ditambahkan!');
        }

        return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
    }

    public function edit($id)
    {
        // 1. Cari data lama berdasarkan ID (UUID)
        $mhs = $this->mhsModel->find($id);

        if (!$mhs) {
            return redirect()->to('/admin/home')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // 2. Aturan Validasi
        // Perhatikan bagian is_unique: mahasiswa.nim,id,{$id}
        // Artinya: NIM harus unik di tabel mahasiswa, KECUALI untuk baris yang ID-nya adalah $id
        $rules = [
            'nim' => "required|exact_length[13]|is_unique[mahasiswa.nim,id,{$id}]",
            'nama' => 'required|min_length[8]',
            'jurusan' => 'required',
            'angkatan' => 'required|numeric|exact_length[4]',
            'spesialisasi' => 'required',
        ];

        // 3. Jalankan Validasi
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->listErrors());
        }

        // 4. Update data menggunakan Entity
        // Kita gunakan fill() untuk mengisi properti entity secara otomatis dari POST
        $mhs->fill($this->request->getPost());
        $mhs->id = $id;

        // 5. Simpan perubahan
        // Model akan mendeteksi ini sebagai UPDATE karena kita menyertakan ID
        if ($this->mhsModel->save($mhs)) {
            return redirect()->to('/admin/home')->with('success', 'Data mahasiswa berhasil diperbarui!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data.');
    }

    public function delete($id)
    {
        // 1. Cek apakah data mahasiswa ada di database
        $mhs = $this->mhsModel->find($id);

        if (!$mhs) {
            return redirect()->to('/admin/home')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // 2. Lakukan penghapusan (Soft Delete)
        // Karena di Model $useSoftDeletes = true, maka data hanya diisi deleted_at-nya
        if ($this->mhsModel->delete($id)) {
            return redirect()->to('/admin/home')->with('success', 'Data mahasiswa berhasil dihapus (Soft Delete).');
        }

        return redirect()->to('/admin/home')->with('error', 'Gagal menghapus data.');
    }

    public function export()
    {
        // 1. Ambil semua data mahasiswa (Entity)
        $mahasiswa = $this->mhsModel->findAll();

        // 2. Inisialisasi Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 3. Set Header Tabel (Baris 1)
        $sheet->setCellValue('A1', 'NIM');
        $sheet->setCellValue('B1', 'NAMA LENGKAP');
        $sheet->setCellValue('C1', 'JURUSAN');
        $sheet->setCellValue('D1', 'SPESIALISASI');
        $sheet->setCellValue('E1', 'ANGKATAN');

        // Style Header (Opsional: Tebal)
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // 4. Isi Data (Mulai Baris 2)
        $row = 2;
        foreach ($mahasiswa as $m) {
            $sheet->setCellValue('A' . $row, $m->nim);
            $sheet->setCellValue('B' . $row, $m->nama);
            $sheet->setCellValue('C' . $row, $m->jurusan);
            $sheet->setCellValue('D' . $row, $m->spesialisasi);
            $sheet->setCellValue('E' . $row, $m->angkatan);
            $row++;
        }

        // 5. Auto-size kolom agar rapi
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // 6. Proses Download ke Browser
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data_Mahasiswa_' . date('Y-m-d_His') . '.xlsx';

        // Header HTTP untuk download file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit(); // Hentikan script agar tidak ada output tambahan
    }

    public function import()
    {
        $file = $this->request->getFile('file_excel');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $path = $file->getTempName();

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($path);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $successCount = 0;
            $errorCount = 0;

            foreach ($sheetData as $index => $row) {
                if ($index == 0)
                    continue; // Lewati header

                try {
                    $this->mhsModel->insert([
                        'nim' => $row[0],
                        'nama' => $row[1],
                        'jurusan' => $row[2],
                        'spesialisasi' => $row[3],
                        'angkatan' => $row[4],
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }

            return redirect()->to('/admin/home')->with('success', "Import selesai. $successCount data berhasil, $errorCount data gagal/duplikat.");
        } else {
            return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan.');
        }
    }

    public function scan()
    {
        return view('admin/scan_qr', [
            'admin' => $this->getSessionData()->getAdmin()
        ]);
    }

    /**
     * API untuk mencari mahasiswa berdasarkan NIM (dipanggil via AJAX oleh scanner)
     */
    public function get_mahasiswa_by_nim($nim)
    {
        $mhs = $this->mhsModel->where('nim', $nim)->first();
        if ($mhs) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $mhs
            ]);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Mahasiswa tidak ditemukan'
        ]);
    }

    public function findMahasiswa($nim = null)
    {
        // 1. Pastikan NIM tidak kosong
        if ($nim === null) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'NIM tidak boleh kosong'
            ])->setStatusCode(400);
        }

        // 2. Cari mahasiswa berdasarkan NIM menggunakan Model
        // Karena returnType di model adalah Entity, $mhs akan menjadi objek MahasiswaEntity
        $mhs = $this->mhsModel->where('nim', $nim)->first();

        // 3. Jika data ditemukan
        if ($mhs) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'nim' => $mhs->nim,
                    'nama' => $mhs->nama,
                    'angkatan' => $mhs->angkatan,
                    'jurusan' => $mhs->jurusan,
                    'spesialisasi' => $mhs->spesialisasi
                ]
            ]);
        }

        // 4. Jika data tidak ditemukan
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan di sistem.'
        ])->setStatusCode(404);
    }
}