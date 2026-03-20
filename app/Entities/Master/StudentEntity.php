<?php

namespace App\Entities\Master;

use CodeIgniter\Entity\Entity;
use Config\Database;
use App\Entities\Internship\InternshipStudentEntity;

class StudentEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'last_access'];
    protected $casts   = [];

    public function getInternshipStudent()
    {
        $db = Database::connect('default');

        return $db->table('m_internship_student')
            ->where('student_id', $this->attributes['id'])
            ->get()
            ->getFirstRow(InternshipStudentEntity::class);
    }
}
