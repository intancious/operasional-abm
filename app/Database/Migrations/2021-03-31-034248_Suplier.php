<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Suplier extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'suplier_id' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'auto_increment' 	=> true,
			],
			'suplier_rekening' => [
				'type'				=> 'BIGINT',
				'constraint'     	=> 20,
				'unsigned'       	=> true,
				'null' 				=> true,
			],
			'suplier_kode' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'suplier_nama' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'suplier_alamat' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '255',
				'null' 				=> true,
			],
			'suplier_telp' => [
				'type'       		=> 'VARCHAR',
				'constraint' 		=> '15',
				'null' 				=> true,
			],
			'suplier_user' => [
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
		$this->forge->addPrimaryKey('suplier_id', true);
		$this->forge->createTable('suplier');
	}

	public function down()
	{
		$this->forge->dropTable('suplier');
	}
}
