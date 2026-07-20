<?php

namespace App\Models\Master;

use CodeIgniter\Model;
use App\Entities\Master\StudentEntity;
use Ramsey\Uuid\Uuid;

class StudentModel extends Model
{
    protected $table            = 'ictadmin_dbwp_ucm_master.m_student';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = StudentEntity::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nim',
        'full_name',
        'major',
        'sub_major',
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
    protected $beforeInsert   = ['generateId'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ['syncStatusAfterFind'];
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

    protected function syncStatusAfterFind(array $data)
    {
        if (!isset($data['data'])) return $data;

        $idsToDeactivate = [];

        $process = function ($row) use (&$idsToDeactivate) {
            if (is_object($row) && isset($row->is_active) && $row->is_active == '1') {
                $db = \Config\Database::connect();

                $internship = $db
                    ->table('ictadmin_dbwp_ucm_internship.m_internship_student ms')
                    ->join(
                        'ictadmin_dbwp_ucm_internship.m_internship m',
                        'm.id = ms.internship_id'
                    )
                    ->where('ms.student_id', $row->id)
                    ->where('m.is_active', 0)
                    ->countAllResults();

                if ($internship > 0) {
                    $row->is_active = '0';
                    $idsToDeactivate[] = $row->id;
                }
            }
            return $row;
        };

        if (!is_array($data['data'])) {
            $data['data'] = $process($data['data']);
        } else {
            foreach ($data['data'] as $key => $row) {
                $data['data'][$key] = $process($row);
            }
        }

        if (!empty($idsToDeactivate)) {
            \Config\Database::connect()
                ->table($this->table)
                ->whereIn('id', $idsToDeactivate)
                ->update(['is_active' => 0]);
        }

        return $data;
    }
}
