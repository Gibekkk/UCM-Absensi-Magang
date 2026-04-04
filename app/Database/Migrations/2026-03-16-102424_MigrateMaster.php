<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class MigrateMaster extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null'    => false,
                'default' => new RawSql('(UUID())'),
            ],
            'is_super_admin' => [
                'type' => 'CHAR',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'unique' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addKey('id', true);
        $this->forge->createTable('m_user');

        $this->forge->addField([
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
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('created_by', 'm_user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('modified_by', 'm_user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('m_student');

        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null'    => false,
                'default' => new RawSql('(UUID())'),
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'last_access' => [
                'type' => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'created_date' => [
                'type' => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'modified_date' => [
                'type' => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'm_user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('m_session');
    }

    public function down()
    {
        $this->forge->dropTable('m_user');
        $this->forge->dropTable('m_student');
        $this->forge->dropTable('m_session');
    }
}
