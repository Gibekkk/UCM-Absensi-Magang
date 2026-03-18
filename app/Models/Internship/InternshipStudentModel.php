<?php

namespace App\Models\Internship;

use CodeIgniter\Model;
use Config\Database;
use App\Entities\Internship\InternshipStudentEntity;
use Ramsey\Uuid\Uuid;

class InternshipStudentModel extends Model
{
    protected $table            = 'm_internship_student';
    protected $DBGroup          = 'default';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = InternshipStudentEntity::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'internship_id',
        'student_id',
        'start_date',
        'end_date',
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
    protected $beforeInsert   = ['autoFillDates', 'updateActiveStatus', 'generateId'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['updateActiveStatus'];
    protected $afterUpdate    = [];
    protected $beforeFind     = ['updateActiveStatus'];
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

    protected function autoFillDates(array $data)
    {
        $internshipId = $data['data']['internship_id'] ?? null;

        if ($internshipId && (empty($data['data']['start_date']) || empty($data['data']['end_date']))) {
            $db = Database::connect($this->DBGroup);
            $internship = $db->table('m_internship')->where('id', $internshipId)->get()->getRow();

            if ($internship) {
                if (empty($data['data']['start_date'])) $data['data']['start_date'] = $internship->start_date;
                if (empty($data['data']['end_date']))   $data['data']['end_date']   = $internship->end_date;
            }
        }
        return $data;
    }

    protected function updateActiveStatus(array $data)
    {
        if (isset($data['data']['end_date'])) {
            $data['data'] = $this->updateStatusIfExpired($data['data']);
        } elseif (isset($data['data'][0])) {
            foreach ($data['data'] as $key => $row) {
                $data['data'][$key] = $this->updateStatusIfExpired($row);
            }
        }
        return $data;
    }

    private function updateStatusIfExpired($row)
    {
        $row = (array) $row;
        $today = date('Y-m-d');

        if ($row['end_date'] < $today && $row['is_active'] == '1') {
            $row['is_active'] = '0';
            $this->db->table($this->table)->update(['is_active' => '0'], ['id' => $row['id']]);
        }
        return $row;
    }
}
