<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$barangModel = new \App\Models\BarangModel();
$kategoriModel = new \App\Models\KabarModel();
$satuanModel = new \App\Models\SatuanModel();
$kasKecilModel = new \App\Models\KasKecilModel();
$supplierModel = new \App\Models\SuplierModel();
$pembelianModel = new \App\Models\PembelianModel();
$rekeningModel = new \App\Models\KoderekeningModel();

$kasKecil = $kasKecilModel->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
    ->where(['kas_kecil.kk_status' => 1, 'kas_kecil.kk_jenis' => 1])
    ->select('kas_kecil.*, user.usr_nama')
    ->orderBy('kas_kecil.kk_tanggal', 'DESC')->findAll();
$categories = $kategoriModel->orderBy('kabar_nama', 'ASC')->findAll();
$satuan = $satuanModel->orderBy('satuan_nama', 'ASC')->findAll();
$suppliers = $supplierModel->orderBy('suplier_nama', 'ASC')->findAll();
$barang = $barangModel->orderBy('barang_nama', 'ASC')->findAll();
$noFaktur = $pembelianModel->buatNoFaktur();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="/dashboard/pembelian"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<?= session()->get('pesan'); ?>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered small">
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">TGL. TRANSAKSI</td>
                        <td>
                            <input type="text" name="tanggal" id="tanggalTrx" value="<?= date('d-m-Y'); ?>" class="form-control form-control-sm">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">NO. FAKTUR</td>
                        <td>
                            <input readonly type="text" name="faktur" id="faktur" value="<?= $noFaktur; ?>" class="form-control form-control-sm">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">CATATAN</td>
                        <td>
                            <textarea name="catatan" id="catatan" rows="2" class="form-control form-control-sm"></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered small">
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">BARANG</td>
                        <td>
                            <select required name="barang" id="barang" class="form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php foreach ($barang as $row) { ?>
                                    <option value="<?= $row['barang_id']; ?>" data-tokens="<?= $row['barang_nama']; ?>" <?= old('barang') == $row['barang_id'] ? 'selected' : ''; ?>><?= strtoupper($row['barang_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">NAMA BARANG</td>
                        <td>
                            <div id="namaBarang" class="text-uppercase">-</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">KATEGORI / SATUAN</td>
                        <td>
                            <div id="kategoriSatuanBarang" class="text-uppercase">-</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">QUANTITY</td>
                        <td>
                            <input type="text" name="qty" id="qtyP" class="form-control form-control-sm" value="0">
                        </td>
                    </tr>
                </table>
                <div class="form-group mt-4 mb-0">
                    <button type="button" class="btn btn-primary btn-sm addCartPembelian"><i class="fas fa-sm fa-plus-circle mr-1"></i> TAMBAH</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-sm-flex align-items-center justify-content-between my-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        DATA BARANG
    </h1>
</div>
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped small" id="dataTable" style="width:100%">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <th>#</th>
                                <th>NAMA BARANG</th>
                                <th>QTY</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody id="cartListPembelian">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">
                                    <button class="btn btn-secondary btn-sm clearItem" type="button">
                                        <i class="fas fa-sm fa-broom fa-sm"></i> BERSIHKAN
                                    </button>
                                </td>
                                <td colspan="2">
                                    <button class="btn btn-primary btn-sm insertPembelian float-right" type="button">
                                        <i class="fas fa-sm fa-save fa-sm"></i> SIMPAN
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qtyHargaModal" tabindex="-1" role="dialog" aria-labelledby="qtyHargaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-uppercase" id="qtyHargaModalLabel">Perbarui</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="itemId" id="itemId">
                <div class="form-group">
                    <label for="newQty">Quantity</label>
                    <input required type="text" name="newQty" id="newQty" class="form-control">
                </div>
                <div class="form-group">
                    <label for="newHarga">Harga</label>
                    <input type="text" name="newHarga" id="newHarga" class="form-control" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary updateQtyHarga">Simpan</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>