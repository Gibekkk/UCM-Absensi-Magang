<?php

namespace App\Controllers;

use App\Models\Master\StudentModel;
use App\Models\Master\SessionModel;
use App\Models\Internship\InternshipStudentModel;
use App\Models\Internship\InternshipModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends BaseController
{
    protected $studentModel;
    protected $sessionModel;
    protected $internshipStudentModel;
    protected $internshipModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->sessionModel = new SessionModel();
        $this->internshipStudentModel = new InternshipStudentModel();
        $this->internshipModel = new InternshipModel();
    }

    public function getStudents($id = null)
    {
        $students = [];
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();
        if ($id != null) {
            if ($user->is_super_admin == 1) {
                $queryRes = $this->studentModel->where('id', $id)->findAll();
            } else {
                $queryRes = $this->studentModel->where('id', $id)->where('created_by', $user->id)->findAll();
            }

            if (count($queryRes) == 0)
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Student Not Found.'
                ])->setStatusCode(404);

            foreach ($queryRes as $student) {
                $internship = $student->getInternshipStudent()->getInternship();

                $students = [
                    'id'                    => $student->id,
                    'nim'                   => $student->nim,
                    'full_name'             => $student->full_name,
                    'major'                 => $student->major,
                    'sub_major'             => $student->sub_major,
                    'is_active'             => $student->is_active,
                    'created_date'          => $student->created_date,
                    'created_by'            => $student->created_by,
                    'modified_date'         => $student->modified_date,
                    'modified_by'           => $student->modified_by,
                    'internship_name'       => $internship->name,
                    'internship_department' => $internship->department,
                    'internship_id'         => $internship->id,
                ];
            }
        } else {
            if ($user->is_super_admin == 1) {
                $queryRes = $this->studentModel->findAll();
            } else {
                $queryRes = $this->studentModel->where('created_by', $user->id)->findAll();
            }

            foreach ($queryRes as $student) {
                $internship = $student->getInternshipStudent()->getInternship();

                $row = [
                    'id'                    => $student->id,
                    'nim'                   => $student->nim,
                    'full_name'             => $student->full_name,
                    'major'                 => $student->major,
                    'sub_major'             => $student->sub_major,
                    'is_active'             => $student->is_active,
                    'created_date'          => $student->created_date,
                    'created_by'            => $student->created_by,
                    'modified_date'         => $student->modified_date,
                    'modified_by'           => $student->modified_by,
                    'internship_name'       => $internship->name,
                    'internship_department' => $internship->department,
                    'internship_id'         => $internship->id,
                ];

                $students[] = $row;
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'students' => $students
        ]);
    }

    public function addStudent()
    {
        $token = $this->request->getHeaderLine('token');
        $id = $this->sessionModel->where('id', $token)->first()->getUser()->id;

        $data = $this->request->getJSON(true);
        $student = [
            'nim' => $data['nim'],
            'full_name' => $data['full_name'],
            'major' => $data['major'],
            'sub_major' => $data['sub_major'],
            'is_active' => "1",
            'created_by' => $id,
            'modified_by' => $id,
        ];

        $internshipData = $this->internshipModel->where('id', $data['internship_id'])->where('is_active', 1)->first();
        if ($internshipData) {
            // Jika ini error, hal yang normal, kode ini bekerja dengan baik
            if ($internshipData->created_by == $id || $user->is_super_admin == 1) {
                $studentSameData = $this->studentModel
                    ->where('nim', $student['nim'])
                    ->where('full_name', $student['full_name'])
                    ->where('major', $student['major'])
                    ->where('sub_major', $student['sub_major'])
                    ->where('is_active', $student['is_active'])
                    ->findAll();
                if (count($studentSameData) == 0) {
                    if ($this->studentModel->insert($student)) {
                        $studentId = $this->studentModel->where('nim', $data['nim'])->where('is_active', 1)->first()->id;
                        $this->internshipStudentModel->insert([
                            "student_id" => $studentId,
                            "internship_id" => $data["internship_id"],
                            "is_active" => "1",
                            "created_by" => $id,
                            "modified_by" => $id,
                        ]);

                        return $this->response->setJSON([
                            'status' => 'success',
                            'message' => 'Student Added.'
                        ]);
                    }
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'This User is Still Active'
                    ])->setStatusCode(403);
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'You Do Not Have Access.'
                ])->setStatusCode(402);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }

    public function editStudent($id)
    {
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();
        $userId = $user->id;

        $data = $this->request->getJSON(true);
        $student = [
            'nim' => $data['nim'],
            'full_name' => $data['full_name'],
            'major' => $data['major'],
            'sub_major' => $data['sub_major'],
            'modified_by' => $userId,
        ];

        $studentData = $this->studentModel->find($id);
        if ($studentData) {
            $internshipData = $this->internshipModel->where('id', $data['internship_id'])->where('is_active', 1)->first();
            if ($internshipData) {
                if ($user->is_super_admin || $studentData->created_by == $userId) {
                    $studentSameData = $this->studentModel
                        ->where('nim', $student['nim'])
                        ->where('full_name', $student['full_name'])
                        ->where('major', $student['major'])
                        ->where('sub_major', $student['sub_major'])
                        ->where('is_active', 1)
                        ->where('id !=', $id)
                        ->findAll();
                    if (count($studentSameData) == 0) {
                        if ($this->studentModel->update($id, $student)) {
                            // Jika ini error, hal yang normal, kode ini bekerja dengan baik
                            if ($studentData->getInternshipStudent()->internship_id != $data['internship_id']) {
                                $this->internshipStudentModel->update($studentData->getInternshipStudent()->id, [
                                    "internship_id" => $data["internship_id"],
                                    "modified_by" => $userId,
                                ]);
                            }

                            return $this->response->setJSON([
                                'status' => 'success',
                                'message' => 'Student Edited.'
                            ]);
                        }
                    } else {
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => 'This User is Still Active.'
                        ])->setStatusCode(403);
                    }
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Internship Not Found.'
                ])->setStatusCode(404);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Student Not Found.'
            ])->setStatusCode(404);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }

    public function setIsActive($id, $isActive)
    {
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();
        $userId = $user->id;

        $student = [
            'is_active' => $isActive,
            'modified_by' => $userId,
        ];

        $studentData = $this->studentModel->find($id);
        if ($studentData) {
            $studentSameData = $this->studentModel
                ->where('nim', $studentData->nim)
                ->where('full_name', $studentData->full_name)
                ->where('major', $studentData->major)
                ->where('sub_major', $studentData->sub_major)
                ->where('id !=', $studentData->id)
                ->where('is_active', 1)
                ->findAll();
            if (count($studentSameData) == 0) {
                if ($user->is_super_admin || $studentData->created_by == $userId) {
                    $internshipData = $this->internshipModel->where('id', $studentData->getInternshipStudent()->internship_id)->where('is_active', 1)->first();
                    if ($internshipData || $isActive == 0) {
                        if ($this->studentModel->update($id, $student)) {
                            // Jika ini error, hal yang normal, kode ini bekerja dengan baik
                            if ($studentData->getInternshipStudent()->is_active != $isActive) {
                                $this->internshipStudentModel->update($studentData->getInternshipStudent()->id, [
                                    "is_active" => $isActive,
                                    "modified_by" => $userId,
                                ]);
                            }

                            return $this->response->setJSON([
                                'status' => 'success',
                                'message' => 'Student Edited.'
                            ]);
                        }
                    } else {
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => 'Internship Not Active.'
                        ])->setStatusCode(403);
                    }
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Student Already Exist.'
                ])->setStatusCode(403);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Student Not Found.'
            ])->setStatusCode(404);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }

    public function deleteStudent($id)
    {
        $student = $this->studentModel->find($id);
        if ($student) {
            $internshipStudent = $student->getInternshipStudent();
            if ($this->studentModel->delete($id) && $this->internshipStudentModel->delete($internshipStudent->id)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Student Deleted.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Student Not Found.'
            ])->setStatusCode(404);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }
