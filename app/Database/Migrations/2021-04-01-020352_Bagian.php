<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Bagian extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'bagian_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'bagian_nama' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'bagian_akses' => [
				'type'       		=> 'LONGTEXT',
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
		$this->forge->addPrimaryKey('bagian_id', true);
		$this->forge->createTable('bagian');
	}

	public function down()
	{
		$this->forge->dropTable('bagian');
	}
}
