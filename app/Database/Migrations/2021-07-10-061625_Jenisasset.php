<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Jenisasset extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'js_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'js_nama' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'null' 				=> true
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
		$this->forge->addPrimaryKey('js_id', true);
		$this->forge->createTable('jenis_asset');
	}

	public function down()
	{
		$this->forge->dropTable('jenis_asset');
	}
}
