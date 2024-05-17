<?php

namespace App\Models;

use CodeIgniter\Model;

class HutangsuplierBayarModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'hutangsuplier_bayar';
    protected $primaryKey           = 'hb_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['hb_tanggal', 'hb_nomor', 'hb_hutangsuplier', 'hb_bayar', 'hb_keterangan', 'hb_debet', 'hb_kredit', 'hb_isongkir', 'hb_istunai', 'hb_user', 'created_at', 'updated_at'];

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
        // 0001/HSB/110821
        $last = $this->orderBy('hb_id', 'DESC')->first();
        $lastNumb = $last ? explode('/', $last['hb_nomor'])[0] : 0;
        $valNumb = intval($lastNumb) + 1;
        $nomor = str_pad($valNumb, 4, "0", STR_PAD_LEFT) . '/HSB/' . date('dm') . substr(date('Y'), -2);
        return $nomor;
    }

    public function totalBayar($idHs)
    {
        $totalBayar = 0;
        $results = $this->where('hb_hutangsuplier', $idHs)->findAll();
        foreach ($results as $row) {
            $bayar = $row['hb_bayar'] ? str_replace(',', '.', $row['hb_bayar']) : 0;
            $totalBayar += $bayar;
        }

        return $totalBayar;
    }
}
