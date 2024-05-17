<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/rekening"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/updateRekening" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="id" value="<?= $rekening['rek_id']; ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kode">Kode</label>
                        <input required type="text" name="kode" id="kode" class="form-control <?= $validation->hasError('kode') ? 'is-invalid' : ''; ?>" value="<?= old('kode') ? old('kode') : $rekening['rek_kode']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('kode'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="rekening">Rekening</label>
                        <input required type="text" name="rekening" id="rekening" class="form-control <?= $validation->hasError('rekening') ? 'is-invalid' : ''; ?>" value="<?= old('rekening') ? old('rekening') : $rekening['rek_nama']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('rekening'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="reksub1">Rekening Sub 1</label>
                        <select name="reksub1" id="reksub1" class="form-control">
                            <option value="">:: PILIH ::</option>
                            <?php foreach ($rekeningModel->rekening(1) as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= $row['rek_id'] == $rekening['reksub2_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= $row['rek_nama']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="reksub2">Rekening Sub 2</label>
                        <select name="reksub2" id="reksub2" class="form-control">
                            <option value="">:: PILIH ::</option>
                            <?php foreach ($rekeningModel->rekening(2) as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= $row['rek_id'] == $rekening['reksub3_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= $row['rek_nama']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="reksub3">Rekening Sub 3</label>
                        <select name="reksub3" id="reksub3" class="form-control">
                            <option value="">:: PILIH ::</option>
                            <?php foreach ($rekeningModel->rekening(3) as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= $row['rek_id'] == $rekening['reksub4_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= $row['rek_nama']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="reksub4">Rekening Sub 4</label>
                        <select name="reksub4" id="reksub4" class="form-control">
                            <option value="">:: PILIH ::</option>
                            <?php foreach ($rekeningModel->rekening(4) as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= $row['rek_id'] == $rekening['reksub5_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= $row['rek_nama']; ?></option>
                            <?php } ?>
                        </select>
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