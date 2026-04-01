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
            'is_active' => isset($data['is_active']) ? "1" : "0",
            'created_by' => $id,
            'modified_by' => $id,
        ];

        $internshipData = $this->internshipModel->find($data['internship_id']);
        if ($internshipData) {
            // Jika ini error, hal yang normal, kode ini bekerja dengan baik
            if ($internshipData->created_by == $id || $user->is_super_admin == 1) {
                if ($this->studentModel->insert($student)) {
                    $studentId = $this->studentModel->where('nim', $data['nim'])->first()->id;
                    $this->internshipStudentModel->insert([
                        "student_id" => $studentId,
                        "internship_id" => $data["internship_id"],
                        "is_active" => isset($data['is_active']) ? "1" : "0",
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
                    'message' => 'You Do Not Have Access.'
                ])->setStatusCode(500);
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
        $id = $user->id;

        $data = $this->request->getJSON(true);
        $student = [
            'nim' => $data['nim'],
            'full_name' => $data['full_name'],
            'major' => $data['major'],
            'sub_major' => $data['sub_major'],
            'is_active' => isset($data['is_active']) ? "1" : "0",
            'modified_by' => $id,
        ];

        $studentData = $this->studentModel->find($id);
        if ($studentData) {
            if ($user->is_super_admin || $studentData->created_by == $id) {
                if ($this->studentModel->update($id, $student)) {
                    // Jika ini error, hal yang normal, kode ini bekerja dengan baik
                    if ($studentData->getInternshipStudent()->internship_id != $data['internship_id']) {
                        $this->internshipStudentModel->update($studentData->getInternshipStudent()->id, [
                            "internship_id" => $data["internship_id"],
                            "is_active" => isset($data['is_active']) ? "1" : "0",
                            "modified_by" => $id,
                        ]);
                    }

                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Student Edited.'
                    ]);
                }
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

        // Validasi Session
        $session = $this->sessionModel->where('id', $token)->first();
        if (!$session) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $id = $session->getUser()->id;

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
                if ($index == 0) continue; // Skip header

                // Mapping: NIM(0), Full Name(1), Major(2), Sub Major(3), Internship Name(4), Is Active(5)
                $rawNim = (string)($row[0] ?? '');
                $nim = str_replace("'", "", $rawNim);
                $internshipName = $row[4] ?? null;

                $internship = $this->internshipModel->where('name', $internshipName)->first();

                if ($internship && $nim && !$this->studentModel->where('nim', $nim)->first()) {
                    $db = \Config\Database::connect();
                    $db->transStart();

                    $this->studentModel->insert([
                        'nim'         => $nim,
                        'full_name'   => $row[1],
                        'major'       => $row[2],
                        'sub_major'   => $row[3],
                        'is_active'   => $row[5] ?? '1',
                        'created_by'  => $id,
                        'modified_by' => $id,
                    ]);

                    $student = $this->studentModel->where('nim', $nim)->first();

                    $this->internshipStudentModel->insert([
                        'student_id'    => $student->id,
                        'internship_id' => $internship->id,
                        'is_active'     => $row[5] ?? '1',
                        'created_by'    => $id,
                        'modified_by'   => $id,
                    ]);

                    $db->transComplete();
                    if ($db->transStatus()) $successCount++;
                    else $errorCount++;
                } else {
                    $errorCount++;
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'data'   => ['success' => $successCount, 'failed' => $errorCount]
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
