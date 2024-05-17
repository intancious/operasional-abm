<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Kategoribarang extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'kabar_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'kabar_nama' => [
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
		$this->forge->addPrimaryKey('kabar_id', true);
		$this->forge->createTable('kategori_barang');
	}

	public function down()
	{
		$this->forge->dropTable('kategori_barang');
	}
}
