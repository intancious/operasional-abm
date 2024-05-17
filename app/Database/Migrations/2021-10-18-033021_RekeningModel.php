<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RekeningModel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'rek_id' => [
                'type'                => 'BIGINT',
                'constraint'         => 20,
                'unsigned'           => true,
                'auto_increment'     => true,
            ],
            'reksub1_id' => [
                'type'                => 'VARCHAR',
                'constraint'         => '255',
                'null'                 => true
            ],
            'reksub2_id' => [
                'type'                => 'VARCHAR',
                'constraint'         => '255',
                'null'                 => true
            ],
            'reksub3_id' => [
                'type'                => 'VARCHAR',
                'constraint'         => '255',
                'null'                 => true
            ],
            'reksub4_id' => [
                'type'                => 'VARCHAR',
                'constraint'         => '255',
                'null'                 => true
            ],
            'reksub5_id' => [
                'type'                => 'VARCHAR',
                'constraint'         => '255',
                'null'                 => true
            ],
            'rek_kode' => [
                'type'                => 'VARCHAR',
                'constraint'         => '255'
            ],
            'rek_nama' => [
                'type'                => 'VARCHAR',
                'constraint'         => '255'
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
        $this->forge->addPrimaryKey('rek_id', true);
        $this->forge->createTable('rekening');
    }

    public function down()
    {
        $this->forge->dropTable('rekening');
    }
}
