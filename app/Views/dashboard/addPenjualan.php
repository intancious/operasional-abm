<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$userModel = new \App\Models\UserModel();
$rekeningModel = new \App\Models\KoderekeningModel();
$rekening = $rekeningModel->orderBy('rek_id', 'ASC')->findAll();

$kprModel = new \App\Models\KprModel();
$kpr = $kprModel->orderBy('kpr_nama', 'ASC')->findAll();
$penjualanunitModel = new \App\Models\PenjualanunitModel();
$pj = $penjualanunitModel->findAll();
foreach ($pj as $row) {
    if ($row['pu_status'] == 1) {
        $allUnits[] = $row['pu_unit'];
    }
}
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/penjualanunit"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<?= session()->get('pesan'); ?>

<div class="card shadow mb-4">
    <div class="card-header bg-primary">
        <h6 class="m-0 font-weight-bold text-uppercase text-white">
            TRANSAKSI PENJUALAN
        </h6>
    </div>
    <div class="card-body">
        <div class="row my-3 small">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="jenisSelect"><b>JENIS PENJUALAN</b></label>
                    <select name="jenis" id="jenisSelect" class="form-control selectpicker" data-live-search="true">
                        <option value="" data-tokens="">:: PILIH ::</option>
                        <option value="cash" data-tokens="cash" <?= old('jenis') == "cash" ? 'selected' : ''; ?>>CASH</option>
                        <option value="kpr" data-tokens="kpr" <?= old('jenis') == "kpr" ? 'selected' : ''; ?>>KPR</option>
                        <option value="kredit" data-tokens="kredit" <?= old('jenis') == "kredit" ? 'selected' : ''; ?>>IN HOUSE</option>
                    </select>
                    <div class="invalid-feedback">
                        <?= $validation->getError('jenis'); ?>
                    </div>
                </div>

                <table class="table table-hover table-bordered">
                    <tr>
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">No. Order</td>
                        <td>
                            <?php
                            $valNumb = $penjualanunitModel->countAll() + 1;
                            $nomor = str_pad($valNumb, 4, "0", STR_PAD_LEFT) . '-PJU/' . date('d/m') . '/' . substr(date('Y'), -2);

                            $checkIsExist = $penjualanunitModel->where('pu_nomor', $nomor)->first();
                            if ($checkIsExist) {
                                $lastRow = $penjualanunitModel->orderBy('pu_id', 'DESC')->first();
                                $exNumb = explode('-', $lastRow['pu_nomor']);
                                $newNumb = intval($exNumb[0]) + 1;
                                $fixNumber = str_pad($newNumb, 4, "0", STR_PAD_LEFT) . '-PJU/' . date('d/m') . '/' . substr(date('Y'), -2);
                            } else {
                                $fixNumber = $nomor;
                            }
                            ?>
                            <input required readonly type="text" name="nomor" id="nomor" class="form-control" value="<?= $fixNumber ?>">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">Tanggal</td>
                        <td>
                            <input required type="text" name="tanggal" id="tanggalPicker" class="form-control datepicker" value="<?= date('d-m-Y'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">Pilih Marketing</td>
                        <td>
                            <select required name="marketing" id="marketing" class="form-control selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php
                                $marketingModel = new \App\Models\MarketingModel();
                                $marketing = $marketingModel->orderBy('m_nama', 'ASC')->findAll();
                                foreach ($marketing as $row) {
                                ?>
                                    <option value="<?= $row['m_id']; ?>" data-tokens="<?= $row['m_nama']; ?>" <?= old('marketing') == $row['m_id'] ? 'selected' : ''; ?>><?= strtoupper($row['m_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">Pilih Customer</td>
                        <td>
                            <select required name="customer" id="customerselect" class="form-control selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php
                                foreach ($customers as $row) {
                                ?>
                                    <option value="<?= $row['cust_id']; ?>" data-tokens="<?= $row['cust_nama']; ?>" <?= old('customer') == $row['cust_id'] ? 'selected' : ''; ?>><?= strtoupper($row['cust_nama']); ?></option>
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
                    <tr>
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">Pilih Unit</td>
                        <td>
                            <select required name="unit" id="unitselect" class="form-control selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php
                                foreach ($units as $row) {
                                    if (isset($allUnits)) {
                                        if (!in_array($row['unit_id'], $allUnits)) {
                                ?>
                                            <option value="<?= $row['unit_id']; ?>" data-tokens="<?= $row['unit_nama']; ?> (<?= $row['type_nama']; ?>)" <?= old('user') == $row['unit_id'] ? 'selected' : ''; ?>><?= strtoupper($row['unit_nama']); ?> (<?= $row['type_nama']; ?>)</option>
                                        <?php }
                                    } else { ?>
                                        <option value="<?= $row['unit_id']; ?>" data-tokens="<?= $row['unit_nama']; ?> (<?= $row['type_nama']; ?>)" <?= old('user') == $row['unit_id'] ? 'selected' : ''; ?>><?= strtoupper($row['unit_nama']); ?> (<?= $row['type_nama']; ?>)</option>
                                <?php }
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle;" class="font-weight-bold text-uppercase">HPP</td>
                        <td>
                            <span id="hpp" class="text-uppercase"></span>
                        </td>
                    </tr>
                </table>

                <table class="table table-hover table-bordered mt-3">
                    <tr>
                        <td colspan="4">
                            <div class="form-group">
                                <label for="hargarill">HARGA POKOK</label>
                                <input type="text" name="hargariil" id="hargariil" class="form-control">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="nnup">NUP</label>
                                <input type="text" name="nup" id="nnup" class="form-control">
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="mutu">PENINGKATAN MUTU</label>
                                <input type="text" name="mutu" id="mutu" class="form-control">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="tanahLebih">KELEBIHAN TANAH</label>
                                <input type="text" name="tanahLebih" id="tanahLebih" class="form-control">
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="sbum">SBUM</label>
                                <input type="text" name="sbum" id="sbum" class="form-control">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="ttlharga">TOTAL HARGA</label>
                                <input readonly type="text" name="ttlharga" id="ttlharga" class="form-control">
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="table table-hover table-bordered mt-3">
                    <tr>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="ajbn">AJB-BN</label>
                                <input type="text" name="ajbn" id="ajbn" class="form-control">
                            </div>
                        </td>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="pph">PPH</label>
                                <input type="text" name="pph" id="pph" class="form-control">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="bphtb">BPHTB</label>
                                <input type="text" name="bphtb" id="bphtb" class="form-control">
                            </div>
                        </td>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="realisasi">BIAYA REALISASI</label>
                                <input type="text" name="realisasi" id="realisasi" class="form-control">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-hover table-bordered mt-3">
                    <tr>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="shm">SHM</label>
                                <input type="text" name="shm" id="shm" class="form-control">
                            </div>
                        </td>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="kanopi">Kanopi</label>
                                <input type="text" name="kanopi" id="kanopi" class="form-control">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="tandon">Tandon</label>
                                <input type="text" name="tandon" id="tandon" class="form-control">
                            </div>
                        </td>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="pompair">Pompa Air</label>
                                <input type="text" name="pompair" id="pompair" class="form-control">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="teralis">Teralis</label>
                                <input type="text" name="teralis" id="teralis" class="form-control">
                            </div>
                        </td>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="tembok">Tembok Keliling</label>
                                <input type="text" name="tembok" id="tembok" class="form-control">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="pondasi">Pondasi</label>
                                <input type="text" name="pondasi" id="pondasi" class="form-control">
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="table table-hover table-bordered mt-3">
                    <tr>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="pijb">PIJB</label>
                                <input type="text" name="pijb" id="pijb" class="form-control">
                            </div>
                        </td>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="ppn">PPN</label>
                                <input type="text" name="ppn" id="ppn" class="form-control">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="fee">Fee & Komisi Penjualan</label>
                                <input type="text" name="fee" id="fee" class="form-control">
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="table table-hover table-bordered mt-3">
                    <tr>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="harga">HARGA TRANSAKSI</label>
                                <input readonly type="text" name="harga" id="harga" class="form-control">
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="kredit_harga"><b>KREDIT</b></label>
                                <select required name="kredit_harga" id="kredit_harga" class="form-control selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekening as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>" <?= old('kredit_harga') == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('kredit_harga'); ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="bayar">UANG MUKA/DP</label>
                                <input type="text" name="bayar" id="bayar" class="form-control">
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="debet_bayar"><b>DEBET</b></label>
                                <select required name="debet_bayar" id="debet_bayar" class="form-control selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekening as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>" <?= old('debet_bayar') == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('debet_bayar'); ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <div id="showKpr" class="mb-3 mt-4">
                    <div class="row">
                        <div class="col-md">
                            <div class="form-group">
                                <label for="kpr"><b>PILIH KPR</b></label>
                                <select name="kpr" id="kpr" class="form-control selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($kpr as $row) { ?>
                                        <option value="<?= $row['kpr_id']; ?>" data-tokens="<?= strtoupper($row['kpr_nama']); ?>" <?= old('kpr') == $row['kpr_id'] ? 'selected' : ''; ?>><?= strtoupper($row['kpr_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tglPengajuanKpr" class="font-weight-bold text-uppercase">TGL. PENGAJUAN</label>
                                <input type="text" name="tglPengajuanKpr" id="tglPengajuanKpr" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nilaiPengajuanKpr">NILAI PENGAJUAN</label>
                                <input type="text" name="nilaiPengajuanKpr" id="nilaiPengajuanKpr" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tglAccKpr" class="font-weight-bold text-uppercase">TGL. ACC</label>
                                <input type="text" name="tglAccKpr" id="tglAccKpr" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nilaiAccKpr">NILAI ACC</label>
                                <input type="text" name="nilaiAccKpr" id="nilaiAccKpr" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tglRealisasiKpr" class="font-weight-bold text-uppercase">TGL. REALISASI</label>
                                <input type="text" name="tglRealisasiKpr" id="tglRealisasiKpr" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="form-group">
                                <label for="debetKpr"><b>DEBET</b></label>
                                <select name="debetKpr" id="debetKpr" class="form-control selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekening as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>" <?= old('debetKpr') == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('debetKpr'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div id="showAngsuran" class="mb-3 mt-4"> -->
                <table class="table table-hover table-bordered mt-4">
                    <tr>
                        <td colspan="2" style="vertical-align: middle;" class="font-weight-bold text-uppercase">
                            <div class="form-group">
                                <label for="sisaBayar">SISA PIUTANG CUSTOMER</label>
                                <input required readonly type="text" name="sisaBayar" id="sisaBayar" class="form-control">
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="form-group">
                                <label for="debet_sisa"><b>DEBET</b></label>
                                <select required name="debet_sisa" id="debet_sisa" class="form-control selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekening as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>" <?= old('debet_sisa') == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $validation->getError('debet_sisa'); ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="form-group">
                                <label for="kaliangsur">BERAPA KALI ANGSURAN?</label>
                                <input type="number" name="kaliangsur" id="kaliangsur" class="form-control">
                            </div>
                        </td>
                    </tr>
                </table>
                <!-- </div> -->

                <table class="table table-hover table-bordered mt-3">
                    <tr>
                        <td colspan="2">
                            <div class="form-group font-weight-bold">
                                <label for="catatan">KETERANGAN</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="2"></textarea>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-primary">
        <h6 class="m-0 font-weight-bold text-uppercase text-white">
            BIAYA LAIN-LAIN
        </h6>
    </div>
    <div class="card-body">
        <div class="row mt-3 small">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="tanggalNup"><b>TANGGAL</b></label>
                    <input required type="text" name="tanggalNup" id="tanggalNup" class="form-control datepicker" value="<?= date('d-m-Y'); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="biayalain"><b>BIAYA LAIN</b></label>
                    <select required name="biayalain" id="biayalain" class="form-control selectpicker" data-live-search="true">
                        <option value="" data-tokens="">:: PILIH ::</option>
                        <?php
                        $biayaLainModel = new \App\Models\BiayaLainModel();
                        $biayalain = $biayaLainModel->orderBy('bl_nama', 'ASC')->findAll();
                        foreach ($biayalain as $row) {
                        ?>
                            <option value="<?= $row['bl_id']; ?>" data-tokens="<?= $row['bl_nama']; ?>" <?= old('biayalain') == $row['bl_id'] ? 'selected' : ''; ?>><?= strtoupper($row['bl_nama']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mt-3 small">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="uraian_lain"><b>URAIAN / KETERANGAN</b></label>
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

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered small" id="customTable" style="width:100%">
                <thead class="thead-light font-weight-bold">
                    <tr>
                        <td>TANGGAL</td>
                        <td>BIAYA</td>
                        <td>URAIAN / KETERANGAN</td>
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

<div class="row mb-5 mt-3">
    <div class="col-md">
        <button type="submit" class="btn btn-primary btn-sm float-right" onclick="insertPenjualanUnit()">SIMPAN</button>
    </div>
</div>
<?= $this->endSection(); ?>