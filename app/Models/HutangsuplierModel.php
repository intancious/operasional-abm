<?php

namespace App\Models;

use CodeIgniter\Model;

class HutangsuplierModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'hutangsuplier';
    protected $primaryKey           = 'hs_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['hs_tanggal', 'hs_nomor', 'hs_pembelian', 'hs_suplier', 'hs_total', 'hs_tempo', 'hs_debet', 'hs_kredit', 'hs_user', 'created_at', 'updated_at'];

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

    public function buatNoFaktur()
    {
        // 0001/HS/110821
        $last = $this->orderBy('hs_id', 'DESC')->first();
        $lastNumb = $last ? explode('/', $last['hs_nomor'])[0] : 0;
        $valNumb = intval($lastNumb) + 1;
        $nomor = str_pad($valNumb, 4, "0", STR_PAD_LEFT) . '/HS/' . date('dm') . substr(date('Y'), -2);
        return $nomor;
    }
}
