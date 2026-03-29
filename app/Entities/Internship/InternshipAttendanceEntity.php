<?php

namespace App\Entities\Internship;

use CodeIgniter\Entity\Entity;
use Config\Database;

class InternshipAttendanceEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'scan_time'];
    protected $casts   = [];

    public function getInternshipStudent()
    {
        $db = Database::connect('default');

        return $db->table('m_internship_student')
            ->where('id', $this->attributes['internship_student_id'])
            ->get()
            ->getFirstRow(InternshipStudentEntity::class);
    }
}
