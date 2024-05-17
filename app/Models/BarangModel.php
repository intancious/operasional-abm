<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'barang';
	protected $primaryKey           = 'barang_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDelete        = false;
	protected $protectFields        = true;
	protected $allowedFields        = ['barang_kode', 'barang_rekening', 'barang_nama', 'barang_kategori', 'barang_satuan', 'barang_jumlah', 'barang_minstok', 'barang_harga', 'barang_user'];

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

	public function getStokSaatIni($id)
	{
		$pembelianItemModel = new \App\Models\PembelianItemModel();
		$barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
		$barang = $this->find($id);
		$stokAwal = $barang['barang_jumlah'] ? str_replace(',', '.', $barang['barang_jumlah']) : 0;

		$pembelian = 0;
		$pembelianItem = $pembelianItemModel->where('pi_barang', $id)->findAll();
		foreach ($pembelianItem as $row) {
			$qtymasuk = $row['pi_qtydatang'] ? str_replace(',', '.', $row['pi_qtydatang']) : 0;
			$pembelian += $qtymasuk;
		}

		$barangkeluar = 0;
		$keluar = $barangKeluarItemModel->where('bki_barang', $id)->findAll();
		foreach ($keluar as $row) {
			$qty = $row['bki_qty'] ? str_replace(',', '.', $row['bki_qty']) : 0;
			$barangkeluar += $qty;
		}

		return ($stokAwal + $pembelian) - $barangkeluar;
	}

	public function rataRataHarga($idBarang)
	{
		$barangModel = new \App\Models\BarangModel();
		$pembelianItemModel = new \App\Models\PembelianItemModel();

		$barang = $barangModel->find($idBarang);
		$stokAwal = $barang ? str_replace(',', '.', $barang['barang_jumlah']) : 0;
		$hargaAwal = $barang ? str_replace(',', '.', $barang['barang_harga']) : 0;

		$totalNilaiAwal = floatval($hargaAwal) * floatval($stokAwal);
		$pembelianItem = $pembelianItemModel->where('pi_barang', $idBarang)->findAll();

		$totalStokPembelian = 0;
		$totalNilaiPembelian = 0;
		foreach ($pembelianItem as $row) {
			$qtyPembelian = $row['pi_qtydatang'] ? str_replace(',', '.', $row['pi_qtydatang']) : 0;
			$hrgPembelian = $row['pi_harga'] ? str_replace(',', '.', $row['pi_harga']) : 0;
			$hargaPembelian = (floatval($hrgPembelian) * floatval($qtyPembelian));

			$totalStokPembelian += $qtyPembelian;
			$totalNilaiPembelian += $hargaPembelian;
		}

		$totalStok = $stokAwal + $totalStokPembelian;
		$totalHarga = $totalNilaiAwal + $totalNilaiPembelian;

		return $totalStokPembelian > 0 ? ($totalHarga / $totalStok) : floatval($hargaAwal);
	}

	public function getSaldoAwalByRek($kodeRekening)
	{
		$rekeningModel = new \App\Models\KoderekeningModel();
		$barangModel = new \App\Models\BarangModel();
		$rekening = $rekeningModel->where('rek_kode', $kodeRekening)->first();
		$exRek = explode('.', $rekening['rek_kode']);
		$rekKode = $exRek[1] - 200;
		$rekeningPersediaan = $rekeningModel->where('rek_kode', $exRek[0] . '.200')->first();
		$barang = $barangModel->where(['barang_kode' => $rekKode, 'barang_rekening' => $rekeningPersediaan['rek_id']])->first();

		$stokAwal = $barang['barang_jumlah'] ? str_replace(',', '.', $barang['barang_jumlah']) : 0;
		$harga = $barang['barang_harga'] ? str_replace(',', '.', $barang['barang_harga']) : 0;

		return [
			'rekKode'   => $rekening['rek_kode'],
			'rekNama'   => $rekening['rek_nama'],
			'rekSaldo'  => $stokAwal * $harga
		];
	}

	public function getSaldoAwal()
	{
		$rekeningModel = new \App\Models\KoderekeningModel();
		$barangModel = new \App\Models\BarangModel();
		$barangs = $barangModel->orderBy('barang_kode', 'ASC')->findAll();
		foreach ($barangs as $row) {
			$rekeningPersediaan = $rekeningModel->where('rek_id', $row['barang_rekening'])->first(); // 114.2
			$exRekPersediaan = explode('.', $rekeningPersediaan['rek_kode']);
			$kodeRekening = $exRekPersediaan[0] . '.' . ($exRekPersediaan[1] + $row['barang_kode']); // 113.2XX

			$rekening = $rekeningModel->where('rek_kode', $kodeRekening)->first();
			$stokAwal = $row['barang_jumlah'] ? str_replace(',', '.', $row['barang_jumlah']) : 0;
			$harga = $row['barang_harga'] ? str_replace(',', '.', $row['barang_harga']) : 0;

			$results[] = [
				'rekKode'   => $rekening['rek_kode'],
				'rekNama'   => $rekening['rek_nama'],
				'rekSaldo'  => $stokAwal * $harga
			];
		}

		return $results;
	}
}
