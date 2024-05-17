<?php

namespace App\Models;

use CodeIgniter\Model;

class OngkirPembelianModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'ongkir_pembelian';
    protected $primaryKey           = 'op_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['op_tanggal', 'op_nomor', 'op_hutangsuplier', 'op_suplier', 'op_bayar', 'op_kaskecil', 'op_keterangan', 'op_debet', 'op_kredit', 'op_user'];

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
}
