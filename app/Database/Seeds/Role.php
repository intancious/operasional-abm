<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Role extends Seeder
{
	public function run()
	{
		$model = model('RoleModel');
		$model->insertBatch([
			[
				'role_nama' 	=> 'SUPER USER',
				'created_at'	=> date('Y-m-d H:i:s'),
				'updated_at'	=> date('Y-m-d H:i:s')
			],
			[
				'role_nama' 	=> 'USER',
				'created_at'	=> date('Y-m-d H:i:s'),
				'updated_at'	=> date('Y-m-d H:i:s')
			],
		]);
	}
}
