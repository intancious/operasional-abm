<?php

namespace App\Models;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'pembelian';
    protected $primaryKey           = 'pb_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['pb_tanggal', 'pb_nomor', 'pb_total', 'pb_ongkir', 'pb_supplier', 'pb_keterangan', 'pb_jenis', 'pb_jatuhtempo', 'pb_kaskecil', 'pb_debet', 'pb_kredit', 'pb_debetongkir', 'pb_kreditongkir', 'pb_status', 'pb_user', 'pb_approval'];

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
        // 0001/PB/110821
        $last = $this->orderBy('pb_id', 'DESC')->first();
        $lastNumb = $last ? explode('/', $last['pb_nomor'])[0] : 0;
        $valNumb = intval($lastNumb) + 1;
        $nomor = str_pad($valNumb, 4, "0", STR_PAD_LEFT) . '/PB/' . date('dm') . substr(date('Y'), -2);
        return $nomor;
    }

    public function getReward($idPembelian, $idUser)
    {
        $itemPembelianModel = new \App\Models\PembelianItemModel();
        $pembelian = $this->where(['pb_id' => $idPembelian, 'pb_user' => $idUser])->first();
        $itemPembelian = $itemPembelianModel->where(['pi_pembelian' => $pembelian['pb_id']])->findAll();

        $diajukan = 0;
        $disetujui = 0;
        $datang = 0;
        foreach ($itemPembelian as $row) {
            $diajukan += $row['pi_qtybeli'] ? str_replace(',', '.', $row['pi_qtybeli']) : 0;
            $disetujui += $row['pi_qtymasuk'] ? str_replace(',', '.', $row['pi_qtymasuk']) : 0;
            $datang += $row['pi_qtydatang'] ? str_replace(',', '.', $row['pi_qtydatang']) : 0;
        }
        return [
            'diajukan'  => $diajukan,
            'disetujui' => $disetujui,
            'datang'    => $datang,
            'persen'    => ($datang > 0) || ($diajukan > 0) ? ($datang / $diajukan) * 100 : 0
        ];
    }

    public function rewards($perPage)
    {
        $itemPembelianModel = new \App\Models\PembelianItemModel();

        $request = \Config\Services::request();

        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $this->where('pembelian.pb_tanggal >=', $start);
            $this->where('pembelian.pb_tanggal <=', $end);
        }

        if ($request->getVar('user')) {
            $this->where('pembelian.pb_user', $request->getVar('user'));
        }

        $pembelian = $this->join('user', 'user.usr_id = pembelian.pb_user', 'left')
            ->select('pembelian.*, user.usr_nama')
            ->orderBy('pembelian.pb_tanggal', 'DESC')
            ->paginate($perPage, 'view');

        foreach ($pembelian as $row) {
            $itemPembelian = $itemPembelianModel->where(['pi_pembelian' => $row['pb_id']])->findAll();

            $diajukan = 0;
            $disetujui = 0;
            $datang = 0;
            foreach ($itemPembelian as $item) {
                $diajukan += $item['pi_qtybeli'] ? str_replace(',', '.', $item['pi_qtybeli']) : 0;
                $disetujui += $item['pi_qtymasuk'] ? str_replace(',', '.', $item['pi_qtymasuk']) : 0;
                $datang += $item['pi_qtydatang'] ? str_replace(',', '.', $item['pi_qtydatang']) : 0;
            }

            $results[] = [
                'pb_tanggal' => $row['pb_tanggal'],
                'usr_id'    => $row['pb_user'],
                'usr_nama'  => $row['usr_nama'],
                'diajukan'  => $diajukan,
                'disetujui' => $disetujui,
                'datang'    => $datang,
                'persen'    => ($datang > 0) || ($diajukan > 0) ? ($datang / $diajukan) * 100 : 0
            ];
        }

        return isset($results) ? $results : [];
    }
}
