<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Bagian extends Seeder
{
	public function run()
	{
		$model = model('BagianModel');
		$model->insertBatch([
			[
				'bagian_nama' 		=> 'OWNER',
				'bagian_akses' 		=> 'users,addUser,insertUser,editUser,updateUser,deleteUser,bagian,addBagian,insertBagian,editBagian,updateBagian,deleteBagian,rekening,addRekening,insertRekening,editRekening,updateRekening,deleteRekening,satuan,addSatuan,insertSatuan,editSatuan,updateSatuan,deleteSatuan,kategoriBarang,addKategoriBarang,insertKategoriBarang,editKategoriBarang,updateKategoriBarang,deleteKategoriBarang,barang,addBarang,insertBarang,editBarang,updateBarang,deleteBarang,unit,addUnit,insertUnit,editUnit,updateUnit,deleteUnit,suplier,addSuplier,insertSuplier,editSuplier,updateSuplier,deleteSuplier,pengaturan,updatePengaturan,pembelian,addPembelian,insertPembelian,editPembelian,updatePembelian,deletePembelian,statusbarang,addStatusbarang,insertStatusbarang,editStatusbarang,updateStatusbarang,deleteStatusbarang,approvalPengajuan,barangkeluar,addBarangKeluar,insertBarangKeluar,editBarangKeluar,updateBarangKeluar,deleteBarangKeluar,tipeUnit,addTipeUnit,insertTipeUnit,editTipeUnit,updateTipeUnit,deleteTipeUnit,rap,addRap,insertRap,editRap,updateRap,deleteRap,laporanbarang,laporanprogresunit,ubk,addUbk,insertUbk,editUbk,updateUbk,deleteUbk,laporanbarang_harga,pengajuanubk,addPengajuanUbk,insertPengajuanUbk,editPengajuanUbk,updatePengajuanUbk,deletePengajuanUbk,approvalPengajuanUbk,ubkkeluar,addUbkKeluar,insertUbkKeluar,editUbkKeluar,updateUbkKeluar,deleteUbkKeluar,ledger,addLedger,insertLedger,editLedger,updateLedger,deleteLedger,contract,addContract,insertContract,editContract,updateContract,deleteContract,hutangsuplier,customer,addCustomer,insertCustomer,editCustomer,updateCustomer,deleteCustomer,penjualanunit,addPenjualan,insertPenjualan,editPenjualan,updatePenjualan,deletePenjualan,kpr,addKpr,insertKpr,editKpr,updateKpr,deleteKpr,assets,addAsset,insertAsset,editAsset,updateAsset,deleteAsset,neracaSaldo,bukubesar,labarugi,laporanneraca,laporanubk,kaskecil,addKaskecil,insertKaskecil,editKaskecil,updateKaskecil,deleteKaskecil',
				'created_at'		=> date('Y-m-d H:i:s'),
				'updated_at'		=> date('Y-m-d H:i:s')
			]
		]);
	}
}
