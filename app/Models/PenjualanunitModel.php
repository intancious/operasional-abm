<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanunitModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'penjualan_unit';
	protected $primaryKey           = 'pu_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDelete        = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'pu_jenis',
		'pu_kaliangsur',
		'pu_nomor',
		'pu_tglTrx',
		'pu_marketing',
		'pu_cust',
		'pu_unit',
		'pu_hrgriil',
		'pu_nup',
		'pu_mutu',
		'pu_tanahlebih',
		'pu_sbum',
		'pu_ajbn',
		'pu_pph',
		'pu_bphtb',
		'pu_realisasi',
		'pu_shm',
		'pu_kanopi',
		'pu_tandon',
		'pu_pompair',
		'pu_teralis',
		'pu_tembok',
		'pu_pondasi',
		'pu_pijb',
		'pu_ppn',
		'pu_fee',
		'pu_harga',
		'pu_hargaKredit',
		'pu_keterangan',
		'pu_kpr',
		'pu_tglPengajuanKpr',
		'pu_nilaiPengajuanKpr',
		'pu_tglAccKpr',
		'pu_nilaiAccKpr',
		'pu_tglRealisasiKpr',
		'pu_nilaiRealisasi',
		'pu_tglPencairanKpr',
		'pu_nilaiPencairan',
		'pu_debetKpr',
		'pu_kreditKpr',
		'pu_sisa',
		'pu_sisaDebet',
		'pu_user',
		'pu_status',
		'created_at',
		'updated_at'
	];

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

	public function dataPenjualan()
	{
		$piutangPenjualanModel = new \App\Models\PiutangpenjualanModel();
		$penjualan = $this->findAll();

		$hargaTrx1 = 0;
		$bayarTrx1 = 0;

		$hargaTrx2 = 0;
		$bayarTrx2 = 0;

		$hargaTrx3 = 0;
		$nominalTrxKpr = 0;
		$bayarTrx3 = 0;
		$bayarKpr = 0;
		foreach ($penjualan as $row) {
			if ($row['pu_jenis'] == 'cash') {
				$piutang = $piutangPenjualanModel->piutangPenjualan($row['pu_nomor']);
				$harga1 = $row['pu_harga'] ? str_replace('.', ',', $row['pu_harga']) : 0;
				$bayar1 = $row['pu_bayar'] ? str_replace('.', ',', $row['pu_bayar']) : 0;

				$hargaTrx1 += $harga1;
				$bayarTrx1 += $bayar1 + $piutang['totalBayar'];
			}
			if ($row['pu_jenis'] == 'kredit') {
				$piutang = $piutangPenjualanModel->piutangPenjualan($row['pu_nomor']);
				$harga2 = $row['pu_harga'] ? str_replace('.', ',', $row['pu_harga']) : 0;
				$bayar2 = $row['pu_bayar'] ? str_replace('.', ',', $row['pu_bayar']) : 0;

				$hargaTrx2 += $harga2;
				$bayarTrx2 += $bayar2 + $piutang['totalBayar'];
			}
			if ($row['pu_jenis'] == 'kpr') {
				$piutang = $piutangPenjualanModel->piutangPenjualan($row['pu_nomor']);
				$harga3 = $row['pu_harga'] ? str_replace('.', ',', $row['pu_harga']) : 0;
				$nominalKpr = $row['pu_nominalKpr'] ? str_replace('.', ',', $row['pu_nominalKpr']) : 0;
				$bayar3 = $row['pu_bayar'] ? str_replace('.', ',', $row['pu_bayar']) : 0;

				$hargaTrx3 += $harga3;
				$nominalTrxKpr += $nominalKpr;
				$bayarTrx3 += $bayar3 + $piutang['totalBayar'];
				$bayarKpr += $piutang['totalBayarKpr'];
			}
		}

		$sisaUmCash = ($hargaTrx1 - $bayarTrx1);
		$piutangUmkredit = ($hargaTrx2 - $bayarTrx2);
		$sisaDanaDitahan = ($nominalTrxKpr - $bayarKpr);
		$piutangUmKpr = ($hargaTrx3 - $nominalTrxKpr);
		$sisaUmKpr = $piutangUmKpr - $bayarTrx3;

		return [
			'penjualanTunai' => [
				'harga'				=> $hargaTrx1,
				'uangmuka' 			=> $bayarTrx1, // um yang dibayarkan oleh customer
				'sisaUm' 			=> $sisaUmCash > 0 ? $sisaUmCash : 0 // harga - sisaUm
			],
			'penjualanKredit' => [
				'harga'				=> $hargaTrx2,
				'uangmuka' 			=> $bayarTrx2,	// um yang dibayarkan oleh customer
				'piutangUm' 		=> $piutangUmkredit > 0 ? $piutangUmkredit : 0 // harga - uangmuka
			],
			'penjualanKpr' 	=> [
				'harga'				=> $hargaTrx3,
				'nominalKpr'		=> $nominalTrxKpr,	// nilai kpr
				'kprDibayar'		=> $bayarKpr,	// nilai kpr yang direalisasi
				'sisaDanaDitahan'	=> $sisaDanaDitahan > 0 ? $sisaDanaDitahan : 0, // nominalKpr - kprDibayar
				'piutangUm'			=> $piutangUmKpr > 0 ? $piutangUmKpr : 0, // harga - nominalKpr
				'bayarUm'			=> $bayarTrx3, 	// um yang dibayarkan oleh customer
				'sisaUm'			=> $sisaUmKpr > 0 ? $sisaUmKpr : 0 // piutangUm - bayarUm
			],
		];
	}

	public function hargaKredit($idRekening)
	{
		$data = $this->where('pu_hargaKredit', $idRekening)->findAll();
		$grandTotal = 0;
		foreach ($data as $row) {
			$harga = $row['pu_harga'] ? str_replace('.', ',', $row['pu_harga']) : 0;
			$grandTotal += $harga;
		}
		return $grandTotal;
	}

	public function bayarDebet($idRekening) // bisa jadi bayar um/titipan um/uang masuk
	{
		$data = $this->where('pu_bayarDebet', $idRekening)->findAll();
		$totalBayar = 0;
		foreach ($data as $row) {
			$bayar = $row['pu_bayar'] ? str_replace('.', ',', $row['pu_bayar']) : 0;
			$totalBayar += $bayar;
		}
		return $totalBayar;
	}

	public function kprDebet($idRekening)
	{
		$data = $this->where('pu_kprDebet', $idRekening)->findAll();
		$grandTotal = 0;
		foreach ($data as $row) {
			$kpr = $row['pu_nominalKpr'] ? str_replace('.', ',', $row['pu_nominalKpr']) : 0;
			$grandTotal += $kpr;
		}
		return $grandTotal;
	}

	public function sisaDebet($idRekening)
	{
		$data = $this->where('pu_sisaDebet', $idRekening)->findAll();
		$grandTotal = 0;
		foreach ($data as $row) {
			$sisa = $row['pu_sisa'] ? str_replace('.', ',', $row['pu_sisa']) : 0;
			$grandTotal += $sisa;
		}
		return $grandTotal;
	}
}
