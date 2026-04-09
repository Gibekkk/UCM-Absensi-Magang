<?php

namespace App\Entities\Internship;

use CodeIgniter\Entity\Entity;
use App\Entities\Internship\InternshipEntity;
use App\Entities\Internship\InternshipAttendanceEntity;
use App\Entities\Master\StudentEntity;
use App\Models\Internship\InternshipModel;
use App\Models\Internship\InternshipAttendanceModel;
use App\Models\Master\StudentModel;

class InternshipStudentEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'start_date', 'end_date'];
    protected $casts   = [];
    protected $internshipModel;
    protected $internshipAttendanceModel;
    protected $studentModel;
    public function __construct()
    {
        $this->internshipModel = new InternshipModel();
        $this->internshipAttendanceModel = new InternshipAttendanceModel();
        $this->studentModel = new StudentModel();
    }

    public function getInternship()
    {
        return $this->internshipModel
            ->where('id', $this->attributes['internship_id'])
            ->get()
            ->getFirstRow(InternshipEntity::class);
    }

    public function getStudent()
    {
        return $this->studentModel
            ->where('id', $this->attributes['student_id'])
            ->get()
            ->getFirstRow(StudentEntity::class);
    }

    public function getAttendances()
    {
        return $this->internshipAttendanceModel
            ->where('internship_student_id', $this->attributes['id'])
            ->get()
            ->getResult(InternshipAttendanceEntity::class);
    }

    public function getLastAttendance()
    {
        $attendances = $this->internshipAttendanceModel
            ->where('internship_student_id', $this->attributes['id'])
            ->orderBy('created_date', 'DESC')
            ->get()
            ->getResult(InternshipAttendanceEntity::class);

        if (count($attendances) > 0)
            return $attendances[0];
        return null;
    }
}
