<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$rekeningModel = new \App\Models\KoderekeningModel();
$kkModel = new \App\Models\KasKecilModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/kaskecil"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/updateKaskecil" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="id" value="<?= $kaskecil['kk_id']; ?>">
            <div class="form-group">
                <label for="jenis">Jenis Kas</label>
                <select required name="jenis" id="jenis" class="form-control">
                    <option value="">:: PILIH ::</option>
                    <?php foreach ($kkModel->getJenisKas() as $row) { ?>
                        <option value="<?= $row['id']; ?>" <?= $kaskecil['kk_jenis'] == $row['id'] ? 'selected' : ''; ?>><?= $row['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nomor">Nomor</label>
                        <input readonly required type="text" name="nomor" id="nomor" class="form-control <?= $validation->hasError('nomor') ? 'is-invalid' : ''; ?>" value="<?= old('nomor') ? old('nomor') : $kaskecil['kk_nomor']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nomor'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input required type="text" name="tanggal" id="tanggalSelect" class="form-control <?= $validation->hasError('tanggal') ? 'is-invalid' : ''; ?>" value="<?= old('tanggal') ? old('tanggal') : date('d-m-Y', strtotime($kaskecil['kk_tanggal'])); ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('tanggal'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="penerima">Penerima</label>
                        <select required name="penerima" id="penerima" class="form-control <?= $validation->hasError('penerima') ? 'is-invalid' : ''; ?>">
                            <option value="">:: PILIH ::</option>
                            <?php
                            $userModel = new \App\Models\UserModel();
                            foreach ($userModel->orderBy('usr_nama', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['usr_id']; ?>" <?= $kaskecil['kk_user'] == $row['usr_id'] ? 'selected' : ''; ?>><?= strtoupper($row['usr_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('penerima'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input required type="text" name="nominal" id="nominal" class="form-control <?= $validation->hasError('nominal') ? 'is-invalid' : ''; ?>" value="<?= old('nominal') ? old('nominal') : $kaskecil['kk_nominal']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nominal'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="uraian">Uraian / Keterangan</label>
                <textarea name="uraian" id="uraian" class="form-control <?= $validation->hasError('uraian') ? 'is-invalid' : ''; ?>" rows="3"><?= $kaskecil['kk_uraian']; ?></textarea>
                <div class="invalid-feedback">
                    <?= $validation->getError('uraian'); ?>
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
                                <option value="<?= $row['rek_id']; ?>" <?= $kaskecil['kk_debet'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
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
                                <option value="<?= $row['rek_id']; ?>" <?= $kaskecil['kk_kredit'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
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