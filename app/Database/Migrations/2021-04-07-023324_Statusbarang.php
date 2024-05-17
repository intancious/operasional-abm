<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Statusbarang extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'sb_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'sb_nama' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'created_at' => [
				'type' 				=> 'DATETIME',
				'null' 				=> true,
			],
			'updated_at' => [
				'type' 				=> 'DATETIME',
				'null' 				=> true,
			]
		]);
		$this->forge->addPrimaryKey('sb_id', true);
		$this->forge->createTable('status_barang');
	}

	public function down()
	{
		$this->forge->dropTable('status_barang');
	}
}
