<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Types extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'type_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'type_nama' => [
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
		$this->forge->addPrimaryKey('type_id', true);
		$this->forge->createTable('types');
	}

	public function down()
	{
		$this->forge->dropTable('types');
	}
}
