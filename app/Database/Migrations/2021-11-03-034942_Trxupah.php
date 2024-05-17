<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Trxupah extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tu_id' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'auto_increment'    => true,
            ],
            'tu_nomor' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'tu_tanggal' => [
                'type'              => 'DATETIME'
            ],
            'tu_jumlah' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'tu_nilai' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'tu_total' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'tu_tukang' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'tu_upah' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'tu_unit' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'tu_keterangan' => [
                'type'              => 'TEXT',
                'null'              => true
            ],
            'tu_debet' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'tu_kredit' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'tu_user' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
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
        $this->forge->addPrimaryKey('tu_id', true);
        $this->forge->createTable('trxupah');
    }

    public function down()
    {
        $this->forge->dropTable('trxupah');
    }
}
