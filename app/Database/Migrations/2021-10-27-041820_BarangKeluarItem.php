<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BarangKeluarItem extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'bki_id' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'bki_barangkeluar' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'bki_barang' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'bki_qty' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
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
        $this->forge->addPrimaryKey('bki_id', true);
        $this->forge->createTable('barangkeluar_item');
    }

    public function down()
    {
        $this->forge->dropTable('barangkeluar_item');
    }
}
