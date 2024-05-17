<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Unit extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'unit_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'unit_rekening' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true,
			],
			'unit_kode' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'unit_tipe' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true,
			],
			'unit_nomor' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'unit_nama' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'unit_keterangan' => [
				'type'       		=> 'TEXT',
				'null' 				=> true,
			],
			'unit_user' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
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
		$this->forge->addPrimaryKey('unit_id', true);
		$this->forge->createTable('unit');
	}

	public function down()
	{
		$this->forge->dropTable('unit');
	}
}
