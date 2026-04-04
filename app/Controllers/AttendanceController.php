<?php

namespace App\Controllers;

use App\Models\Internship\InternshipAttendanceModel;
use App\Models\Internship\InternshipModel;
use App\Models\Master\StudentModel;
use App\Models\Master\SessionModel;
use DateTime;

class AttendanceController extends BaseController
{
    protected $internshipModel;
    protected $internshipAttendanceModel;
    protected $sessionModel;
    protected $studentModel;

    public function __construct()
    {
        $this->internshipModel = new InternshipModel();
        $this->sessionModel = new SessionModel();
        $this->internshipAttendanceModel = new InternshipAttendanceModel();
        $this->studentModel = new StudentModel();
    }

    public function index()
    {
        return view('Attendance/scanner');
    }

    public function scanner()
    {
        return view('Attendance/scanner');
    }

    public function camera()
    {
        return view('Attendance/camera');
    }

    public function createAttendance()
    {
        $data = $this->request->getJSON(true);
        $rawInput = $data['input'];
        $nim = explode('+', $rawInput)[0];
        $student = $this->studentModel->where("NIM", $nim)->where('is_active', 1)->first();
        if ($student) {
            $internshipStudent = $student->getInternshipStudent();

            $lastAttendance = $internshipStudent->getLastAttendance();
            if ($lastAttendance != null) {
                $lastScanTime = new DateTime($lastAttendance->created_date);
                $lastScanTime->modify("+30 seconds");
                if ($lastScanTime > new DateTime())
                    return  $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'You Have Been Absent.'
                    ])->setStatusCode(400);
            }

