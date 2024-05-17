<?php

namespace App\Models;

use CodeIgniter\Model;

class TagihanPuModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'tagihanpu';
    protected $primaryKey           = 'tp_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDelete        = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['tp_pu', 'tp_jenis', 'tp_nomor', 'tp_angsuran', 'tp_keterangan', 'tp_tgltrx', 'tp_nilai', 'tp_jthtempo', 'tp_tglbayar', 'tp_nominal', 'tp_debet', 'tp_kredit', 'tp_user'];

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
        // 0001/INV/110821
        $last = $this->orderBy('tp_id', 'DESC')->first();
        $lastNumb = $last ? explode('/', $last['tp_nomor'])[0] : 0;
        $valNumb = intval($lastNumb) + 1;
        $nomor = str_pad($valNumb, 4, "0", STR_PAD_LEFT) . '/INV/' . date('dm') . substr(date('Y'), -2);
        return $nomor;
    }

    public function getTotalBayar($puId, $jenis)
    {
        $tagihan = $this->where(['tp_pu' => $puId, 'tp_nominal >' => 0, 'tp_jenis' => $jenis])->findAll();
        $totalBayar = 0;
        foreach ($tagihan as $row) {
            $bayar = $row['tp_nominal'] ? str_replace(',', '.', $row['tp_nominal']) : 0;
            $totalBayar += $bayar;
        }
        return $totalBayar;
    }
}
