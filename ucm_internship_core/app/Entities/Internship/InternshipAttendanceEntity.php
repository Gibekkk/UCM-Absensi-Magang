<?php

namespace App\Entities\Internship;

use CodeIgniter\Entity\Entity;
use App\Models\Internship\InternshipStudentModel;

class InternshipAttendanceEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'scan_time'];
    protected $casts   = [];
    protected $internshipStudentModel;
    public function __construct()
    {
        $this->internshipStudentModel = new InternshipStudentModel();
    }

    public function getInternshipStudent()
    {
        return $this->internshipStudentModel
            ->where('id', $this->attributes['internship_student_id'])
            ->get()
            ->getFirstRow(InternshipStudentEntity::class);
    }
}
