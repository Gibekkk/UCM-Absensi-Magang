<?php

namespace App\Entities\Master;

use CodeIgniter\Entity\Entity;

class UserEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date'];
    protected $casts   = [];
}
