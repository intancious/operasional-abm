<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/upah"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/insertUpah" method="post">
            <?= csrf_field(); ?>
            <?php
            $rekeningModel = new \App\Models\KoderekeningModel();
            $upah = $rekeningModel->where('rek_kode', '62.2')->first();
            $lastKode = $upahModel->orderBy('up_id', 'DESC')->first();
            ?>
            <input type="hidden" name="rekening" id="rekening" value="<?= $upah['rek_id']; ?>">
            <input type="hidden" name="kode" id="kode" value="<?= isset($lastKode) ? (intval($lastKode['up_kode']) + 1) : 100; ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama UBK</label>
                        <input required type="text" name="nama" id="nama" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" value="<?= old('nama') ? old('nama') : ''; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nama'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kategori">Kategori Barang</label>
                        <select required name="kategori" id="kategori" class="form-control <?= $validation->hasError('kategori') ? 'is-invalid' : ''; ?>">
                            <option value="">:: PILIH ::</option>
                            <?php
                            $kabarModel = new \App\Models\KabarModel();
                            foreach ($kabarModel->orderBy('kabar_nama', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['kabar_id']; ?>" <?= old('kategori') == $row['kabar_id'] ? 'selected' : ''; ?>><?= strtoupper($row['kabar_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('kategori'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="satuan">Satuan</label>
                        <select name="satuan" id="satuan" class="form-control <?= $validation->hasError('satuan') ? 'is-invalid' : ''; ?>">
                            <option value="">:: PILIH ::</option>
                            <?php
                            $satuanModel = new \App\Models\SatuanModel();
                            foreach ($satuanModel->orderBy('satuan_nama', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['satuan_id']; ?>" <?= old('satuan') == $row['satuan_id'] ? 'selected' : ''; ?>><?= strtoupper($row['satuan_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('satuan'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nilai">Nilai (Rp)</label>
                        <input required type="text" name="nilai" id="nilai" class="form-control <?= $validation->hasError('nilai') ? 'is-invalid' : ''; ?>" value="<?= old('nilai') ? old('nilai') : '0'; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nilai'); ?>
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