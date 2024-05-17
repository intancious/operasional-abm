<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Role extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'role_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'role_nama' => [
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
		$this->forge->addPrimaryKey('role_id', true);
		$this->forge->createTable('role');
	}

	public function down()
	{
		$this->forge->dropTable('role');
	}
}
