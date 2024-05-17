<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KasKecil extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'kk_id' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'auto_increment'    => true,
            ],
            'kk_jenis' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true
            ],
            'kk_nomor' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'kk_tanggal' => [
                'type'              => 'DATETIME'
            ],
            'kk_uraian' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255',
                'null'              => true,
            ],
            'kk_nominal' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'kk_kembali' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255',
                'null'              => true
            ],
            'kk_debet' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'kk_kredit' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'kk_user' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
            ],
            'kk_approval' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
            ],
            'kk_status' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true
            ],
            'kk_kembaliDebet' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'kk_kembaliKredit' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
            'updated_at' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ]
        ]);
        $this->forge->addPrimaryKey('kk_id', true);
        $this->forge->createTable('kas_kecil');
    }

    public function down()
    {
        $this->forge->dropTable('kas_kecil');
    }
}
