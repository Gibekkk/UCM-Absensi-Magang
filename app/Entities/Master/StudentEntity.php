<?php

namespace App\Entities\Master;

use CodeIgniter\Entity\Entity;
use App\Entities\Internship\InternshipStudentEntity;
use App\Models\Internship\InternshipStudentModel;

class StudentEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'last_access'];
    protected $casts   = [];
    protected $internshipStudentModel;
    public function __construct()
    {
        $this->internshipStudentModel = new InternshipStudentModel();
    }

    public function getInternshipStudent()
    {
        return $this->internshipStudentModel
            ->where('student_id', $this->attributes['id'])
            ->get()
            ->getFirstRow(InternshipStudentEntity::class);
    }
}
