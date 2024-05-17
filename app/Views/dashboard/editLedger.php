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
                    <input required type="text" name="tanggal" id="tanggalPicker" class="form-control datepicker" value="<?= date('d-m-Y', strtotime($ledger[0]['created_at'])); ?>">
                    <div class="invalid-feedback">
                        <?= $validation->getError('tanggal'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nomor">Nomor Transaksi</label>
                    <input required type="text" name="nomor" id="nomor" class="form-control" value="<?= $ledger[0]['gl_nomor']; ?>">
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
                    <input type="text" name="uraian" id="uraian" class="form-control" value="<?= $ledger[0]['gl_uraian']; ?>">
                    <div class="invalid-feedback">
                        <?= $validation->getError('uraian'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="ket_trx">Ket. Transaksi</label>
                    <input required type="text" name="ket_trx" id="ket_trx" class="form-control" value="<?= $ledger[0]['gl_trx']; ?>">
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
                    DEBET BARU
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
                    KREDIT BARU
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
<div class="mb-4">
    <button class="btn btn-secondary btn-sm clearItem mr-2"><i class="fas fa-sm fa-broom fa-sm"></i> BERSIHKAN</button>
    <button type="button" class="btn btn-primary float-right btn-sm btnInsert" onclick="updateLedger()"><i class="fas fa-sm fa-save fa-sm"></i> SIMPAN</button>
</div>
<hr />
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
                                <th>REKENING</th>
                                <th>NOMINAL</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalDebet = 0;
                            foreach ($ledger as $row) {
                                if ($row['gl_nominalDebet'] || $row['gl_nominalDebet'] != 0) {
                                    $totalDebet += $row['gl_nominalDebet'];
                                    $getDebetRekening = $rekeningModel
                                        ->where('rek_id', $row['gl_debet'])
                                        ->first();
                            ?>
                                    <tr>
                                        <td>(<?= $getDebetRekening['rek_kode']; ?>) <?= strtoupper($getDebetRekening['rek_nama']); ?></td>
                                        <td><?= $row['gl_nominalDebet'] ? number_format($row['gl_nominalDebet'], 0, ".", ".") : 0; ?></td>
                                        <td>
                                            <form class="d-inline" action="/dashboard/deleteItemLedger" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['gl_id']; ?>">
                                                <input type="hidden" name="nomor" value="<?= $row['gl_nomor']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-light font-weight-bold">
                                <td>TOTAL</td>
                                <td><?= $totalDebet ? number_format($totalDebet, 0, ".", ".") : 0; ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
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
                                <th>REKENING</th>
                                <th>NOMINAL</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalKredit = 0;
                            foreach ($ledger as $row) {
                                if ($row['gl_nominalKredit'] || $row['gl_nominalKredit'] != 0) {
                                    $totalKredit += $row['gl_nominalKredit'];
                                    $getKreditRekening = $rekeningModel
                                        ->where('rek_id', $row['gl_kredit'])
                                        ->first();
                            ?>
                                    <tr>
                                        <td>(<?= $getKreditRekening['rek_kode']; ?>) <?= strtoupper($getKreditRekening['rek_nama']); ?></td>
                                        <td><?= $row['gl_nominalKredit'] ? number_format($row['gl_nominalKredit'], 0, ".", ".") : 0; ?></td>
                                        <td>
                                            <form class="d-inline" action="/dashboard/deleteItemLedger" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['gl_id']; ?>">
                                                <input type="hidden" name="nomor" value="<?= $row['gl_nomor']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-light font-weight-bold">
                                <td>TOTAL</td>
                                <td><?= $totalKredit ? number_format($totalKredit, 0, ".", ".") : 0; ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>