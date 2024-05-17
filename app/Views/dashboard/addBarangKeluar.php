<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$barangModel = new \App\Models\BarangModel();
$kategoriModel = new \App\Models\KabarModel();
$satuanModel = new \App\Models\SatuanModel();
$unitModel = new \App\Models\UnitModel();
$barangKeluarModel = new \App\Models\BarangKeluarModel();
$rekeningModel = new \App\Models\KoderekeningModel();

$units = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
    ->orderBy('unit.unit_nama', 'ASC')
    ->select('unit.*, types.type_nama')
    ->findAll();
$categories = $kategoriModel->orderBy('kabar_nama', 'ASC')->findAll();
$satuan = $satuanModel->orderBy('satuan_nama', 'ASC')->findAll();
$barang = $barangModel->orderBy('barang_nama', 'ASC')->findAll();
$noFaktur = $barangKeluarModel->buatNoFaktur();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="/dashboard/barangkeluar"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
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
                        <td style="vertical-align: middle; font-weight: bold;">NOMOR</td>
                        <td>
                            <input readonly type="text" name="faktur" id="faktur" value="<?= $noFaktur; ?>" class="form-control form-control-sm">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">UNIT</td>
                        <td>
                            <select required name="unit" id="unit" class="form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php foreach ($units as $row) { ?>
                                    <option value="<?= $row['unit_id']; ?>" data-tokens="<?= $row['unit_nama']; ?> (<?= $row['type_nama']; ?>)"><?= strtoupper($row['unit_nama']); ?> (<?= $row['type_nama']; ?>)</option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">CATATAN</td>
                        <td>
                            <textarea name="catatan" id="catatan" rows="2" class="form-control form-control-sm"></textarea>
                        </td>
                    </tr>
                </table>
                <table class="table table-bordered small mt-4">
                    <thead class="thead-light font-weight-bold">
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">DEBET</td>
                            <td style="vertical-align: middle; font-weight: bold;">KREDIT</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="debet" id="debet" class="form-control form-control-sm <?= $validation->hasError('debet') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" <?= old('debet') == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select name="kredit" id="kredit" class="form-control form-control-sm <?= $validation->hasError('kredit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" <?= old('kredit') == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
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
                        <td style="vertical-align: middle; font-weight: bold;">STOK</td>
                        <td>
                            <div id="stokSaatIni" class="text-uppercase">0</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">VOLUME RAP</td>
                        <td>
                            <div id="volumeRap" class="text-uppercase">0</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">QUANTITY</td>
                        <td>
                            <input type="text" name="qty" id="qtyP" class="form-control form-control-sm" value="0">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">HARGA</td>
                        <td>
                            <input type="text" name="harga" id="harga" class="form-control form-control-sm" value="0">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">TOTAL</td>
                        <td>
                            <input readonly type="text" name="total" id="total" class="form-control form-control-sm" value="0">
                        </td>
                    </tr>
                </table>
                <div class="form-group mt-4 mb-0">
                    <button type="button" class="btn btn-primary btn-sm addCartKeluar"><i class="fas fa-sm fa-plus-circle mr-1"></i> TAMBAH</button>
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
                                <th>HARGA</th>
                                <th>SUBTOTAL</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody id="cartListKeluar">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qtyKeluarModal" tabindex="-1" role="dialog" aria-labelledby="qtyKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-uppercase" id="qtyKeluarModalLabel">Perbarui</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="outId" id="outId">
                <div class="form-group">
                    <label for="outQty">Quantity</label>
                    <input required type="text" name="outQty" id="outQty" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary updateQtyKeluar">Simpan</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>