            $attendanceData = [
                'internship_student_id' => $internshipStudent->id,
                'scan_time' => date("Y-m-d"),
                'created_by' => $internshipStudent->id,
                'modified_by' => $internshipStudent->id,
            ];
            if ($this->internshipAttendanceModel->insert($attendanceData)) {
                $todayAttendance = $this->internshipAttendanceModel
                    ->where('internship_student_id', $internshipStudent->id)
                    ->where('scan_time', date('Y-m-d'))
                    ->findAll();

                $attendance = end($todayAttendance);

                return $this->response->setJSON([
                    'name' => $student->full_name,
                    'nim' => $student->nim,
                    'status' => $attendance->scan_time_type,
                    'created' => $attendance->created_date,
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Attendance Insertion Failed.'
                ])->setStatusCode(500);
            }
        }
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Student Not Found.'
        ])->setStatusCode(404);
    }

    public function getAttendances($id = null)
    {
        $attendances = [];
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();
        $queryRes = $this->internshipAttendanceModel->where('scan_time', date('Y-m-d'))->findAll();
        if ($id != null) {
            $queryRes = $this->internshipAttendanceModel->where('id', $id)->findAll();

            foreach ($queryRes as $attendance) {
                $internshipStudent = $attendance->getInternshipStudent();
                $student = $internshipStudent->getStudent();
                $internship = $internshipStudent->getInternship();
                if ($internship->created_by == $user->id || $user->is_super_admin == 1) {
                    $attendances = [
                        'id' => $attendance->id,
                        'nim' => $student->nim,
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'major' => $student->major,
                        'sub_major' => $student->sub_major,
                        'scan_time' => $attendance->scan_time,
                        'scan_time_type' => $attendance->scan_time_type,
                        'created_date' => $attendance->created_date,
                        'created_by' => $attendance->created_by,
                        'modified_date' => $attendance->modified_date,
                        'modified_by' => $attendance->modified_by,
                        'internship_name' => $internship->name,
                        'internship_id' => $internship->id,
                    ];
                }
            }
        } else {
            $queryRes = $this->internshipAttendanceModel->findAll();

            foreach ($queryRes as $attendance) {
                $internshipStudent = $attendance->getInternshipStudent();
                $student = $internshipStudent->getStudent();
                $internship = $internshipStudent->getInternship();
                if ($internship->created_by == $user->id || $user->is_super_admin == 1) {
                    $row = [
                        'id' => $attendance->id,
                        'nim' => $student->nim,
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'major' => $student->major,
                        'sub_major' => $student->sub_major,
                        'scan_time' => $attendance->scan_time,
                        'scan_time_type' => $attendance->scan_time_type,
                        'created_date' => $attendance->created_date,
                        'created_by' => $attendance->created_by,
                        'modified_date' => $attendance->modified_date,
                        'modified_by' => $attendance->modified_by,
                        'internship_name' => $internship->name,
                        'internship_id' => $internship->id,
                    ];

                    $attendances[] = $row;
                }
            }
        }

        return $this->response->setJSON([
            'attendances' => $attendances
        ]);
    }

    public function viewTodayAttendances()
    {
        $queryRes = $this->internshipAttendanceModel->where('scan_time', date('Y-m-d'))->findAll();
        $attendances = [];

        foreach ($queryRes as $attendance) {
            $internshipStudent = $attendance->getInternshipStudent();
            $student = $internshipStudent->getStudent();
            $internship = $internshipStudent->getInternship();
            $row = [
                'id' => $attendance->id,
                'nim' => $student->nim,
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'major' => $student->major,
                'sub_major' => $student->sub_major,
                'scan_time' => $attendance->scan_time,
                'scan_time_type' => $attendance->scan_time_type,
                'created_date' => $attendance->created_date,
                'created_by' => $attendance->created_by,
                'modified_date' => $attendance->modified_date,
                'modified_by' => $attendance->modified_by,
                'internship_name' => $internship->name,
                'internship_id' => $internship->id,
            ];

            $attendances[] = $row;
        }
        return $this->response->setJSON([
            'attendances' => $attendances
        ]);
    }

    public function getTodayAttendances()
    {
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();
        $queryRes = $this->internshipAttendanceModel->where('scan_time', date('Y-m-d'))->findAll();
        $attendances = [];

        foreach ($queryRes as $attendance) {
            $internshipStudent = $attendance->getInternshipStudent();
            $student = $internshipStudent->getStudent();
            $internship = $internshipStudent->getInternship();
            if ($internship->created_by == $user->id || $user->is_super_admin == 1) {
                $row = [
                    'id' => $attendance->id,
                    'nim' => $student->nim,
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'major' => $student->major,
                    'sub_major' => $student->sub_major,
                    'scan_time' => $attendance->scan_time,
                    'scan_time_type' => $attendance->scan_time_type,
                    'created_date' => $attendance->created_date,
                    'created_by' => $attendance->created_by,
                    'modified_date' => $attendance->modified_date,
                    'modified_by' => $attendance->modified_by,
                    'internship_name' => $internship->name,
                    'internship_id' => $internship->id,
                ];

                $attendances[] = $row;
            }
        }
        return $this->response->setJSON([
            'attendances' => $attendances
        ]);
    }

    public function getDateAttendances($day, $month, $year)
    {
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();
        $queryRes = $this->internshipAttendanceModel->where('scan_time', date('Y-m-d', strtotime("$day-$month-$year")))->findAll();
        $attendances = [];

        foreach ($queryRes as $attendance) {
            $internshipStudent = $attendance->getInternshipStudent();
            $student = $internshipStudent->getStudent();
            $internship = $internshipStudent->getInternship();
            if ($internship->created_by == $user->id || $user->is_super_admin == 1) {
                $row = [
                    'id' => $attendance->id,
                    'nim' => $student->nim,
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'major' => $student->major,
                    'sub_major' => $student->sub_major,
                    'scan_time' => $attendance->scan_time,
                    'scan_time_type' => $attendance->scan_time_type,
                    'created_date' => $attendance->created_date,
                    'created_by' => $attendance->created_by,
                    'modified_date' => $attendance->modified_date,
                    'modified_by' => $attendance->modified_by,
                    'internship_name' => $internship->name,
                    'internship_id' => $internship->id,
                ];

                $attendances[] = $row;
            }
        }
        return $this->response->setJSON([
            'attendances' => $attendances
        ]);
    }

    public function getDateRangeAttendances($year1, $month1, $day1, $year2, $month2, $day2)
    {
        $token = $this->request->getHeaderLine('token');
        $user = $this->sessionModel->where('id', $token)->first()->getUser();
        $queryRes = $this->internshipAttendanceModel->where('scan_time >=', date('Y-m-d', strtotime("$day1-$month1-$year1")))->where('scan_time <=', date('Y-m-d', strtotime("$day2-$month2-$year2")))->findAll();
        $attendances = [];

        foreach ($queryRes as $attendance) {
            $internshipStudent = $attendance->getInternshipStudent();
            $student = $internshipStudent->getStudent();
            $internship = $internshipStudent->getInternship();
            if ($internship->created_by == $user->id || $user->is_super_admin == 1) {
                $row = [
                    'id' => $attendance->id,
                    'nim' => $student->nim,
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'major' => $student->major,
                    'sub_major' => $student->sub_major,
                    'scan_time' => $attendance->scan_time,
                    'scan_time_type' => $attendance->scan_time_type,
                    'created_date' => $attendance->created_date,
                    'created_by' => $attendance->created_by,
                    'modified_date' => $attendance->modified_date,
                    'modified_by' => $attendance->modified_by,
                    'internship_name' => $internship->name,
                    'internship_id' => $internship->id,
                ];

                $attendances[] = $row;
            }
        }
        return $this->response->setJSON([
            'attendances' => $attendances
        ]);
    }
}
