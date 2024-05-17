<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HutangsupplierBayar extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'hb_id' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'hb_tanggal' => [
                'type'              => 'DATETIME'
            ],
            'hb_nomor' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'hb_hutangsuplier' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'hb_bayar' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'hb_keterangan' => [
                'type'              => 'TEXT',
                'null'              => true
            ],
            'hb_debet' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true,
            ],
            'hb_kredit' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'hb_isongkir' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true
            ],
            'hb_istunai' => [
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => true,
                'null'              => true
            ],
            'hb_user' => [
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
        $this->forge->addPrimaryKey('hb_id', true);
        $this->forge->createTable('hutangsuplier_bayar');
    }

    public function down()
    {
        $this->forge->dropTable('hutangsuplier_bayar');
    }
}
