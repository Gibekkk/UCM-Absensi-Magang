<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class MigrateMagang extends Migration
{
    protected $DBGroup = 'internship';
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null'    => false,
                'default' => new RawSql('(UUID())'),
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null'    => false,
            ],
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'head_department' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'end_date' => [
                'type' => 'DATE',
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
        $this->forge->addForeignKey('created_by', 'db_mstr.m_user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('modified_by', 'db_mstr.m_user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('m_internship');

        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null'    => false,
                'default' => new RawSql('(UUID())'),
            ],
            'internship_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'student_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'end_date' => [
                'type' => 'DATE',
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
        $this->forge->addForeignKey('internship_id', 'm_internship', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'db_mstr.m_user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('modified_by', 'db_mstr.m_user', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('m_internship_student');

        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null'    => false,
                'default' => new RawSql('(UUID())'),
            ],
            'internship_student_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'scan_time' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'scan_time_type' => [
                'type' => 'VARCHAR',
                'constraint' => 24,
                'default' => 'IN',
                'null' => false,
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
        $this->forge->addForeignKey('internship_student_id', 'm_internship_student', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'm_internship_student', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('modified_by', 'm_internship_student', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('t_internship_attendance');
    }

    public function down()
    {
        $this->forge->dropTable('m_internship');
        $this->forge->dropTable('m_internship_student');
        $this->forge->dropTable('t_internship_attendance');
    }
}
