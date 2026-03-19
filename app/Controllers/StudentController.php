<?php

namespace App\Controllers;

use App\Models\Master\StudentModel;
use App\Models\Master\SessionModel;

class StudentController extends BaseController
{
    protected $studentModel;
    protected $sessionModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->sessionModel = new SessionModel();
    }

    public function getStudents($id = null)
    {
        if ($id != null) {
            $students = $this->studentModel->find($id);
        } else {
            $students = $this->studentModel->findAll();
        }

        return $this->response->setJSON([
            'students' => $students
        ]);
    }

    public function addStudent()
    {
        $token = $this->request->getHeaderLine('token');
        $username = $this->sessionModel->where('id', $token)->first()->getUser()->username;

        $data = $this->request->getJSON(true);
        $student = [
            'nim' => $data['nim'],
            'full_name' => $data['full_name'],
            'major' => $data['major'],
            'sub_major' => $data['sub_major'],
            'is_active' => isset($data['is_active']) ? "1" : "0",
            'created_by' => $username,
            'modified_by' => $username,
        ];

        if ($this->studentModel->insert($student)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Student Added.'
            ]);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }

    public function editStudent($id)
    {
        $token = $this->request->getHeaderLine('token');
        $username = $this->sessionModel->where('id', $token)->first()->getUser()->username;

        $data = $this->request->getJSON(true);
        $student = [
            'nim' => $data['nim'],
            'full_name' => $data['full_name'],
            'major' => $data['major'],
            'sub_major' => $data['sub_major'],
            'is_active' => isset($data['is_active']) ? "1" : "0",
            'created_by' => $username,
            'modified_by' => $username,
        ];

        if ($this->studentModel->find($id)) {
            if ($this->studentModel->update($id, $student)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Student Edited.'
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

    public function deleteStudent($id)
    {
        $token = $this->request->getHeaderLine('token');;
        $username = $this->sessionModel->where('id', $token)->first()->getUser()->username;


        if ($this->studentModel->find($id)) {
            if ($this->studentModel->delete($id)) {
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
}
