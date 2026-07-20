<?php

namespace App\Models\Internship;

use CodeIgniter\Model;
use App\Entities\Internship\InternshipStudentEntity;
use App\Models\Master\StudentModel;
use Ramsey\Uuid\Uuid;

class InternshipStudentModel extends Model
{
    protected $table            = 'ictadmin_dbwp_ucm_internship.m_internship_student';
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
    protected $beforeInsert   = ['syncStatus', 'autoFillDates', 'generateId'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['syncStatus'];
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

    protected function autoFillDates(array $data)
    {
        $internshipModel = new InternshipModel();
        $internshipId = $data['data']['internship_id'] ?? null;

        if ($internshipId && (empty($data['data']['start_date']) || empty($data['data']['end_date']))) {
            $internship = $internshipModel->where('id', $internshipId)->get()->getRow();

            if ($internship) {
                if (empty($data['data']['start_date'])) $data['data']['start_date'] = $internship->start_date;
                if (empty($data['data']['end_date']))   $data['data']['end_date']   = $internship->end_date;
            }
        }
        return $data;
    }

    protected function syncStatus(array $data)
    {
        // Cek apakah 'end_date' ada dalam data yang akan disimpan
        if (isset($data['data']['end_date'])) {
            $today = date('Y-m-d');

            // Jika end_date sudah lewat, paksa is_active jadi '0'
            if ($data['data']['end_date'] < $today) {
                $data['data']['is_active'] = '0';
            }
        }
        return $data;
    }

    protected function syncStatusAfterFind(array $data)
    {
        if (!isset($data['data'])) return $data;

        $idsToUpdate = [];
        $today = date('Y-m-d');

        $process = function ($row) use (&$idsToUpdate, $today) {
            if (is_object($row) && isset($row->is_active) && $row->is_active == '1') {
                $shouldDeactivate = false;

                // Kondisi 1: end_date sudah lewat (logika lama)
                if (isset($row->end_date) && $row->end_date < $today) {
                    $shouldDeactivate = true;
                }

                // Kondisi 2: internship induk tidak aktif (logika baru)
                if (!$shouldDeactivate && isset($row->internship_id)) {
                    $internshipInactive = \Config\Database::connect()
                        ->table('ictadmin_dbwp_ucm_internship.m_internship')
                        ->where('id', $row->internship_id)
                        ->where('is_active', 0)
                        ->countAllResults();

                    if ($internshipInactive > 0) {
                        $shouldDeactivate = true;
                    }
                }

                if ($shouldDeactivate) {
                    $row->is_active = '0';
                    $idsToUpdate[] = $row->id;
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

        // Bypass model callbacks
        if (!empty($idsToUpdate)) {
            \Config\Database::connect()
                ->table($this->table)
                ->whereIn('id', $idsToUpdate)
                ->update(['is_active' => 0]);
        }

        return $data;
    }
}
