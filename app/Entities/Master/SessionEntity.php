<?php

namespace App\Entities\Master;

use CodeIgniter\Entity\Entity;
use App\Models\Master\UserModel;

class SessionEntity extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_date', 'modified_date', 'last_access'];
    protected $casts   = [];
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function getUser()
    {
        return $this->userModel->find($this->attributes['user_id']);
    }
}
