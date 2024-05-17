<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class User extends Seeder
{
	public function run()
	{
		$model = model('UserModel');
		$model->insertBatch([
			[
				'usr_username' 	=> 'admin',
				'usr_nama' 		=> 'SUPER ADMIN',
				'usr_nohp' 		=> '081234567890',
				'usr_email' 	=> 'email@example.com',
				'usr_password' 	=> password_hash('admin', PASSWORD_DEFAULT),
				'usr_bagian' 	=> 1,
				'usr_role' 		=> 1,
				'usr_aktif' 	=> 1,
				'usr_token' 	=> '02vsiseapw7t6zdwk6z5igrx3lkneh7dm70wbxemyjwxpw1qayiyqf1omcthwwibk1fj5wel4v2ti',
				'created_at'	=> Time::now('Asia/Jakarta', 'id_ID'),
				'updated_at'	=> Time::now('Asia/Jakarta', 'id_ID')
			]
		]);
	}
}
