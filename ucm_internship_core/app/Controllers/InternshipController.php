<?php

namespace App\Controllers;

use App\Models\Internship\InternshipModel;
use App\Models\Master\SessionModel;

class InternshipController extends BaseController
{
    protected $internshipModel;
    protected $sessionModel;

    public function __construct()
    {
        $this->internshipModel = new InternshipModel();
        $this->sessionModel = new SessionModel();
    }
    public function getInternships($id = null)
    {
        $internships = [];
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();
        if ($id != null) {
            if ($user->is_super_admin == 1) {
                $queryRes = $this->internshipModel->where('id', $id)->findAll();
            } else {
                $queryRes = $this->internshipModel->where('id', $id)->where('created_by', $user->id)->findAll();
            }

            if (count($queryRes) == 0)
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Internship Not Found.'
                ])->setStatusCode(404);

            foreach ($queryRes as $internship) {
                $students = $internship->getStudentInternships();

                $internships = [
                    'id' => $internship->id,
                    'name' => $internship->name,
                    'department' => $internship->department,
                    'head_department' => $internship->head_department,
                    'start_date' => $internship->start_date,
                    'end_date' => $internship->end_date,
                    'is_active' => $internship->is_active,
                    'created_by' => $internship->created_by,
                    'modified_by' => $internship->modified_by,
                    'students_count' => count($students),
                ];
            }
        } else {
            if ($user->is_super_admin == 1) {
                $queryRes = $this->internshipModel->findAll();
            } else {
                $queryRes = $this->internshipModel->where('created_by', $user->id)->findAll();
            }

            foreach ($queryRes as $internship) {
                $students = $internship->getStudentInternships();

                $row = [
                    'id' => $internship->id,
                    'name' => $internship->name,
                    'department' => $internship->department,
                    'head_department' => $internship->head_department,
                    'start_date' => $internship->start_date,
                    'end_date' => $internship->end_date,
                    'is_active' => $internship->is_active,
                    'created_by' => $internship->created_by,
                    'modified_by' => $internship->modified_by,
                    'students_count' => count($students),
                ];

                $internships[] = $row;
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'internships' => $internships
        ]);
    }

    public function findInternshipByDepartment($department)
    {
        $internships = [];
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();

        if ($user->is_super_admin == 1) {
            $queryRes = $this->internshipModel->where('department', $department)->findAll();
        } else {
            $queryRes = $this->internshipModel->where('department', $department)->where('created_by', $user->id)->findAll();
        }

        foreach ($queryRes as $internship) {
            $students = $internship->getStudentInternships();

            $row = [
                'id' => $internship->id,
                'name' => $internship->name,
                'department' => $internship->department,
                'head_department' => $internship->head_department,
                'start_date' => $internship->start_date,
                'end_date' => $internship->end_date,
                'is_active' => $internship->is_active,
                'created_by' => $internship->created_by,
                'modified_by' => $internship->modified_by,
                'students_count' => count($students),
            ];

            $internships[] = $row;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'internships' => $internships
        ]);
    }

    public function getDepartments()
    {
        $departments = [];
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();

        if ($user->is_super_admin == 1) {
            $queryRes = $this->internshipModel->select('department')->groupBy('department')->findAll();
        } else {
            $queryRes = $this->internshipModel->select('department')->groupBy('department')->where('created_by', $user->id)->findAll();
        }

        foreach ($queryRes as $rows) {

            $row = [
                'name' => $rows->department,
            ];

            $departments[] = $row;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'departments' => $departments
        ]);
    }

    public function addInternship()
    {
        $token = $this->request->getHeaderLine('token');
        $id = $this->sessionModel->where('id', $token)->first()->getUser()->id;

        $data = $this->request->getJSON(true);
        $internship = [
            'name' => $data['name'],
            'department' => $data['department'],
            'head_department' => $data['head_department'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_active' => "1",
            'created_by' => $id,
            'modified_by' => $id,
        ];

        if ($this->internshipModel->insert($internship)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Internship Added.'
            ]);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }

    public function editInternship($id)
    {
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();
        $userId = $user->id;

        $data = $this->request->getJSON(true);
        $internship = [
            'name' => $data['name'],
            'department' => $data['department'],
            'head_department' => $data['head_department'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'modified_by' => $userId,
        ];
        $internshipData = $this->internshipModel->find($id);
        if ($internshipData) {
            // Jika ini error, hal yang normal, kode ini bekerja dengan baik
            if ($user->is_super_admin || $internshipData->created_by == $userId) {
                if ($this->internshipModel->update($id, $internship)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Internship Edited.'
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'You Do Not Have Access.'
                ])->setStatusCode(402);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Internship Not Found.'
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

        $internship = [
            'is_active' => $isActive,
            'modified_by' => $userId,
        ];
        $internshipData = $this->internshipModel->find($id);
        if ($internshipData) {
            // Jika ini error, hal yang normal, kode ini bekerja dengan baik
            if ($user->is_super_admin || $internshipData->created_by == $userId) {
                if ($this->internshipModel->update($id, $internship)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Internship Edited.'
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'You Do Not Have Access.'
                ])->setStatusCode(402);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Internship Not Found.'
            ])->setStatusCode(404);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }

    public function deleteInternship($id)
    {
        $token = $this->request->getHeaderLine('token');

        if ($this->internshipModel->find($id)) {
            if ($this->internshipModel->delete($id)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Internship Deleted.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Internship Not Found.'
            ])->setStatusCode(404);
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Unknown Error Occured.'
        ])->setStatusCode(500);
    }
}
