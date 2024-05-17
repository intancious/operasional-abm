<?php

namespace App\Models;

use CodeIgniter\Model;

class KoderekeningModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'rekening';
	protected $primaryKey           = 'rek_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDelete        = false;
	protected $protectFields        = true;
	protected $allowedFields        = ['reksub1_id', 'reksub2_id', 'reksub3_id', 'reksub4_id', 'reksub5_id', 'rek_kode', 'rek_nama'];

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

	public function rekening($sub = null)
	{
		if ($sub == 1) {
			$this->where('reksub1_id >', 0);
			$this->where('reksub2_id', NULL);
			$this->where('reksub3_id', NULL);
			$this->where('reksub4_id', NULL);
			$this->where('reksub5_id', NULL);
		} else if ($sub == 2) {
			$this->where('reksub1_id >', 0);
			$this->where('reksub2_id >', 0);
			$this->where('reksub3_id', NULL);
			$this->where('reksub4_id', NULL);
			$this->where('reksub5_id', NULL);
		} else if ($sub == 3) {
			$this->where('reksub1_id >', 0);
			$this->where('reksub2_id >', 0);
			$this->where('reksub3_id >', 0);
			$this->where('reksub4_id', NULL);
			$this->where('reksub5_id', NULL);
		} else if ($sub == 4) {
			$this->where('reksub1_id >', 0);
			$this->where('reksub2_id >', 0);
			$this->where('reksub3_id >', 0);
			$this->where('reksub4_id >', 0);
			$this->where('reksub5_id', NULL);
		} else if ($sub == 5) {
			$this->where('reksub1_id >', 0);
			$this->where('reksub2_id >', 0);
			$this->where('reksub3_id >', 0);
			$this->where('reksub4_id >', 0);
			$this->where('reksub5_id >', 0);
		}
		return $this->findAll();
	}
}
