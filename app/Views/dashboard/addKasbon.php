<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$rekeningModel = new \App\Models\KoderekeningModel();
$kasbonModel = new \App\Models\KasbonModel();
$kkModel = new \App\Models\KasKecilModel();
$tukangModel = new \App\Models\TukangModel();
$kasKecil = $kkModel->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
    ->where(['kas_kecil.kk_status' => 1, 'kas_kecil.kk_jenis' => 4])
    ->select('kas_kecil.*, user.usr_nama')
    ->orderBy('kas_kecil.kk_tanggal', 'DESC')->findAll();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/kasbon"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/insertKasbon" method="post">
            <?= csrf_field(); ?>
            <?php
            $valNumb = $kasbonModel->countAll() + 1;
            $nomor = 'KB' . str_pad($valNumb, 4, "0", STR_PAD_LEFT);

            $checkIsExist = $kasbonModel->where('bu_nomor', $nomor)->first();
            if ($checkIsExist) {
                $lastRow = $kasbonModel->orderBy('bu_id', 'DESC')->first();
                $exNumb = explode('-', $lastRow['bu_nomor']);
                $newNumb = intval($exNumb[0]) + 1;
                $fixNumber = 'KB' . str_pad($newNumb, 4, "0", STR_PAD_LEFT);
            } else {
                $fixNumber = $nomor;
            }
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kaskecil">Kas Kecil</label>
                        <select required name="kaskecil" id="kaskecil" class="form-control <?= $validation->hasError('kaskecil') ? 'is-invalid' : ''; ?>">
                            <option value="">:: PILIH ::</option>
                            <?php
                            foreach ($kasKecil as $row) { ?>
                                <option value="<?= $row['kk_id']; ?>" <?= old('kaskecil') == $row['kk_id'] ? 'selected' : ''; ?>>(<?= $row['kk_nomor']; ?>) <?= strtoupper($row['usr_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('kaskecil'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input required type="text" name="tanggal" id="tanggalSelect" class="form-control <?= $validation->hasError('tanggal') ? 'is-invalid' : ''; ?>" value="<?= old('tanggal') ? old('tanggal') : date('d-m-Y'); ?>">
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
                        <input required type="text" name="nomor" id="nomor" class="form-control <?= $validation->hasError('nomor') ? 'is-invalid' : ''; ?>" value="<?= old('nomor') ? old('nomor') : $fixNumber; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nomor'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tukang">Tukang</label>
                        <select required name="tukang" id="tukang" class="form-control selectpicker <?= $validation->hasError('tukang') ? 'is-invalid' : ''; ?>" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php foreach ($tukangModel->orderBy('tk_nama', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['tk_id']; ?>" <?= old('tukang') == $row['tk_id'] ? 'selected' : ''; ?> data-tokens="<?= strtoupper($row['tk_nama']); ?>"><?= strtoupper($row['tk_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('tukang'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nominal">Nominal</label>
                        <input required type="text" name="nominal" id="nominal" class="form-control <?= $validation->hasError('nominal') ? 'is-invalid' : ''; ?>" value="<?= old('nominal') ? old('nominal') : 0; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nominal'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="uraian">Uraian / Keterangan</label>
                        <textarea name="uraian" id="uraian" class="form-control <?= $validation->hasError('uraian') ? 'is-invalid' : ''; ?>" rows="1"></textarea>
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
                                <option value="<?= $row['rek_id']; ?>" <?= old('debet') == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
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
                                <option value="<?= $row['rek_id']; ?>" <?= old('kredit') == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
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