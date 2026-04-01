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
        if ($id != null) {
            $queryRes = $this->internshipModel->where('id', $id)->findAll();

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
            $queryRes = $this->internshipModel->findAll();

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
            'internships' => $internships
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
            'is_active' => isset($data['is_active']) ? "1" : "0",
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
        $id = $this->sessionModel->where('id', $token)->first()->getUser()->id;

        $data = $this->request->getJSON(true);
        $internship = [
            'name' => $data['name'],
            'department' => $data['department'],
            'head_department' => $data['head_department'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_active' => isset($data['is_active']) ? "1" : "0",
            'modified_by' => $id,
        ];

        if ($this->internshipModel->find($id)) {
            if ($this->internshipModel->update($id, $internship)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Internship Edited.'
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
