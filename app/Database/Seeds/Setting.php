<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Setting extends Seeder
{
	public function run()
	{
		$model = model('SettingModel');
		$model->insert([
			'setting_nama' 		=> 'ASA DREAMLAND',
			'setting_logo' 		=> 'logo1.png',
			'setting_logo2' 	=> 'logo2.png',
			'setting_favicon' 	=> 'logo3.png',
			'created_at'		=> date('Y-m-d H:i:s'),
			'updated_at'		=> date('Y-m-d H:i:s')
		]);
	}
}
