<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class InternshipStudentEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'start_date', 'end_date'];
    protected $casts   = [];
}
