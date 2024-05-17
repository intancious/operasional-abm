<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tukang extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tk_id' => [
                'type'                => 'BIGINT',
                'constraint'         => 20,
                'unsigned'           => true,
                'auto_increment'     => true,
            ],
            'tk_nama' => [
                'type'               => 'VARCHAR',
                'constraint'         => '255'
            ],
            'tk_alamat' => [
                'type'               => 'TEXT',
                'null'                 => true,
            ],
            'created_at' => [
                'type'                 => 'DATETIME',
                'null'                 => true,
            ],
            'updated_at' => [
                'type'                 => 'DATETIME',
                'null'                 => true,
            ]
        ]);
        $this->forge->addPrimaryKey('tk_id', true);
        $this->forge->createTable('tukang');
    }

    public function down()
    {
        $this->forge->dropTable('tukang');
    }
}
