<?php

namespace App\Models\Master;

use CodeIgniter\Model;
use App\Entities\Master\SessionEntity;
use Ramsey\Uuid\Uuid;

class SessionModel extends Model
{
    protected $table            = 'ictadmin_dbwp_ucm_master.m_session';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = SessionEntity::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'last_access'
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
    protected $beforeInsert   = ['generateId'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
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
}
