<?php

namespace App\Models;

use CodeIgniter\Model;

class BiayapenjualanModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'biaya_penjualan';
	protected $primaryKey           = 'bp_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDelete        = false;
	protected $protectFields        = true;
	protected $allowedFields        = ['bp_tanggal', 'bp_penjualan', 'bp_biayalain', 'bp_uraian', 'bp_nominal', 'bp_debet', 'bp_kredit', 'bp_user', 'bp_kembali', 'created_at', 'updated_at'];

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
}
