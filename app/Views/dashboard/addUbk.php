<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$unitModel = new \App\Models\UnitModel();
$tukangModel = new \App\Models\TukangModel();
$upahModel = new \App\Models\UpahModel();
$trxupahModel = new \App\Models\TrxupahModel();
$rekeningModel = new \App\Models\KoderekeningModel();

$tukang = $tukangModel->orderBy('tk_nama', 'ASC')->findAll();
$upah = $upahModel->join('satuan', 'satuan.satuan_id = upah.up_satuan', 'left')
    ->join('kategori_barang', 'kategori_barang.kabar_id = upah.up_kategori', 'left')
    ->select('upah.*, satuan.satuan_nama, kategori_barang.kabar_nama')
    ->orderBy('upah.up_nama', 'ASC')->findAll();
$unit = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
    ->select('unit.*, types.type_nama')
    ->orderBy('unit.unit_nama', 'ASC')->findAll();

$noFaktur = $trxupahModel->buatNoFaktur();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="/dashboard/ubk"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
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
                        <td style="vertical-align: middle; font-weight: bold;">TUKANG</td>
                        <td>
                            <select required name="tukang" id="tukang" class="form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php foreach ($tukang as $row) { ?>
                                    <option value="<?= $row['tk_id']; ?>" data-tokens="<?= $row['tk_nama']; ?>"><?= strtoupper($row['tk_nama']); ?></option>
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
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered small mb-4">
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">TGL. TRANSAKSI</td>
                        <td>
                            <input type="text" name="tanggalUpah" id="tanggalUpah" value="<?= date('d-m-Y'); ?>" class="form-control form-control-sm">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">UNIT</td>
                        <td>
                            <select required name="unitUpah" id="unitUpah" class="form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php foreach ($unit as $row) { ?>
                                    <option value="<?= $row['unit_id']; ?>" data-tokens="<?= $row['unit_nama']; ?>" <?= old('unitUpah') == $row['unit_id'] ? 'selected' : ''; ?>><?= strtoupper($row['unit_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">UPAH</td>
                        <td>
                            <select required name="ubk" id="ubk" class="form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php foreach ($upah as $row) { ?>
                                    <option value="<?= $row['up_id']; ?>" data-tokens="<?= $row['up_nama']; ?>" <?= old('ubk') == $row['up_id'] ? 'selected' : ''; ?>><?= strtoupper($row['up_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <table class="table table-bordered small">
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">JUMLAH</td>
                        <td>
                            <input type="text" name="qtyUpah" id="qtyUpah" class="form-control form-control-sm">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">NILAI UPAH</td>
                        <td>
                            <input readonly type="text" name="nilaiUbk" id="nilaiUbk" class="form-control form-control-sm">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">TOTAL</td>
                        <td>
                            <input readonly type="text" name="total" id="total" class="form-control form-control-sm" value="0">
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
                <div class="form-group mt-4 mb-0">
                    <button type="button" class="btn btn-primary btn-sm addCartUpah"><i class="fas fa-sm fa-plus-circle mr-1"></i> TAMBAH</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-sm-flex align-items-center justify-content-between my-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        DATA UPAH
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
                                <th>TANGGAL</th>
                                <th>UNIT</th>
                                <th>UPAH</th>
                                <th>JUMLAH</th>
                                <th>NILAI</th>
                                <th>TOTAL</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody id="cartListUpah">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <button class="btn btn-secondary btn-sm clearItem" type="button">
                                        <i class="fas fa-sm fa-broom fa-sm"></i> BERSIHKAN
                                    </button>
                                </td>
                                <td colspan="4">
                                    <button class="btn btn-primary btn-sm insertUbk float-right" type="button">
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
<?= $this->endSection(); ?>