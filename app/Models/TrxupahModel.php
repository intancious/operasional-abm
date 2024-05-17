<?php

namespace App\Models;

use CodeIgniter\Model;

class TrxupahModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'trxupah';
    protected $primaryKey           = 'tu_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['tu_nomor', 'tu_tanggal', 'tu_tukang', 'tu_unit', 'tu_totalupah', 'tu_lembur', 'tu_bon', 'tu_keterangan', 'tu_debet', 'tu_kredit', 'tu_user', 'created_at', 'updated_at'];

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
        // 0001/UPH/110821
        $last = $this->orderBy('tu_id', 'DESC')->first();
        $lastNumb = $last ? explode('/', $last['tu_nomor'])[0] : 0;
        $valNumb = intval($lastNumb) + 1;
        $nomor = str_pad($valNumb, 4, "0", STR_PAD_LEFT) . '/UPH/' . date('dm') . substr(date('Y'), -2);
        return $nomor;
    }
}
