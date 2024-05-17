<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/suplier"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/updateCustomer" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="id" value="<?= $customer['cust_id'] ?>">
            <input type="hidden" name="rekening" id="rekening" value="<?= $customer['cust_rekening']; ?>">
            <input type="hidden" name="kode" id="kode" value="<?= $customer['cust_kode']; ?>">
            <!-- <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="rekening">No. Akun</label>
                        <select required name="rekening" id="rekening" class="form-control selectpicker <?= $validation->hasError('rekening') ? 'is-invalid' : ''; ?>" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            $rekeningModel = new \App\Models\KoderekeningModel();
                            $pmg = $rekeningModel->where('rek_kode', '116')->first();
                            foreach ($rekeningModel->where('rek_kode', $pmg['rek_kode'])->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= $row['rek_nama']; ?>" <?= $customer['cust_rekening'] == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('rekening'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kode">Kode Customer</label>
                        <input required type="text" name="kode" id="kode" class="form-control <?= $validation->hasError('kode') ? 'is-invalid' : ''; ?>" value="<?= old('kode') ? old('kode') : $customer['cust_kode']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('kode'); ?>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input required type="text" name="nama" id="nama" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" value="<?= old('nama') ? old('nama') : $customer['cust_nama']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nama'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telp">Telp</label>
                        <input type="number" name="telp" id="telp" class="form-control <?= $validation->hasError('telp') ? 'is-invalid' : ''; ?>" value="<?= old('telp') ? old('telp') : $customer['cust_telp']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('telp'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" rows="5" class="form-control <?= $validation->hasError('alamat') ? 'is-invalid' : ''; ?>"><?= old('alamat') ? old('alamat') : $customer['cust_alamat']; ?></textarea>
                <div class="invalid-feedback">
                    <?= $validation->getError('alamat'); ?>
                </div>
            </div>
            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>