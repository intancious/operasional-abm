<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BarangKeluar extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'bk_id' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'bk_tanggal' => [
                'type'              => 'DATETIME'
            ],
            'bk_nomor' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'bk_keterangan' => [
                'type'              => 'TEXT',
                'null'              => true
            ],
            'bk_unit' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'bk_debet' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'bk_kredit' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'bk_user' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
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
        $this->forge->addPrimaryKey('bk_id', true);
        $this->forge->createTable('barangkeluar');
    }

    public function down()
    {
        $this->forge->dropTable('barangkeluar');
    }
}
