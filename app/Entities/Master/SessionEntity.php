<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SessionEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'last_access'];
    protected $casts   = [];
}
