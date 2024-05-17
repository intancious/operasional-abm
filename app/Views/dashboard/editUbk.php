<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$unitModel = new \App\Models\UnitModel();
$tukangModel = new \App\Models\TukangModel();
$upahModel = new \App\Models\UpahModel();
$trxupahModel = new \App\Models\TrxupahModel();
$trxupahBayarModel = new \App\Models\TrxupahBayarModel();
$rekeningModel = new \App\Models\KoderekeningModel();
$kkModel = new \App\Models\KasKecilModel();

$pembayaran = $trxupahBayarModel->where(['tub_trxupah' => $trxupah['tu_id']])->findAll();
$totalBayar = 0;
foreach ($pembayaran as $row) {
    $bayar = $row['tub_bayar'] ? str_replace(',', '.', $row['tub_bayar']) : 0;
    $totalBayar += $bayar;
}

$kasKecil = $kkModel->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
    ->where(['kas_kecil.kk_status' => 1, 'kas_kecil.kk_jenis' => 4])
    ->select('kas_kecil.*, user.usr_nama')
    ->orderBy('kas_kecil.kk_tanggal', 'DESC')->findAll();

$tukang = $tukangModel->orderBy('tk_nama', 'ASC')->findAll();
$upah = $upahModel->join('satuan', 'satuan.satuan_id = upah.up_satuan', 'left')
    ->join('kategori_barang', 'kategori_barang.kabar_id = upah.up_kategori', 'left')
    ->select('upah.*, satuan.satuan_nama, kategori_barang.kabar_nama')
    ->orderBy('upah.up_nama', 'ASC')->findAll();
