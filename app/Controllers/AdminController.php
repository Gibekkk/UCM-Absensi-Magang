<?php

namespace App\Controllers;

use App\Models\Master\StudentModel;

class AdminController extends BaseController
{
    public function index()
    {
        return view('Admin/dashboard');
    }

    public function getStudents()
    {
        $studentModel = new StudentModel();
        $students = $studentModel->findAll();

        return $this->response->setJSON([
            'students' => $students
        ]);
    }
}
