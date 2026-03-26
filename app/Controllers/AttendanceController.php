<?php

namespace App\Controllers;

use App\Models\Internship\InternshipModel;
use App\Models\Master\SessionModel;
use App\Models\Master\StudentModel;

class AttendanceController extends BaseController
{
    protected $internshipModel;
    protected $sessionModel;
    protected $studentModel;

    public function __construct()
    {
        $this->internshipModel = new InternshipModel();
        $this->sessionModel = new SessionModel();
        $this->studentModel = new StudentModel();
    }
    
    public function scanner()
    {
        return view('Attendance/scanner');
    }
}
