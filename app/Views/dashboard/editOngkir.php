<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$rekeningModel = new \App\Models\KoderekeningModel();
$ongkirModel = new \App\Models\OngkirPembelianModel();
$hsModel = new \App\Models\HutangsuplierModel();
$kkModel = new \App\Models\KasKecilModel();
$kasKecil = $kkModel->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
    ->where(['kas_kecil.kk_jenis' => 2])
    ->select('kas_kecil.*, user.usr_nama')
    ->orderBy('kas_kecil.kk_tanggal', 'DESC')->findAll();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/ongkoskirim"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/updateOngkir" method="post">
            <input type="hidden" name="id" id="id" value="<?= $ongkir['op_id']; ?>">
            <?= csrf_field(); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="faktur">Suplier</label>
                        <select required name="faktur" id="faktur" class="form-control <?= $validation->hasError('faktur') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php foreach ($hsModel->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left')
                                ->orderBy('hs_id', 'DESC')->groupBy('hutangsuplier.hs_suplier')->findAll() as $row) { ?>
                                <option value="<?= $row['suplier_id']; ?>" <?= $ongkir['op_suplier'] == $row['suplier_id'] ? 'selected' : ''; ?> data-tokens="<?= strtoupper($row['suplier_nama']); ?>"><?= strtoupper($row['suplier_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('faktur'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input required type="text" name="tanggal" id="tanggalSelect" class="form-control <?= $validation->hasError('tanggal') ? 'is-invalid' : ''; ?>" value="<?= old('tanggal') ? old('tanggal') : date('d-m-Y', strtotime($ongkir['op_tanggal'])); ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('tanggal'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nomor">Nomor</label>
                        <input required type="text" name="nomor" id="nomor" class="form-control <?= $validation->hasError('nomor') ? 'is-invalid' : ''; ?>" value="<?= old('nomor') ? old('nomor') : $ongkir['op_nomor']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nomor'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input required type="text" name="nominal" id="nominal" class="form-control <?= $validation->hasError('nominal') ? 'is-invalid' : ''; ?>" value="<?= old('nominal') ? old('nominal') : $ongkir['op_bayar']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nominal'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kaskecil">Kas Kecil</label>
                        <select required name="kaskecil" id="kaskecil" class="form-control <?= $validation->hasError('kaskecil') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <?php foreach ($kasKecil as $row) {
                                if ($ongkir['op_kaskecil'] == $row['kk_id']) {
                            ?>
                                    <option value="<?= $row['kk_id']; ?>" <?= $ongkir['op_kaskecil'] == $row['kk_id'] ? 'selected' : ''; ?> data-tokens="(<?= $row['kk_nomor']; ?>) <?= strtoupper($row['usr_nama']); ?>">(<?= $row['kk_nomor']; ?>) <?= strtoupper($row['usr_nama']); ?></option>
                            <?php }
                            } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('kaskecil'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="uraian">Uraian / Keterangan</label>
                        <textarea name="uraian" id="uraian" class="form-control <?= $validation->hasError('uraian') ? 'is-invalid' : ''; ?>" rows="1"><?= $ongkir['op_keterangan']; ?></textarea>
                        <div class="invalid-feedback">
                            <?= $validation->getError('uraian'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="debet">Debet</label>
                        <select name="debet" id="debet" class="form-control <?= $validation->hasError('debet') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= $ongkir['op_debet'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('debet'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kredit">Kredit</label>
                        <select name="kredit" id="kredit" class="form-control <?= $validation->hasError('kredit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= $ongkir['op_kredit'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('kredit'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>