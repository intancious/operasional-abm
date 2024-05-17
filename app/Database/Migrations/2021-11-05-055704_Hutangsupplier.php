<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Hutangsupplier extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'hs_id' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'hs_tanggal' => [
                'type'              => 'DATETIME'
            ],
            'hs_nomor' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'hs_pembelian' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'hs_suplier' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'hs_total' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'hs_tempo' => [
                'type'              => 'DATETIME',
                'null'              => true,
            ],
            'hs_debet' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true,
            ],
            'hs_kredit' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'hs_user' => [
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
        $this->forge->addPrimaryKey('hs_id', true);
        $this->forge->createTable('hutangsuplier');
    }

    public function down()
    {
        $this->forge->dropTable('hutangsuplier');
    }
}