$unit = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
    ->select('unit.*, types.type_nama')
    ->orderBy('unit.unit_nama', 'ASC')->findAll();
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
                <form action="/dashboard/savefakturubk" method="post">
                    <input type="hidden" name="id" id="id" value="<?= $trxupah['tu_id']; ?>">
                    <table class="table table-bordered small">
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">TGL. TRANSAKSI</td>
                            <td>
                                <input type="text" name="tanggal" id="tanggalTrx" value="<?= date('d-m-Y', strtotime($trxupah['tu_tanggal'])); ?>" class="form-control form-control-sm">
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">NO. FAKTUR</td>
                            <td>
                                <input readonly type="text" name="faktur" id="faktur" value="<?= $trxupah['tu_nomor']; ?>" class="form-control form-control-sm">
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">TUKANG</td>
                            <td>
                                <select required name="tukang" id="tukang" class="form-control form-control-sm selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php foreach ($tukang as $row) { ?>
                                        <option value="<?= $row['tk_id']; ?>" <?= $trxupah['tu_tukang'] == $row['tk_id'] ? 'selected' : ''; ?> data-tokens="<?= $row['tk_nama']; ?>"><?= strtoupper($row['tk_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">CATATAN</td>
                            <td>
                                <textarea name="catatan" id="catatan" rows="2" class="form-control form-control-sm"><?= $trxupah['tu_keterangan']; ?></textarea>
                            </td>
                        </tr>
                    </table>
                    <div class="form-group mt-4 mb-0 float-right">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-sm fa-save mr-1"></i> UPDATE</button>
                    </div>
                </form>
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
                                        <option value="<?= $row['rek_id']; ?>" <?= $trxupah['tu_debet'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select name="kredit" id="kredit" class="form-control form-control-sm <?= $validation->hasError('kredit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" <?= $trxupah['tu_kredit'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
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
        DATA UPAH BARU
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
                                    <button class="btn btn-primary btn-sm updateUbk float-right" type="button">
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

<div class="d-sm-flex align-items-center justify-content-between my-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        DATA UPAH SAAT INI
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
                        <tbody>
                            <?php
                            $grandTotal = 0;
                            if ($trxupahItem) {
                                $i = 1;
                                foreach ($trxupahItem as $row) {
                                    $grandTotal += str_replace(',', '.', $row['tui_total']);
                            ?>
                                    <tr class="text-uppercase">
                                        <td><?= $i++; ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tui_tanggal'])); ?></td>
                                        <td><?= $row['unit_nama']; ?></td>
                                        <td><?= $row['up_nama']; ?></td>
                                        <td><?= $row['tui_jumlah']; ?></td>
                                        <td><?= number_format(str_replace(',', '.', $row['tui_nilai']), 2, ',', '.'); ?></td>
                                        <td><?= number_format(str_replace(',', '.', $row['tui_total']), 2, ',', '.'); ?></td>
                                        <td>
                                            <form class="d-inline ml-1" action="/dashboard/deleteItemUbk" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['tui_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="8" class="text-center font-italic">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="6" class="font-weight-bold text-right">GRAND TOTAL</td>
                                <td><?= number_format($grandTotal, 2, ',', '.') ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-sm-flex align-items-center justify-content-between my-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        TRANSAKSI KAS BON ATAU LEMBUR
    </h1>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggalLain">TANGGAL</label>
                            <input type="text" name="tanggalLain" id="tanggalLain" value="<?= date('d-m-Y'); ?>" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jenistrx">JENIS TRANSAKSI</label>
                            <select required name="jenistrx" id="jenistrx" class="form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <option value="1" data-tokens="KAS BON">KAS BON</option>
                                <option value="2" data-tokens="LEMBUR">LEMBUR</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nominalLain">NOMINAL</label>
                            <input type="text" name="nominalLain" id="nominalLain" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-md-6" id="showKaskecil">
                        <div id="#kaskecilbon">
                            <div class="form-group">
                                <label for="kaskecil">KAS KECIL KAS BON</label>
                                <select name="kaskecil" id="kaskecil" class="form-control form-control-sm selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($kasKecil as $row) { ?>
                                        <option value="<?= $row['kk_id']; ?>" data-tokens="(<?= $row['kk_nomor']; ?>) <?= strtoupper($row['usr_nama']); ?>">(<?= $row['kk_nomor']; ?>) <?= strtoupper($row['usr_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="debetLain">DEBET</label>
                            <select name="debetLain" id="debetLain" class="form-control form-control-sm <?= $validation->hasError('debetLain') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php
                                foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                    <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kreditLain">KREDIT</label>
                            <select name="kreditLain" id="kreditLain" class="form-control form-control-sm <?= $validation->hasError('kreditLain') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php
                                foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                    <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md">
                        <div class="form-group">
                            <label for="keteranganLain">KETERANGAN</label>
                            <textarea name="keteranganLain" id="keteranganLain" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group float-right">
                    <button type="button" class="btn btn-primary btn-sm addCartUpahLain"><i class="fas fa-sm fa-plus-circle mr-1"></i> TAMBAH</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-sm-flex align-items-center justify-content-between my-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        DATA KAS BON DAN UPAH BARU
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
                                <th>TRANSAKSI</th>
                                <th>NOMINAL</th>
                                <th>KAS KECIL</th>
                                <th>DEBET</th>
                                <th>KREDIT</th>
                                <th>KETERANGAN</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody id="cartListLain">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <button class="btn btn-secondary btn-sm clearItemUpahLain" type="button">
                                        <i class="fas fa-sm fa-broom fa-sm"></i> BERSIHKAN
                                    </button>
                                </td>
                                <td colspan="4">
                                    <button class="btn btn-primary btn-sm inserTrxupahLain float-right" type="button">
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

<div class="d-sm-flex align-items-center justify-content-between my-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        DATA KAS BON DAN UPAH SAAT INI
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
                                <th>TRANSAKSI</th>
                                <th>NOMINAL</th>
                                <th>KAS KECIL</th>
                                <th>DEBET</th>
                                <th>KREDIT</th>
                                <th>KETERANGAN</th>
                                <th>PRINT</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalLembur = 0;
                            $totalKasbon = 0;
                            if ($trxupahLain) {
                                $kasKecilModel = new \App\Models\KasKecilModel();
                                $koderekeningModel = new \App\Models\KoderekeningModel();

                                $i = 1;
                                foreach ($trxupahLain as $row) {
                                    if ($row['tul_jenis'] == 1) {
                                        $totalKasbon += str_replace(',', '.', $row['tul_nominal']);
                                    }
                                    if ($row['tul_jenis'] == 2) {
                                        $totalLembur += str_replace(',', '.', $row['tul_nominal']);
                                    }

                                    $kasKecilbon = $kasKecilModel->where('kk_id', $row['tul_kaskecil'])
                                        ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
                                        ->select('kas_kecil.*, user.usr_nama')->first();
                                    $rekeningDebet = $koderekeningModel->find($row['tul_debet']);
                                    $rekeningKredit = $koderekeningModel->find($row['tul_kredit']);
                            ?>
                                    <tr class="text-uppercase">
                                        <td><?= $i++; ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tul_tanggal'])); ?></td>
                                        <td><?= $row['tul_jenis'] == 1 ? 'KAS BON' : ($row['tul_jenis'] == 2 ? 'LEMBUR' : ''); ?></td>
                                        <td><?= number_format(str_replace(',', '.', $row['tul_nominal']), 2, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($kasKecilbon) { ?>
                                                (<?= $kasKecilbon['kk_nomor']; ?>) <?= $kasKecilbon['usr_nama']; ?>
                                            <?php } else { ?>
                                                -
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($rekeningDebet) { ?>
                                                (<?= $rekeningDebet['rek_kode']; ?>) <?= $rekeningDebet['rek_nama']; ?>
                                            <?php } else {
                                                echo '-';
                                            } ?>
                                        </td>
                                        <td>
                                            <?php if ($rekeningKredit) { ?>
                                                (<?= $rekeningKredit['rek_kode']; ?>) <?= $rekeningKredit['rek_nama']; ?>
                                            <?php } else {
                                                echo '-';
                                            } ?>
                                        </td>
                                        <td><?= $row['tul_keterangan'] ? $row['tul_keterangan'] : '-'; ?></td>
                                        <td>
                                            <?php if ($row['tul_jenis'] == 1) { ?>
                                                <a href="/dashboard/printkasbon/<?= $row['tul_id']; ?>" target="_blank"><i class="fas fa-print"></i></a>
                                            <?php } else {
                                                echo '-';
                                            } ?>
                                        </td>
                                        <td>
                                            <form class="d-inline ml-1" action="/dashboard/deleteItemUpahLain" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['tul_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="10" class="text-center font-italic">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="3" class="font-weight-bold" align="right">TOTAL KAS BON</td>
                                <td><?= number_format($totalKasbon, 2, ',', '.') ?></td>
                                <td colspan="10"></td>
                            </tr>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="3" class="font-weight-bold" align="right">TOTAL LEMBUR</td>
                                <td><?= number_format($totalLembur, 2, ',', '.') ?></td>
                                <td colspan="10"></td>
                            </tr>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="3" class="font-weight-bold" align="right">TOTAL UPAH</td>
                                <td><?= number_format(($grandTotal + $totalLembur) - $totalKasbon, 2, ',', '.') ?></td>
                                <td colspan="10"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>