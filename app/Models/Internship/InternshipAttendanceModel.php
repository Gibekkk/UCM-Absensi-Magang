<?php

namespace App\Models\Internship;

use CodeIgniter\Model;
use App\Entities\Internship\InternshipAttendanceEntity;
use Ramsey\Uuid\Uuid;

class InternshipAttendanceModel extends Model
{
    protected $table            = 't_internship_attendance';
    protected $DBGroup          = 'default';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = InternshipAttendanceEntity::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'internship_student_id',
        'scan_time',
        'scan_time_type',
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
    protected $beforeInsert   = ['determineAttendanceType', 'generateId'];
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

    protected function determineAttendanceType(array $data)
    {
        if (!isset($data['data']['scan_time'])) {
            $data['data']['scan_time'] = date('Y-m-d');
        }

        $studentId = $data['data']['internship_student_id'] ?? null;
        $today = date('Y-m-d');

        if ($studentId) {
            $existing = $this->where('internship_student_id', $studentId)
                ->where('scan_time', $today)
                ->where('scan_time_type', 'IN')
                ->first();

            $data['data']['scan_time_type'] = $existing ? 'OUT' : 'IN';
        }

        return $data;
    }
}
