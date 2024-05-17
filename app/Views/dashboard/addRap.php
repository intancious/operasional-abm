<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$request = \Config\Services::request();
?>
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
                            <option value="<?= $row['type_id']; ?>" data-tokens="<?= $row['type_nama']; ?>" <?= old('tipe') == $row['type_id'] || $request->getVar('tipe') == $row['type_id'] ? 'selected' : ''; ?>><?= strtoupper($row['type_nama']); ?></option>
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
                    <select required name="barang" id="barang" class="form-control selectpicker <?= $validation->hasError('barang') ? 'is-invalid' : ''; ?>" data-live-search="true">
                        <option value="" data-tokens="">:: PILIH ::</option>
                        <?php
                        $barangModel = new \App\Models\BarangModel();
                        foreach ($barangModel->orderBy('barang_nama', 'ASC')->findAll() as $row) {
                        ?>
                            <option value="<?= $row['barang_id']; ?>" data-tokens="<?= $row['barang_nama']; ?>" <?= old('barang') == $row['barang_id'] ? 'selected' : ''; ?>><?= strtoupper($row['barang_nama']); ?></option>
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
                    <select required name="ubk" id="ubk" class="form-control selectpicker <?= $validation->hasError('ubk') ? 'is-invalid' : ''; ?>" data-live-search="true">
                        <option value="" data-tokens="">:: PILIH ::</option>
                        <?php
                        $upahModel = new \App\Models\UpahModel();
                        foreach ($upahModel->orderBy('up_nama', 'ASC')->findAll() as $row) {
                        ?>
                            <option value="<?= $row['up_id']; ?>" data-tokens="<?= $row['up_nama']; ?>" <?= old('ubk') == $row['up_id'] ? 'selected' : ''; ?>><?= strtoupper($row['up_nama']); ?></option>
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
                    <input required type="text" name="volume" id="volume" class="form-control <?= $validation->hasError('volume') ? 'is-invalid' : ''; ?>" value="<?= old('volume') ? old('volume') : ''; ?>">
                    <div class="invalid-feedback">
                        <?= $validation->getError('volume'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input required type="text" name="harga" id="harga" class="form-control <?= $validation->hasError('harga') ? 'is-invalid' : ''; ?>" value="<?= old('harga') ? old('harga') : ''; ?>">
                    <div class="invalid-feedback">
                        <?= $validation->getError('harga'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3" class="form-control <?= $validation->hasError('keterangan') ? 'is-invalid' : ''; ?>"><?= old('keterangan') ? old('keterangan') : ''; ?></textarea>
            <div class="invalid-feedback">
                <?= $validation->getError('keterangan'); ?>
            </div>
        </div>
        <div class="form-group mt-2">
            <button type="submit" class="btn btn-primary formAddRap">Tambah</button>
        </div>
    </div>
</div>

<div class="card shadow my-4">
    <div class="card-body">
        <div class="table-responsive">
            <table id="customTable" class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                <thead class="thead-light font-weight-bold">
                    <tr>
                        <th>#</th>
                        <th>TIPE</th>
                        <th>BARANG / PEKERJAAN</th>
                        <th>SATUAN</th>
                        <th>VOLUME</th>
                        <th>HARGA SATUAN</th>
                        <th>SUBTOTAL</th>
                        <th>KETERANGAN</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody id="rapList"></tbody>
            </table>
        </div>
        <div class="my-3">
            <button class="btn btn-danger btn-sm clearItemRap mr-2"><i class="fas fa-broom mr-1"></i> BERSIHKAN</button>
            <button type="button" class="btn btn-primary float-right btn-sm" onclick="insertRap()"><i class="fas fa-save mr-1"></i> SIMPAN</button>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>