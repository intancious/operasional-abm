<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PembelianItem extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pi_id' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'pi_pembelian' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'pi_barang' => [
                'type'              => 'BIGINT',
                'constraint'        => 20,
                'unsigned'          => true
            ],
            'pi_qtybeli' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'pi_qtymasuk' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255',
                'null'              => true
            ],
            'pi_harga' => [
                'type'              => 'VARCHAR',
                'constraint'        => '255'
            ],
            'pi_jenis' => [
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
        $this->forge->addPrimaryKey('pi_id', true);
        $this->forge->createTable('pembelian_item');
    }

    public function down()
    {
        $this->forge->dropTable('pembelian_item');
    }
}
