<?php

namespace App\Entities\Internship;

use CodeIgniter\Entity\Entity;
use Config\Database;
use App\Entities\Internship\InternshipEntity;
use App\Entities\Master\StudentEntity;

class InternshipStudentEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'start_date', 'end_date'];
    protected $casts   = [];

    public function getInternship()
    {
        $db = Database::connect('default');

        return $db->table('m_internship')
                  ->where('id', $this->attributes['internship_id'])
                  ->get()
                  ->getFirstRow(InternshipEntity::class);
    }

    public function getStudent()
    {
        $db = Database::connect('master');

        return $db->table('m_student')
                  ->where('id', $this->attributes['student_id'])
                  ->get()
                  ->getFirstRow(StudentEntity::class);
    }
}
