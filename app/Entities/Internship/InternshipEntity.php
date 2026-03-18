<?php

namespace App\Entities\Internship;

use CodeIgniter\Entity\Entity;

class InternshipEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'start_date', 'end_date'];
    protected $casts   = [];
}
