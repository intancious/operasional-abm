<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$rekeningModel = new \App\Models\KoderekeningModel();
$rekening = $rekeningModel
    ->orderBy('rek_id', 'ASC')->findAll();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/ledger"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?= session()->get('pesan'); ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="tanggal">Tanggal Transaksi</label>
                    <input required type="text" name="tanggal" id="tanggalPicker" class="form-control datepicker" value="<?= date('d-m-Y'); ?>">
                    <div class="invalid-feedback">
                        <?= $validation->getError('tanggal'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nomor">Nomor Transaksi</label>
                    <input required type="text" name="nomor" id="nomor" class="form-control">
                    <div class="invalid-feedback">
                        <?= $validation->getError('nomor'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="uraian">Uraian</label>
                    <input type="text" name="uraian" id="uraian" class="form-control">
                    <div class="invalid-feedback">
                        <?= $validation->getError('uraian'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="ket_trx">Ket. Transaksi</label>
                    <input required type="text" name="ket_trx" id="ket_trx" class="form-control">
                    <div class="invalid-feedback">
                        <?= $validation->getError('ket_trx'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-3">
            <div class="card-body">
                <input type="hidden" name="jenis" id="jenis" value="debet">
                <div class="form-group">
                    <label for="debet">Pilih Rekening Debet</label>
                    <select name="debet" id="debet" class="form-control selectpicker" data-live-search="true">
                        <option value="" data-tokens="">:: PILIH ::</option>
                        <?php
                        foreach ($rekening as $row) { ?>
                            <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                        <?php } ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= $validation->getError('debet'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nominaldebet">Nominal Debet</label>
                    <input type="text" name="nominaldebet" id="nominaldebet" class="form-control">
                    <div class="invalid-feedback">
                        <?= $validation->getError('nominaldebet'); ?>
                    </div>
                </div>
                <button type="button" id="formAddLedger" class="btn btn-primary btn-sm formAddLedger">Tambah</button>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow mb-3">
            <div class="card-body">
                <input type="hidden" name="jenis" id="jenis" value="kredit">
                <div class="form-group">
                    <label for="kredit">Pilih Rekening Kredit</label>
                    <select name="kredit" id="kredit" class="form-control selectpicker" data-live-search="true">
                        <option value="" data-tokens="">:: PILIH ::</option>
                        <?php
                        foreach ($rekening as $row) { ?>
                            <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                        <?php } ?>
                    </select>
                    <div class="invalid-feedback">
                        <?= $validation->getError('kredit'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nominalkredit">Nominal Kredit</label>
                    <input type="text" name="nominalkredit" id="nominalkredit" class="form-control">
                    <div class="invalid-feedback">
                        <?= $validation->getError('nominalkredit'); ?>
                    </div>
                </div>
                <button type="button" id="formAddLedger" class="btn btn-primary btn-sm formAddLedger">Tambah</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow my-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-uppercase">
                    DEBET
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                        <thead class="bg-dark text-white">
                            <tr>
                                <td>REKENING</td>
                                <td>NOMINAL</td>
                                <td>HAPUS</td>
                            </tr>
                        </thead>
                        <tbody id="ledgerListDebet">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow my-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary text-uppercase">
                    KREDIT
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                        <thead class="bg-dark text-white">
                            <tr>
                                <td>REKENING</td>
                                <td>NOMINAL</td>
                                <td>HAPUS</td>
                            </tr>
                        </thead>
                        <tbody id="ledgerListKredit">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mb-5">
    <button class="btn btn-secondary btn-sm clearItem mr-2"><i class="fas fa-sm fa-broom fa-sm"></i> BERSIHKAN</button>
    <button type="button" class="btn btn-primary float-right btn-sm btnInsert" onclick="insertLedger()"><i class="fas fa-sm fa-save fa-sm"></i> SIMPAN</button>
</div>
<?= $this->endSection(); ?>