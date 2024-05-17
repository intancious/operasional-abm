<?php

namespace App\Models;

use CodeIgniter\Model;

class TrxupahLainModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'trxupah_lain';
    protected $primaryKey           = 'tul_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['tul_trxupah', 'tul_tanggal', 'tul_nomor',  'tul_jenis', 'tul_kaskecil', 'tul_nominal', 'tul_keterangan', 'tul_debet', 'tub_user', 'tul_kredit', 'tul_user', 'created_at', 'updated_at'];

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
        // 0001/UBL/110821
        $last = $this->orderBy('tul_id', 'DESC')->first();
        $lastNumb = $last ? explode('/', $last['tul_nomor'])[0] : 0;
        $valNumb = intval($lastNumb) + 1;
        $nomor = str_pad($valNumb, 4, "0", STR_PAD_LEFT) . '/UBL/' . date('dm') . substr(date('Y'), -2);
        return $nomor;
    }
}
