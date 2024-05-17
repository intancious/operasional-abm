<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Logstokawal extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'ls_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'ls_barang' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true
			],
			'ls_before' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'ls_after' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'ls_beforeHarga' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'ls_afterHarga' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'ls_user' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true
			],
			'created_at' => [
				'type' 				=> 'DATETIME',
				'null' 				=> true,
			],
			'updated_at' => [
				'type' 				=> 'DATETIME',
				'null' 				=> true,
			],
		]);
		$this->forge->addPrimaryKey('ls_id', true);
		$this->forge->createTable('log_stokawal');
	}

	public function down()
	{
		$this->forge->dropTable('log_stokawal');
	}
}
