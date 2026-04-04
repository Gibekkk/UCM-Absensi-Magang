<?php

namespace App\Entities\Internship;

use CodeIgniter\Entity\Entity;
use App\Entities\Internship\InternshipStudentEntity;
use App\Models\Internship\InternshipStudentModel;

class InternshipEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'start_date', 'end_date'];
    protected $casts   = [];
    protected $internshipStudentModel;
    public function __construct()
    {
        $this->internshipStudentModel = new InternshipStudentModel();
    }

    public function getStudentInternships()
    {
        return $this->internshipStudentModel
            ->where('internship_id', $this->attributes['id'])
            ->get()
            ->getResult(InternshipStudentEntity::class);
    }
}
