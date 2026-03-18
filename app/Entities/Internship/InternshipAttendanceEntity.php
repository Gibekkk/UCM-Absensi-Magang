<?php

namespace App\Entities\Internship;

use CodeIgniter\Entity\Entity;

class InternshipAttendanceEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'scan_time'];
    protected $casts   = [];
}
