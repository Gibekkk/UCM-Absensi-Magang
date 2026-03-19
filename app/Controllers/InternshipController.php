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
            $internships = $this->internshipModel->find($id);
        } else {
            $internships = $this->internshipModel->findAll();
        }

        return $this->response->setJSON([
            'internships' => $internships
        ]);
    }

    public function addInternship()
    {
        $token = $this->request->getHeaderLine('token');
        $username = $this->sessionModel->where('id', $token)->first()->getUser()->username;

        $data = $this->request->getJSON(true);
        $internship = [
            'name' => $data['name'],
            'department' => $data['department'],
            'head_department' => $data['head_department'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_active' => isset($data['is_active']) ? "1" : "0",
            'created_by' => $username,
            'modified_by' => $username,
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
        $username = $this->sessionModel->where('id', $token)->first()->getUser()->username;

        $data = $this->request->getJSON(true);
        $internship = [
            'name' => $data['name'],
            'department' => $data['department'],
            'head_department' => $data['head_department'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_active' => isset($data['is_active']) ? "1" : "0",
            'created_by' => $username,
            'modified_by' => $username,
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
        $token = $this->request->getHeaderLine('token');;
        $username = $this->sessionModel->where('id', $token)->first()->getUser()->username;


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
