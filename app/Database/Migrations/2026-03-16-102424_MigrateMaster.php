<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class MigrateMaster extends Migration
{
    public function up()
    {
        $forge = Database::forge('master');

        $forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null'    => false,
                'default' => new RawSql('(UUID())'),
            ],
            'cos_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'default' => '2b0e737b-2d3c-11ea-9dc8-000d3aa02732',
                'null'    => false,
            ],
            'nim' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
                'null'    => false,
            ],
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null'    => false,
            ],
            'major' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null'    => false,
            ],
            'sub_major' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null'    => false,
            ],
            'is_active' => [
                'type' => 'CHAR',
                'constraint' => 1,
                'null'    => false,
                'default' => 1,
            ],
            'created_date' => [
                'type' => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => '36',
                'null'    => false,
            ],
            'modified_date' => [
                'type' => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
            'modified_by' => [
                'type' => 'VARCHAR',
                'constraint' => '36',
                'null'    => false,
            ],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('m_student');

        $forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null'    => false,
                'default' => new RawSql('(UUID())'),
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'is_active' => [
                'type' => 'CHAR',
                'constraint' => 1,
                'null'    => false,
                'default' => 1,
            ],
            'created_date' => [
                'type' => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => '36',
                'null'    => false,
            ],
            'modified_date' => [
                'type' => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
            'modified_by' => [
                'type' => 'VARCHAR',
                'constraint' => '36',
                'null'    => false,
            ],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('m_user');
    }

    public function down()
    {
        $forge = Database::forge('master');
        $forge->dropTable('m_user');
        $forge->dropTable('m_student');
    }
}
