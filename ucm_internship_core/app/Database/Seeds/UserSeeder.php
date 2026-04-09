<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
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
            [
                'full_name' => 'Admin1',
                'is_super_admin' => 0,
                'password' => password_hash('admin1234', PASSWORD_DEFAULT),
                'username' => 'admin1',
                'phone_number' => '08123456789',
                'created_by' => 'seeder',
                'modified_by' => 'seeder',
                'email' => 'email@example.com',
            ],
            [
                'full_name' => 'Admin2',
                'is_super_admin' => 0,
                'password' => password_hash('admin1234', PASSWORD_DEFAULT),
                'username' => 'admin2',
                'phone_number' => '08123456789',
                'created_by' => 'seeder',
                'modified_by' => 'seeder',
                'email' => 'email@example.com',
            ],
        ];
        $this->db->table('ictadmin_dbwp_ucm_master.m_user')->insertBatch($data);
    }
}
