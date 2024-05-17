<?php

namespace App\Models;

use CodeIgniter\Model;

class PembelianItemModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'pembelian_item';
    protected $primaryKey           = 'pi_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['pi_pembelian', 'pi_barang', 'pi_qtybeli', 'pi_qtymasuk', 'pi_qtydatang', 'pi_harga', 'pi_total', 'pi_jenis', 'pi_suplier', 'pi_jatuhtempo', 'pi_debet', 'pi_kredit', 'created_at', 'updated_at'];

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

    public function itemPembelian($idBarang)
    {
        $totalQty = 0;
        $subtotal = 0;
        $items = $this->where(['pi_barang' => $idBarang, 'pi_qtymasuk >' => 0])->findAll();
        foreach ($items as $row) {
            $qtyMasuk = $row['pi_qtymasuk'] ? str_replace(',', '.', $row['pi_qtymasuk']) : 0;
            $harga = $row['pi_harga'] ? str_replace(',', '.', $row['pi_harga']) : 0;

            $totalQty += $qtyMasuk;
            $subtotal += $qtyMasuk * $harga;
        }
        $rataRataPembelian = $totalQty ? ($subtotal / $totalQty) : 0;
        return [
            'qty'       => $totalQty,
            'harga'     => $rataRataPembelian,
            'total'     => $totalQty > 0 ? $totalQty * $rataRataPembelian : 0
        ];
    }
}
