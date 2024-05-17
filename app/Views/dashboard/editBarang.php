<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/barang"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/updateBarang" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="id" value="<?= $barang['barang_id']; ?>">
            <input type="hidden" name="rekening" id="rekening" value="<?= $barang['barang_rekening']; ?>">
            <input type="hidden" name="kode" id="kode" value="<?= $barang['barang_kode']; ?>">
            <!-- <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="rekening">No. Akun</label>
                        <select disabled name="rekening" id="rekening" class="form-control selectpicker <?= $validation->hasError('rekening') ? 'is-invalid' : ''; ?>" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            $rekeningModel = new \App\Models\KoderekeningModel();
                            $pmg = $rekeningModel->where('rek_kode', '114.2')->first();
                            foreach ($rekeningModel->where('rek_kode', '114.2')->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= $row['rek_nama']; ?>" <?= ($pmg['rek_id'] ? $pmg['rek_id'] : $barang['barang_rekening']) == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('rekening'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kode">Kode Barang</label>
                        <input disabled type="text" name="kode" id="kode" class="form-control <?= $validation->hasError('kode') ? 'is-invalid' : ''; ?>" value="<?= old('kode') ? old('kode') : $barang['barang_kode']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('kode'); ?>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama Barang</label>
                        <input required type="text" name="nama" id="nama" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" value="<?= old('nama') ? old('nama') : $barang['barang_nama']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nama'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="satuan">Satuan Barang</label>
                        <select required name="satuan" id="satuan" class="form-control <?= $validation->hasError('satuan') ? 'is-invalid' : ''; ?>">
                            <option value="">:: PILIH ::</option>
                            <?php
                            $satuanModel = new \App\Models\SatuanModel();
                            foreach ($satuanModel->orderBy('satuan_nama', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['satuan_id']; ?>" <?= $barang['barang_satuan'] == $row['satuan_id'] ? 'selected' : ''; ?>><?= strtoupper($row['satuan_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('satuan'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kategori">Kategori Barang</label>
                        <select required name="kategori" id="kategori" class="form-control <?= $validation->hasError('kategori') ? 'is-invalid' : ''; ?>">
                            <option value="">:: PILIH ::</option>
                            <?php
                            $kabarModel = new \App\Models\KabarModel();
                            foreach ($kabarModel->orderBy('kabar_nama', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['kabar_id']; ?>" <?= $barang['barang_kategori'] == $row['kabar_id'] ? 'selected' : ''; ?>><?= strtoupper($row['kabar_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('kategori'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="hargaStok">Harga Barang</label>
                        <input required type="text" name="harga" id="hargaStok" class="form-control <?= $validation->hasError('harga') ? 'is-invalid' : ''; ?>" value="<?= old('harga') ? old('harga') : $barang['barang_harga']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('hargaStok'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jumlah">Stok Awal</label>
                        <input type="text" name="jumlah" id="jumlah" class="form-control <?= $validation->hasError('jumlah') ? 'is-invalid' : ''; ?>" value="<?= old('jumlah') ? old('jumlah') : $barang['barang_jumlah']; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                        <div class="invalid-feedback">
                            <?= $validation->getError('jumlah'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="minstok">Stok Minimal</label>
                        <input type="text" name="minstok" id="minstok" class="form-control <?= $validation->hasError('minstok') ? 'is-invalid' : ''; ?>" value="<?= old('minstok') ? old('minstok') : $barang['barang_minstok']; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                        <div class="invalid-feedback">
                            <?= $validation->getError('minstok'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="harga">Harga Barang (Rp)</label>
                            <input required type="text" name="harga" id="harga" class="form-control <?= $validation->hasError('harga') ? 'is-invalid' : ''; ?>" value="<?= old('harga') ? old('harga') : $barang['barang_harga']; ?>">
                            <div class="invalid-feedback">
                                <?= $validation->getError('harga'); ?>
                            </div>
                        </div>
                    </div>
                </div> -->
            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>