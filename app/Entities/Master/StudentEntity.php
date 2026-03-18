<?php

namespace App\Entities\Master;

use CodeIgniter\Entity\Entity;

class StudentEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'last_access'];
    protected $casts   = [];
}
