<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use Config\Database;

class MigrateMagang extends Migration
{
    public function up()
    {
        $forge = Database::forge('default');

        $forge->addField([
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
        $forge->addKey('id', true);
        $forge->createTable('m_internship');

        $forge->addField([
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
        $forge->addKey('id', true);
        $forge->addForeignKey('internship_id', 'm_internship', 'id', 'CASCADE', 'CASCADE');
        $forge->createTable('m_internship_student');

        $forge->addField([
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
        $forge->addKey('id', true);
        $forge->addForeignKey('internship_student_id', 'm_internship_student', 'id', 'CASCADE', 'CASCADE');
        $forge->createTable('t_internship_attendance');
    }

    public function down()
    {
        $forge = Database::forge('default');
        $forge->dropTable('m_internship');
        $forge->dropTable('m_internship_student');
        $forge->dropTable('t_internship_attendance');
    }
}
