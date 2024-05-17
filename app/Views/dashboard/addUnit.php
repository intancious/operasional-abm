<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/unit"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/insertUnit" method="post">
            <?= csrf_field(); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="rekening">No. Akun</label>
                        <select required name="rekening" id="rekening" class="form-control selectpicker <?= $validation->hasError('rekening') ? 'is-invalid' : ''; ?>" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            $rekeningModel = new \App\Models\KoderekeningModel();
                            foreach ($rekeningModel
                                // ->where('rek_kode', '113.100')
                                // ->orWhere('rek_kode', '114.2')
                                ->orWhere('rek_kode', '114.3')
                                // ->orWhere('rek_kode', '114.4')
                                // ->orWhere('rek_kode', '113.500')
                                ->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= $row['rek_nama']; ?>" <?= old('rekening') == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('rekening'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <?php
                    $lastKode = $unitModel->orderBy('unit_id', 'DESC')->first();
                    ?>
                    <div class="form-group">
                        <label for="kode">Kode Unit</label>
                        <input type="text" name="kode" id="kode" class="form-control <?= $validation->hasError('kode') ? 'is-invalid' : ''; ?>" value="<?= old('kode') ? old('kode') : (intval($lastKode['unit_kode']) + 1); ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('kode'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tipe">Tipe Unit</label>
                        <select required name="tipe" id="tipe" class="form-control selectpicker <?= $validation->hasError('tipe') ? 'is-invalid' : ''; ?>" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            $typesModel = new \App\Models\TypesModel();
                            $types = $typesModel->orderBy('type_nama', 'ASC')->findAll();
                            foreach ($types as $row) { ?>
                                <option value="<?= $row['type_id']; ?>" data-tokens="<?= $row['type_nama']; ?>" <?= old('tipe') == $row['type_id'] ? 'selected' : ''; ?>><?= strtoupper($row['type_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('tipe'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama Unit</label>
                        <input required type="text" name="nama" id="nama" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" value="<?= old('nama') ? old('nama') : ''; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nama'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="nilaitanah">Nilai Tanah</label>
                <input required type="text" name="nilaitanah" id="nilaitanah" class="form-control <?= $validation->hasError('nilaitanah') ? 'is-invalid' : ''; ?>" value="<?= old('nilaitanah') ? old('nilaitanah') : ''; ?>">
                <div class="invalid-feedback">
                    <?= $validation->getError('nilaitanah'); ?>
                </div>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="5" class="form-control <?= $validation->hasError('keterangan') ? 'is-invalid' : ''; ?>"><?= old('keterangan') ? old('keterangan') : ''; ?></textarea>
                <div class="invalid-feedback">
                    <?= $validation->getError('keterangan'); ?>
                </div>
            </div>
            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>