public function importStudents()
{
    $file = $this->request->getFile('file_excel');
    $token = $this->request->getHeaderLine('token');

    // Validasi Session & User
    $session = $this->sessionModel->where('id', $token)->first();
    if (!$session) {
        return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
    }
    $user = $session->getUser();
    $userId = $user->id;

    // Validasi File
    if (!$file || !$file->isValid() || $file->hasMoved()) {
        return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'File tidak valid']);
    }

    try {
        $path = $file->getTempName();
        $spreadsheet = IOFactory::load($path);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $successCount = 0;
        $errorCount = 0;

        foreach ($sheetData as $index => $row) {
            if ($index == 0) continue; // Skip header baris pertama

            // Mapping Excel: NIM(0), Full Name(1), Major(2), Sub Major(3), Internship Name(4)
            $rawNim = (string)($row[0] ?? '');
            $nim = str_replace("'", "", $rawNim);
            $fullName = $row[1] ?? null;
            $major = $row[2] ?? null;
            $subMajor = $row[3] ?? null;
            $internshipName = $row[4] ?? null;
            $internshipDepartment = $row[5] ?? null;

            if (empty($nim) || empty($fullName) || empty($internshipName) || empty($internshipDepartment)) {
                $errorCount++;
                continue;
            }

            // 1. Cari Internship berdasarkan Nama dan pastikan Aktif (Sesuai logika addStudent)
            $internshipData = $this->internshipModel->where('name', $internshipName)->where('department', $internshipDepartment)->where('is_active', 1)->first();

            if ($internshipData) {
                // 2. Validasi Hak Akses (Sesuai logika addStudent)
                if ($internshipData->created_by == $userId || $user->is_super_admin == 1) {
                    
                    // 3. Cek Duplikasi Data Student yang masih aktif (Sesuai logika addStudent)
                    $studentSameData = $this->studentModel
                        ->where('nim', $nim)
                        ->where('full_name', $fullName)
                        ->where('major', $major)
                        ->where('sub_major', $subMajor)
                        ->where('is_active', "1")
                        ->findAll();

                    if (count($studentSameData) == 0) {
                        $db = \Config\Database::connect();
                        $db->transStart();

                        // Insert Student
                        $this->studentModel->insert([
                            'nim'         => $nim,
                            'full_name'   => $fullName,
                            'major'       => $major,
                            'sub_major'   => $subMajor,
                            'is_active'   => "1",
                            'created_by'  => $userId,
                            'modified_by' => $userId,
                        ]);

                        $studentId = $this->studentModel
                        ->where('nim', $nim)
                        ->where('full_name', $fullName)
                        ->where('major', $major)
                        ->where('sub_major', $subMajor)
                        ->where('is_active', "1")
                        ->first()->id;

                        // Insert Internship Student (Relasi)
                        $this->internshipStudentModel->insert([
                            "student_id"    => $studentId,
                            "internship_id" => $internshipData->id,
                            "is_active"     => "1",
                            "created_by"    => $userId,
                            "modified_by"   => $userId,
                        ]);

                        $db->transComplete();

                        if ($db->transStatus()) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    } else {
                        // Student sudah ada dan aktif
                        $errorCount++;
                    }
                } else {
                    // Tidak punya akses ke internship ini
                    $errorCount++;
                }
            } else {
                // Internship tidak ditemukan atau tidak aktif
                $errorCount++;
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Proses impor selesai',
            'data'   => [
                'success' => $successCount,
                'failed'  => $errorCount
            ]
        ]);

    } catch (\Exception $e) {
        return $this->response->setStatusCode(500)->setJSON([
            'status' => 'error', 
            'message' => $e->getMessage()
        ]);
    }
}
}
