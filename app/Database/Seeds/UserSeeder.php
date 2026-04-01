<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    protected $DBGroup = 'master';

    public function run()
    {
        $data = [
            [
                'full_name' => 'Super Admin',
                'is_super_admin' => 1,
                'password' => password_hash('admin1234', PASSWORD_DEFAULT),
                'username' => 'admin',
                'phone_number' => '08123456789',
                'created_by' => 'seeder',
                'modified_by' => 'seeder',
                'email' => 'email@example.com',
            ],
        ];
        $this->db->table('m_user')->insertBatch($data);
    }
}
