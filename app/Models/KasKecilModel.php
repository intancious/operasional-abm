<?php

namespace App\Models;

use CodeIgniter\Model;

class KasKecilModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'kas_kecil';
    protected $primaryKey           = 'kk_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['kk_jenis', 'kk_nomor', 'kk_tanggal', 'kk_uraian', 'kk_nominal', 'kk_kembali', 'kk_debet', 'kk_kredit', 'kk_user', 'kk_approval', 'kk_status', 'kk_kembaliDebet', 'kk_kembaliKredit'];

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

    public function getTransaksi($jenis, $idKas)
    {
        if ($jenis == 1) {
            $pembelianModel = new \App\Models\PembelianModel();

            $pembelian = $pembelianModel->where('pb_kaskecil', $idKas)->findAll();
            $totalPembelian = 0;
            foreach ($pembelian as $pb) {
                $totalPembelian += $pb['pb_total'] ? str_replace(',', '.', $pb['pb_total']) : 0;
            }

            return $totalPembelian;
        } else if ($jenis == 4) {
            $trxupahLainModel = new \App\Models\TrxupahLainModel();
            $kasbon = $trxupahLainModel->where('tul_kaskecil', $idKas)->findAll();
            $totalKasbon = 0;
            foreach ($kasbon as $row) {
                $totalKasbon += $row['tul_nominal'] ? str_replace(',', '.', $row['tul_nominal']) : 0;
            }
            return $totalKasbon;
        } else {
            $opModel = new \App\Models\OperasionalModel();
            $operasional = $opModel->where('tl_kaskecil', $idKas)->findAll();
            $totalOperasional = 0;
            foreach ($operasional as $row) {
                $totalOperasional += $row['tl_nominal'] ? str_replace(',', '.', $row['tl_nominal']) : 0;
            }

            $ongkirModel = new \App\Models\OngkirPembelianModel();
            $ongkir = $ongkirModel->where('op_kaskecil', $idKas)->findAll();
            $totalOngkir = 0;
            foreach ($ongkir as $ok) {
                $totalOngkir += $ok['op_bayar'] ? str_replace(',', '.', $ok['op_bayar']) : 0;
            }
            return $totalOperasional + $totalOngkir;
        }
    }

    public function getJenisKas($id = null)
    {
        if ($id) {
            if ($id == 1) {
                $jenis = [
                    'id'    => 1,
                    'name'  => 'PEMBELIAN MATERIAL',
                ];
            } else if ($id == 2) {
                $jenis = [
                    'id'    => 2,
                    'name'  => 'OPERASIONAL PRODUKSI',
                ];
            } else if ($id == 3) {
                $jenis = [
                    'id'    => 3,
                    'name'  => 'OPERASIONAL KANTOR',
                ];
            } else if ($id == 4) {
                $jenis = [
                    'id'    => 4,
                    'name'  => 'KAS BON / HUTANG',
                ];
            }
        } else {
            $jenis = [
                [
                    'id'    => 1,
                    'name'  => 'PEMBELIAN MATERIAL',
                ],
                [
                    'id'    => 2,
                    'name'  => 'OPERASIONAL PRODUKSI',
                ],
                [
                    'id'    => 3,
                    'name'  => 'OPERASIONAL KANTOR',
                ],
                [
                    'id'    => 4,
                    'name'  => 'KAS BON / HUTANG',
                ]
            ];
        }
        return $jenis;
    }
}
