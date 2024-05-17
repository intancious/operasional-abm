<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'user';
	protected $primaryKey           = 'usr_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDelete        = false;
	protected $protectFields        = true;
	protected $allowedFields        = ['usr_username', 'usr_nama', 'usr_nohp', 'usr_email', 'usr_password', 'usr_bagian', 'usr_photo', 'usr_role', 'usr_jamkerja', 'usr_jamkerja2', 'usr_aktif', 'usr_token'];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

	public function login($user, $password)
	{
		$user = $this->where(['usr_username' => $user])->orWhere(['usr_email' => $user])->first();
		if ($user) {
			$valid = password_verify($password, $user['usr_password']);
			if ($valid) {
				return $user;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function getUserId($id)
	{
		return $this->where(['usr_id' => $id])->first();
	}

	public function getUser($user)
	{
		return $this->where(['usr_username' => $user])->orWhere(['usr_email' => $user])->orWhere(['usr_nohp' => $user])->first();
	}

	public function getAccess($email, $token)
	{
		return $this->where(['usr_email' => $email, 'usr_token' => $token])->first();
	}
}
