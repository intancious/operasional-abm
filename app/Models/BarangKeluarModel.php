<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangKeluarModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'barangkeluar';
    protected $primaryKey           = 'bk_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['bk_tanggal', 'bk_nomor', 'bk_keterangan', 'bk_unit', 'bk_debet', 'bk_kredit', 'bk_user'];

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
        // 0001/BK/110821
        $last = $this->orderBy('bk_id', 'DESC')->first();
        $lastNumb = $last ? explode('/', $last['bk_nomor'])[0] : 0;
        $valNumb = intval($lastNumb) + 1;
        $nomor = str_pad($valNumb, 4, "0", STR_PAD_LEFT) . '/BK/' . date('dm') . substr(date('Y'), -2);
        return $nomor;
    }
}
