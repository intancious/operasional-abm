<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangKeluarItemModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'barangkeluar_item';
    protected $primaryKey           = 'bki_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['bki_barangkeluar', 'bki_barang', 'bki_qty', 'bki_harga', 'created_at', 'updated_at'];

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

    public function getItems($bkId)
    {
        $barangModel = new \App\Models\BarangModel();
        $items = $this->where('bki_barangkeluar', $bkId)->findAll();
        $totalNilai = 0;
        foreach ($items as $row) {
            $qty = $row['bki_qty'] ? str_replace(',', '.', $row['bki_qty']) : 0;
            $harga = $row['bki_harga'] ? str_replace(',', '.', $row['bki_harga']) : 0;
            // $rataRataHarga = $barangModel->rataRataHarga($row['bki_barang']);
            $totalNilai += $harga * $qty;
        }

        return [
            'totalBarang' => count($items),
            'nilaiBarang' => $totalNilai
        ];
    }

    public function itemKeluar($idBarang)
    {
        $barangModel = new \App\Models\BarangModel();
        $totalQty = 0;
        $subtotal = 0;
        $items = $this->where(['bki_barang' => $idBarang])->findAll();
        $rataRataHarga = $barangModel->rataRataHarga($idBarang);
        foreach ($items as $row) {
            $qty = $row['bki_qty'] ? str_replace(',', '.', $row['bki_qty']) : 0;

            $totalQty += $qty;
            $subtotal += $qty * $rataRataHarga;
        }
        return [
            'qty'       => $totalQty,
            'harga'     => $rataRataHarga,
            'total'     => $totalQty > 0 ? $totalQty * $rataRataHarga : 0
        ];
    }
}
