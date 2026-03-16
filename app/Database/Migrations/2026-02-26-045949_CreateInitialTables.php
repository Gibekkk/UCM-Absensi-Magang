<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateInitialTables extends Migration
{
    public function up()
    {
        // --- 1. Tabel Users ---
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 36],
            'username' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        // --- 2. Tabel Admins ---
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 36],
            'user_id' => ['type' => 'VARCHAR', 'constraint' => 36],
            'nama_admin' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('admins');

        // --- 3. Tabel Mahasiswa ---
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 36],
            'nim' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100],
            'jurusan' => ['type' => 'VARCHAR', 'constraint' => 100],
            'spesialisasi' => ['type' => 'VARCHAR', 'constraint' => 100],
            'angkatan' => [
                'type' => 'int',
                'constraint' => 4,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('mahasiswa');

        // --- 4. Tabel Sessions (Manual Token) ---
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 36], // Token UUID
            'user_id' => ['type' => 'VARCHAR', 'constraint' => 36],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'expired_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sessions');
    }

    public function down()
    {
        $this->forge->dropTable('sessions');
        $this->forge->dropTable('mahasiswa');
        $this->forge->dropTable('admins');
        $this->forge->dropTable('users');
    }
}