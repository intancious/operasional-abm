<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$userModel = new \App\Models\UserModel();
$rekeningModel = new \App\Models\KoderekeningModel();
$rekening = $rekeningModel->orderBy('rek_id', 'ASC')->findAll();

$piutangpenjualanModel = new \App\Models\PiutangpenjualanModel();
$bpModel = new \App\Models\BiayapenjualanModel();

$piutang = $piutangpenjualanModel->where('pc_penjualan', $penjualan['pu_nomor'])->orderBy('pc_id', 'DESC')->findAll();
$biayaLain = $bpModel->where('bp_penjualan', $penjualan['pu_nomor'])->orderBy('bp_id', 'DESC')->findAll();

$kprModel = new \App\Models\KprModel();
$kpr = $kprModel->orderBy('kpr_nama', 'ASC')->findAll();

$totalBayar = 0;
$totalBayarKpr = 0;
foreach ($piutang as $utang) {
    $totalBayar += $utang['pc_bayar'];
    $totalBayarKpr += $utang['pc_bayarKpr'];
}

$piutangCustomer = ($penjualan['pu_harga'] - $penjualan['pu_nominalKpr']) - $totalBayar;
$piutangKpr = $penjualan['pu_nominalKpr'] - $totalBayarKpr;

$bpModel = new \App\Models\BiayapenjualanModel();
$biayaLain = $bpModel->where('bp_penjualan', $penjualan['pu_nomor'])->orderBy('bp_id', 'DESC')->findAll();
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="javascript: history.go(-1)"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-primary">
        <h6 class="m-0 font-weight-bold text-uppercase text-white">
            PERBARUI TRANSAKSI PEMBAYARAN
        </h6>
    </div>
    <div class="card-body">
        <?= session()->get('pesan'); ?>
        <input type="hidden" name="puId" id="puId" value="<?= $penjualan['pu_id'] ?>">
        <input type="hidden" name="pjId" id="pjId" value="<?= $piutangData['pc_id'] ?>">
        <div class="row my-3 small">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="jenisSelect"><b>JENIS PENJUALAN</b></label>
                    <select disabled name="jenis" id="jenisSelect" class="form-control selectpicker" data-live-search="true">
                        <option value="" data-tokens="">:: PILIH ::</option>
                        <option value="cash" data-tokens="cash" <?= $penjualan['pu_jenis'] == "cash" ? 'selected' : ''; ?>>CASH</option>
                        <option value="kpr" data-tokens="kpr" <?= $penjualan['pu_jenis'] == "kpr" ? 'selected' : ''; ?>>KPR</option>
                        <option value="kredit" data-tokens="kredit" <?= $penjualan['pu_jenis'] == "kredit" ? 'selected' : ''; ?>>KREDIT</option>
                    </select>
                    <div class="invalid-feedback">
                        <?= $validation->getError('jenis'); ?>
                    </div>
                </div>
                <table class="table table-hover table-bordered">
                    <tr class="bg-light">
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">No. Order</td>
                        <td>
                            <input required readonly type="text" name="nomor" id="nomor" class="form-control" value="<?= $penjualan['pu_nomor'] ?>">
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">Tgl. Transaksi</td>
                        <td>
                            <input disabled required type="text" name="tanggal" id="tanggalPicker" class="form-control datepicker" value="<?= date('d-m-Y', strtotime($penjualan['created_at'])); ?>">
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">Pilih Customer</td>
                        <td>
                            <select disabled required name="customer" id="customerselect" class="form-control selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php
                                foreach ($customers as $row) {
                                ?>
                                    <option value="<?= $row['cust_id']; ?>" data-tokens="<?= $row['cust_nama']; ?>" <?= $penjualan['pu_cust'] == $row['cust_id'] ? 'selected' : ''; ?>><?= strtoupper($row['cust_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">Nama</td>
                        <td>
                            <span id="namaCustomer" class="text-uppercase"></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">Alamat</td>
                        <td>
                            <span id="alamatCustomer" class="text-uppercase"></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">No. Telp</td>
                        <td>
                            <span id="telpCustomer" class="text-uppercase"></span>
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">Pilih Unit</td>
                        <td>
                            <select disabled required name="unit" id="unitselect" class="form-control selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php
                                foreach ($units as $row) {
                                ?>
                                    <option value="<?= $row['unit_id']; ?>" data-tokens="<?= $row['unit_nama']; ?> (<?= $row['type_nama']; ?>)" <?= $penjualan['pu_unit'] == $row['unit_id'] ? 'selected' : ''; ?>><?= strtoupper($row['unit_nama']); ?> (<?= $row['type_nama']; ?>)</option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-7">
                <table class="table table-hover table-bordered">
                    <tr class="bg-light">
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">TANGGAL BAYAR</td>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <input required type="text" name="tanggalBayar" id="tanggalBayarPicker" class="form-control datepicker" value="<?= date('d-m-Y', strtotime($piutangData['created_at'])); ?>">
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="harga">HARGA TRANSAKSI</label>
                                <input readonly required type="text" name="harga" id="harga" class="form-control" value="<?= $penjualan['pu_harga']; ?>">
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="piutangCustomer">SISA PIUTANG CUSTOMER</label>
                                <input readonly required type="text" name="piutangCustomer" id="piutangCustomer" class="form-control" value="<?= $piutangCustomer; ?>">
                            </div>
                        </td>
                    </tr>
                </table>

                <div id="showKpr" class="mb-3 mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group font-weight-bold">
                                <label for="nominalKpr">KPR <?= $kprModel->find($penjualan['pu_kpr'])['kpr_nama']; ?></label>
                                <input readonly type="text" name="nominalKpr" id="nominalKpr" class="form-control" value="<?= $penjualan['pu_nominalKpr']; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group font-weight-bold">
                                <label for="piutangKpr">SISA PIUTANG KPR</label>
                                <input readonly type="text" name="piutangKpr" id="piutangKpr" class="form-control" value="<?= $piutangKpr; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md">
                            <div class="form-group">
                                <label for="bayarKpr">BAYAR KPR</label>
                                <input required type="text" name="bayarKpr" id="bayarKpr" class="form-control" value="<?= $piutangData['pc_bayarKpr']; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="debetBayarKpr"><b>DEBET</b></label>
                                <select name="debetBayarKpr" id="debetBayarKpr" class="form-control selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekening as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>" <?= $piutangData['pc_bayarKprDebet'] == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('debetBayarKpr'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kreditBayarKpr"><b>KREDIT</b></label>
                                <select name="kreditBayarKpr" id="kreditBayarKpr" class="form-control selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekening as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>" <?= $piutangData['pc_bayarKprKredit'] == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('kreditBayarKpr'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-hover table-bordered">
                    <tr class="bg-light">
                        <td colspan="4" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="bayarCustomer">BAYAR CUSTOMER</label>
                                <input required type="text" name="bayarCustomer" id="bayarCustomer" class="form-control" value="<?= $piutangData['pc_bayar']; ?>">
                            </div>
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td colspan="2">
                            <div class="form-group">
                                <label for="debetBayarCustomer"><b>DEBET</b></label>
                                <select name="debetBayarCustomer" id="debetBayarCustomer" class="form-control selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekening as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>" <?= $piutangData['pc_bayarDebet'] == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('debetBayarCustomer'); ?>
                                </div>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="kreditBayarCustomer"><b>KREDIT</b></label>
                                <select name="kreditBayarCustomer" id="kreditBayarCustomer" class="form-control selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekening as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>" <?= $piutangData['pc_bayarKredit'] == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('kreditBayarCustomer'); ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="bg-light">
                        <td colspan="4">
                            <div class="form-group font-weight-bold">
                                <label for="catatan">CATATAN</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="2"><?= $piutangData['pc_keterangan']; ?></textarea>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- <div class="card shadow mt-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-uppercase">
            BIAYA LAIN-LAIN
        </h6>
    </div>
    <div class="card-body">
        <div class="row mt-3 small">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="uraian_lain"><b>URAIAN</b></label>
                    <input type="text" name="uraian_lain" id="uraian_lain" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nominal_lain"><b>NOMINAL</b></label>
                    <input type="text" name="nominal_lain" id="nominal_lain" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="debet_lain"><b>DEBET</b></label>
                    <select name="debet_lain" id="debet_lain" class="form-control selectpicker" data-live-search="true">
                        <option value="" data-tokens="">:: PILIH ::</option>
                        <?php
                        foreach ($rekening as $row) { ?>
                            <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>" <?= old('debet_lain') == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                        <?php } ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= $validation->getError('debet_lain'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="kredit_lain"><b>KREDIT</b></label>
                    <select name="kredit_lain" id="kredit_lain" class="form-control selectpicker" data-live-search="true">
                        <option value="" data-tokens="">:: PILIH ::</option>
                        <?php
                        foreach ($rekening as $row) { ?>
                            <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>" <?= old('kredit_lain') == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                        <?php } ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= $validation->getError('kredit_lain'); ?>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary btn-sm addBiayaLain">TAMBAH</button>
    </div>
</div>

<div class="card shadow my-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-uppercase">
            BIAYA LAIN-LAIN (BARU)
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                <thead class="bg-dark text-white">
                    <tr>
                        <td>URAIAN</td>
                        <td>NOMINAL</td>
                        <td>DEBET</td>
                        <td>KREDIT</td>
                        <td>HAPUS</td>
                    </tr>
                </thead>
                <tbody id="biayaLainList">
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow my-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-uppercase">
            BIAYA LAIN-LAIN
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>URAIAN</th>
                        <th>NOMINAL</th>
                        <th>DEBET</th>
                        <th>KREDIT</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grandTotal = 0;
                    if ($biayaLain) {
                        foreach ($biayaLain as $row) {
                            $biayaDebet = $rekeningModel->find($row['bp_debet']);
                            $biayaKredit = $rekeningModel->find($row['bp_kredit']);
                            $grandTotal += $row['bp_nominal'];
                    ?>
                            <tr>
                                <td class="text-uppercase"><?= $row['bp_uraian']; ?></td>
                                <td align="right"><?= number_format($row['bp_nominal'], 0, ".", "."); ?></td>
                                <td class="text-uppercase"><?= $row['bp_debet'] ? '(' . $biayaDebet['rek_kode'] . ') ' . $biayaDebet['rek_nama'] : '-'; ?></td>
                                <td class="text-uppercase"><?= $row['bp_kredit'] ? '(' . $biayaKredit['rek_kode'] . ') ' . $biayaKredit['rek_nama'] : '-'; ?></td>
                                <td>
                                    <form class="d-inline" action="/dashboard/deleteItemBiayaLain" method="post">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $row['bp_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="5" class="text-center font-italic">Data belum tersedia.</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="font-weight-bold">TOTAL</td>
                        <td class="font-weight-bold" align="right"><?= number_format($grandTotal, 0, ".", "."); ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div> -->

<div class="row mb-5 mt-3">
    <div class="col-md">
        <button type="submit" class="btn btn-primary btn-sm float-right" onclick="updateBayarPiutangPenjualan()">SIMPAN</button>
    </div>
</div>
<?= $this->endSection(); ?>