<?php

namespace App\Models;

use CodeIgniter\Model;

class TrxupahBayarModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'trxupah_bayar';
    protected $primaryKey           = 'tub_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['tub_tanggal', 'tub_nomor', 'tub_trxupah', 'tub_tupah', 'tub_tupahdebet', 'tub_tbon', 'tub_tbonkredit', 'tub_tsupah', 'tub_tsupahkredit', 'tub_bayar', 'tub_debet', 'tub_kredit', 'tub_keterangan', 'tub_user', 'created_at', 'updated_at'];

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
        // 0001/BUP/110821
        $last = $this->orderBy('tub_id', 'DESC')->first();
        $lastNumb = $last ? explode('/', $last['tub_nomor'])[0] : 0;
        $valNumb = intval($lastNumb) + 1;
        $nomor = str_pad($valNumb, 4, "0", STR_PAD_LEFT) . '/BUP/' . date('dm') . substr(date('Y'), -2);
        return $nomor;
    }

    public function totalBayarUpah($trxupahId)
    {
        $totalBayar = 0;
        $pembayaran = $this->where(['tub_trxupah' => $trxupahId])->findAll();
        foreach ($pembayaran as $row) {
            $bayar = $row['tub_bayar'] ? str_replace(',', '.', $row['tub_bayar']) : 0;
            $totalBayar += $bayar;
        }
        return $totalBayar;
    }
}
