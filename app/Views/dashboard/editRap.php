<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="#" onclick="window.history.go(-1);"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/updateRap" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="id" value="<?= $rap['rap_id']; ?>">
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
                        <label for="tipe">Tipe</label>
                        <select required name="tipe" id="tipe" class="form-control selectpicker <?= $validation->hasError('tipe') ? 'is-invalid' : ''; ?>" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            $typesModel = new \App\Models\TypesModel();
                            $types = $typesModel->orderBy('type_nama', 'ASC')->findAll();
                            foreach ($types as $row) { ?>
                                <option value="<?= $row['type_id']; ?>" data-tokens="<?= $row['type_nama']; ?>" <?= $rap['rap_tipe'] == $row['type_id'] ? 'selected' : ''; ?>><?= strtoupper($row['type_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('tipe'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="barang">Barang</label>
                        <select name="barang" id="barang" class="form-control selectpicker <?= $validation->hasError('barang') ? 'is-invalid' : ''; ?>" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            $barangModel = new \App\Models\BarangModel();
                            foreach ($barangModel->orderBy('barang_nama', 'ASC')->findAll() as $row) {
                            ?>
                                <option value="<?= $row['barang_id']; ?>" data-tokens="<?= $row['barang_nama']; ?>" <?= $rap['rap_barang'] == $row['barang_id'] ? 'selected' : ''; ?>><?= strtoupper($row['barang_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('barang'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ubk">Upah</label>
                        <select name="ubk" id="ubk" class="form-control selectpicker <?= $validation->hasError('ubk') ? 'is-invalid' : ''; ?>" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            $upahModel = new \App\Models\UpahModel();
                            foreach ($upahModel->orderBy('up_nama', 'ASC')->findAll() as $row) {
                            ?>
                                <option value="<?= $row['up_id']; ?>" data-tokens="<?= $row['up_nama']; ?>" <?= $rap['rap_upah'] == $row['up_id'] ? 'selected' : ''; ?>><?= strtoupper($row['up_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('ubk'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="volume">Volume</label>
                        <input required type="text" name="volume" id="volume" class="form-control <?= $validation->hasError('volume') ? 'is-invalid' : ''; ?>" value="<?= old('volume') ? old('volume') : $rap['rap_volume']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('volume'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="harga">Harga</label>
                        <input required type="text" name="harga" id="harga" class="form-control <?= $validation->hasError('harga') ? 'is-invalid' : ''; ?>" value="<?= old('harga') ? old('harga') : $rap['rap_harga']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('harga'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="5" class="form-control <?= $validation->hasError('keterangan') ? 'is-invalid' : ''; ?>"><?= old('keterangan') ? old('keterangan') : $rap['rap_keterangan']; ?></textarea>
                <div class="invalid-feedback">
                    <?= $validation->getError('keterangan'); ?>
                </div>
            </div>
            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>