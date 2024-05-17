<?php

namespace App\Models;

use CodeIgniter\Model;

class PiutangpenjualanModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'piutang_penjualan';
	protected $primaryKey           = 'pc_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDelete        = false;
	protected $protectFields        = true;
	protected $allowedFields        = ['pc_notrx', 'pc_penjualan', 'pc_bayar', 'pc_bayarDebet', 'pc_bayarKredit', 'pc_bayarKpr', 'pc_bayarKprDebet', 'pc_bayarKprKredit', 'pc_sisa', 'pc_sisaDebet', 'pc_keterangan', 'pc_bayarLebih', 'pc_debetLebih', 'pc_kreditLebih', 'pc_keteranganLebih', 'pc_user', 'pc_primary', 'created_at', 'updated_at'];

	// Dates
	protected $useTimestamps        = false;
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

	public function piutangPenjualan($puNomor)
	{
		$piutang = $this->where('pc_penjualan', $puNomor)->findAll();
		$totalBayar = 0;
		$totalBayarKpr = 0;
		$totalSisa = 0;
		foreach ($piutang as $row) {
			$bayar = $row['pc_bayar'] ? str_replace('.', ',', $row['pc_bayar']) : 0;
			$bayarKpr = $row['pc_bayarKpr'] ? str_replace('.', ',', $row['pc_bayarKpr']) : 0;
			$sisa = $row['pc_sisa'] ? str_replace('.', ',', $row['pc_sisa']) : 0;

			$totalBayar += $bayar;
			$totalBayarKpr += $bayarKpr;
			$totalSisa += $sisa;
		}

		return [
			'totalBayar' 	=> $totalBayar,
			'totalBayarKpr' => $totalBayarKpr,
			'totalSisa' 	=> $totalSisa
		];
	}

	public function bayarDebet($idRekening) // bisa jadi bayar um/titipan um/uang masuk
	{
		$data = $this->where('pc_bayarDebet', $idRekening)->findAll();
		$totalBayar = 0;
		foreach ($data as $row) {
			$bayar = $row['pc_bayar'] ? str_replace('.', ',', $row['pc_bayar']) : 0;
			$totalBayar += $bayar;
		}
		return $totalBayar;
	}

	public function bayarKredit($idRekening)
	{
		$data = $this->where('pc_bayarKredit', $idRekening)->findAll();
		$totalBayar = 0;
		foreach ($data as $row) {
			$bayar = $row['pc_bayar'] ? str_replace('.', ',', $row['pc_bayar']) : 0;
			$totalBayar += $bayar;
		}
		return $totalBayar;
	}

	public function bayarKprDebet($idRekening)
	{
		$data = $this->where('pc_bayarKprDebet', $idRekening)->findAll();
		$totalBayar = 0;
		foreach ($data as $row) {
			$bayar = $row['pc_bayarKpr'] ? str_replace('.', ',', $row['pc_bayarKpr']) : 0;
			$totalBayar += $bayar;
		}
		return $totalBayar;
	}

	public function bayarKprKredit($idRekening)
	{
		$data = $this->where('pc_bayarKprKredit', $idRekening)->findAll();
		$totalBayar = 0;
		foreach ($data as $row) {
			$bayar = $row['pc_bayarKpr'] ? str_replace('.', ',', $row['pc_bayarKpr']) : 0;
			$totalBayar += $bayar;
		}
		return $totalBayar;
	}

	public function sisaDebet($idRekening)
	{
		$data = $this->where('pc_sisaDebet', $idRekening)->findAll();
		$totalBayar = 0;
		foreach ($data as $row) {
			$bayar = $row['pc_sisa'] ? str_replace('.', ',', $row['pc_sisa']) : 0;
			$totalBayar += $bayar;
		}
		return $totalBayar;
	}
}
