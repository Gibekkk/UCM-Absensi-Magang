<?php

namespace App\Models\Master;

use CodeIgniter\Model;
use App\Entities\Master\UserEntity;
use Ramsey\Uuid\Uuid;

class UserModel extends Model
{
    protected $table            = 'db_mstr.m_user';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = UserEntity::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username',
        'password',
        'full_name',
        'email',
        'phone_number',
        'is_active',
        'created_by',
        'modified_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateId', 'hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function generateId(array $data)
    {
        // Jika ID belum ada, buatkan manual (opsional jika database sudah punya default)
        if (!isset($data['data']['id'])) {
            $data['data']['id'] = Uuid::uuid7()->toString();
        }
        return $data;
    }

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            // Lakukan hashing password menggunakan algoritma default (BCRYPT)
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}
