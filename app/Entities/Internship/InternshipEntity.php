<?php

namespace App\Entities\Internship;

use CodeIgniter\Entity\Entity;
use Config\Database;
use App\Entities\Internship\InternshipStudentEntity;

class InternshipEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'start_date', 'end_date'];
    protected $casts   = [];

    public function getStudentInternships()
    {
        $db = Database::connect('default');

        return $db->table('m_internship_student')
            ->where('internship_id', $this->attributes['id'])
            ->get()
            ->getResult(InternshipStudentEntity::class);
    }
}
