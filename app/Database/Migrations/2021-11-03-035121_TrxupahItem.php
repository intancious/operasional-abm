<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TrxupahItem extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ui_id' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'ui_tanggal' => [
                'type'              => 'DATETIME'
            ],
            'ui_nomor' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'ui_trxupah' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'ui_bayar' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'ui_debet' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'ui_kredit' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'null'              => true
            ],
            'ui_keterangan' => [
                'type'              => 'TEXT',
                'null'              => true
            ],
            'ui_user' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'created_at' => [
                'type'              => 'DATETIME',
                'null'              => true
            ],
            'updated_at' => [
                'type'              => 'DATETIME',
                'null'              => true
            ]
        ]);
        $this->forge->addPrimaryKey('ui_id', true);
        $this->forge->createTable('trxupah_item');
    }

    public function down()
    {
        $this->forge->dropTable('trxupah_item');
    }
}
