<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Assets extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'as_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'as_nama' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'null' 				=> true
			],
			'as_jenis' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true,
			],
			'as_kode' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'null' 				=> true,
			],
			'as_unit' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'null' 				=> true,
			],
			'as_tgl' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'null' 				=> true,
			],
			'as_harga' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'null' 				=> true,
			],
			'as_umur' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'null' 				=> true,
			],
			'as_debet' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true,
			],
			'as_kredit' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true,
			],
			'as_user' => [
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
		$this->forge->addPrimaryKey('as_id', true);
		$this->forge->createTable('assets');
	}

	public function down()
	{
		$this->forge->dropTable('assets');
	}
}
