<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Models\AdminModel;
use App\Models\UserModel;

class Session extends Entity
{
    protected $datamap = [];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [];

    public function getAdmin()
    {
        $adminModel = new AdminModel();
        return $adminModel->where('user_id', $this->attributes['user_id'])->first();
    }

    public function getUser()
    {
        $userModel = new UserModel();
        return $userModel->find($this->attributes['user_id']);
    }
}
