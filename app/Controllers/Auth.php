<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
	public function index()
	{
		$data = [
			'title_bar' 	=> 'Masuk ke Dashboard',
			'validation'    => $this->validation
		];
		return view('auth/login', $data);
	}

	public function login()
	{
		$validate = $this->validate([
			'user' => [
				'rules'        => 'required',
				'errors'    => [
					'required'  => 'Kolom ini harus diisi.'
				]
			],
			'password' => [
				'rules'     => 'required',
				'errors'    => [
					'required'  => 'Kolom ini harus diisi.'
				]
			]
		]);

		if (!$validate) {
			return redirect()->to('/auth')->withInput();
		}

		$request = \Config\Services::request();
		$userModel = new \App\Models\UserModel();

		$query = $userModel->login($request->getPost('user'), $request->getPost('password'));
		if ($query) {
			if ($query['usr_aktif'] == 1) {
				$data = [
					'usr_id'        => $query['usr_id'],
					'usr_role'      => $query['usr_role'],
					'usr_bagian'   	=> $query['usr_bagian'],
					'logged_in'     => TRUE
				];
				$this->session->set($data); 
				return redirect()->to('/dashboard');
			} else {
				session()->setFlashdata('pesan', '<div class="alert alert-warning alert-dismissible fade show" role="alert">Akun belum aktif atau dinonaktifkan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				return redirect()->to('/auth');
			}
		} else {
			session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">User atau password salah.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			return redirect()->to('/auth')->withInput();
		}
	}

	public function forgot()
	{
		$data = [
			'title_bar'     => 'Lupa Password',
			'validation'    => $this->validation,
			'captcha'       => $this->captcha->load()
		];
		return view('auth/forgot', $data);
	}

	public function reset()
	{
		$request = \Config\Services::request();
		$userModel = new \App\Models\UserModel();

		$validate = $this->validate([
			'user' => [
				'rules'        => 'required',
				'errors'    => [
					'required'  => 'Kolom ini harus diisi.'
				]
			],
			'captcha' => [
				'rules'     => 'required',
				'errors'    => [
					'required'  => 'Kolom ini harus diisi.'
				]
			]
		]);
		if (!$validate) {
			return redirect()->to('/auth/forgot')->withInput();
		}
		$user = $request->getVar('user');
		$number1 = $request->getVar('number1');
		$number2 = $request->getVar('number2');
		$total = $request->getVar('captcha');
		$captcha = $this->captcha->solve($number1, $number2, $total);
		if ($captcha == false) {
			session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Hasil penjumlahan salah!</div>');
			return redirect()->to('/auth/forgot')->withInput();
		}
		$query = $userModel->getUser($user);
		if ($query) {
			$sendMail = new \App\Libraries\SendMail();
			$sendMail->reset($query);
			
			session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Konfirmasi permintaan perubahan password telah terkirim ke Email!</div>');
			return redirect()->to('/auth/forgot');
		} else {
			session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">User tidak ditemukan!</div>');
			return redirect()->to('/auth/forgot')->withInput();
		}
	}

	public function newpassword()
	{
		$userModel = new \App\Models\UserModel();
		$request = \Config\Services::request();

		$email = $request->getVar('email');
		$token = $request->getVar('token');
		if (isset($email) && isset($token)) {
			$query = $userModel->where(['usr_email' => $email, 'usr_token' => $token])->first();
			if ($query) {
				$data = [
					'title_bar'     => 'Password Baru',
					'user'          => $query,
					'validation'    => $this->validation,
					'captcha'       => $this->captcha->load()
				];
				return view('auth/newpassword', $data);
			} else {
				session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Token/email tidak valid!</div>');
				return redirect()->to('/auth/forgot');
			}
		} else {
			session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Parameter tidak valid!</div>');
			return redirect()->to('/auth/forgot');
		}
	}

	public function changepassword()
	{
		$request = \Config\Services::request();
		$userModel = new \App\Models\UserModel();

		$id = $request->getVar('id');
		$user = $userModel->find($id);
		$validate = $this->validate([
			'password' => [
				'rules'     => 'required',
				'errors'    => [
					'required'  => 'Kolom ini harus diisi.'
				]
			],
			'confpassword' => [
				'rules'     => 'required|matches[password]',
				'errors'    => [
					'required'      => 'Kolom ini harus diisi.',
					'matches'       => 'Konfirmasi password salah'
				]
			],
			'captcha' => [
				'rules'     => 'required',
				'errors'    => [
					'required'  => 'Kolom ini harus diisi.'
				]
			]
		]);
		if (!$validate) {
			return redirect()->to('/auth/newpassword?email=' . $user['usr_email'] . '&token=' . $user['usr_token'])->withInput();
		}

		$number1 = $request->getVar('number1');
		$number2 = $request->getVar('number2');
		$total = $request->getVar('captcha');
		$captcha = $this->captcha->solve($number1, $number2, $total);
		if ($captcha == false) {
			session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Hasil penjumlahan salah!</div>');
			return redirect()->to('/auth/newpassword?email=' . $user['usr_email'] . '&token=' . $user['usr_token'])->withInput();
		}

		$data = [
			'usr_password'  => password_hash($request->getVar('password'), PASSWORD_DEFAULT),
			'usr_token'     => random_string('alnum', 77)
		];
		$query = $userModel->update(['id' => $user['usr_id']], $data);
		if ($query) {
			session()->setFlashdata('pesan', '<div class="alert alert-success" role="alert">Password berhasil diperbarui!</div>');
			return redirect()->to('/auth');
		} else {
			session()->setFlashdata('pesan', '<div class="alert alert-danger" role="alert">Gagal memperbarui password!</div>');
			return redirect()->to('/auth/forgot')->withInput();
		}
	}

	public function logout()
	{
		$data = ['usr_id', 'usr_role', 'usr_bagian', 'logged_in'];
		$this->session->remove($data);
		return redirect()->to('/');
	}

	// public function register()
	// {
	// 	$userModel = new \App\Models\UserModel();
	// 	$data = [
	// 		'usr_username' 	=> 'admin',
	// 		'usr_nama' 		=> 'Administrator',
	// 		'usr_nohp' 		=> '081234567890',
	// 		'usr_email' 	=> 'email@example.com',
	// 		'usr_password' 	=> password_hash('admin', PASSWORD_DEFAULT),
	// 		'usr_bagian' 	=> 1,
	// 		'usr_role' 		=> 1,
	// 		'usr_aktif' 	=> 1,
	// 		'usr_token' 	=> random_string('alnum', 77)
	// 	];
	// 	$query = $userModel->save($data);
	// 	if ($query) {
	// 		return 'Sukses';
	// 	} else {
	// 		return 'Gagal';
	// 	}
	// }
}
