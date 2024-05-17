<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Kpr extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'kpr_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'kpr_nama' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'null' 				=> true
			],
			'kpr_keterangan' => [
				'type'				=> 'TEXT',
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
		$this->forge->addPrimaryKey('kpr_id', true);
		$this->forge->createTable('kpr');
	}

	public function down()
	{
		$this->forge->dropTable('kpr');
	}
}
