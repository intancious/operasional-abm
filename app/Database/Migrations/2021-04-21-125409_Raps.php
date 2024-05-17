<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Raps extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'rap_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'rap_nama' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'default'			=> 'GENERAL'
			],
			'rap_tipe' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true
			],
			'rap_barang' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true
			],
			'rap_upah' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true
			],
			'rap_volume' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'null' 				=> true
			],
			'rap_harga' => [
				'type'				=> 'VARCHAR',
				'constraint'     	=> '255',
				'null' 				=> true
			],
			'rap_keterangan' => [
				'type'				=> 'TEXT',
				'null' 				=> true,
			],
			'rap_user' => [
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
			]
		]);
		$this->forge->addPrimaryKey('rap_id', true);
		$this->forge->createTable('raps');
	}

	public function down()
	{
		$this->forge->dropTable('raps');
	}
}
