<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/users"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/insertUser" method="post">
            <?= csrf_field(); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control <?= $validation->hasError('username') ? 'is-invalid' : ''; ?>" value="<?= old('username') ? old('username') : ''; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('username'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input required type="email" name="email" id="email" class="form-control <?= $validation->hasError('email') ? 'is-invalid' : ''; ?>" value="<?= old('email') ? old('email') : ''; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('email'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input required type="text" name="nama" id="nama" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" value="<?= old('nama') ? old('nama') : ''; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nama'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nohp">No. HP</label>
                        <input required type="number" name="nohp" id="nohp" class="form-control <?= $validation->hasError('nohp') ? 'is-invalid' : ''; ?>" value="<?= old('nohp') ? old('nohp') : ''; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nohp'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select required name="status" id="status" class="form-control <?= $validation->hasError('status') ? 'is-invalid' : ''; ?>">
                            <option value="">:: PILIH ::</option>
                            <option value="1">AKTIF</option>
                            <option value="0">NONAKTIF</option>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('status'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bagian">Bagian</label>
                        <select required name="bagian" id="bagian" class="form-control <?= $validation->hasError('bagian') ? 'is-invalid' : ''; ?>">
                            <option value="">:: PILIH ::</option>
                            <?php
                            $bagianModel = new \App\Models\BagianModel();
                            foreach ($bagianModel->orderBy('bagian_nama', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['bagian_id']; ?>" <?= old('bagian') == $row['bagian_id'] ? 'selected' : ''; ?>><?= strtoupper($row['bagian_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('bagian'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jamkerja">Jam Kerja (Senin - Jumat)</label>
                        <select name="jamkerja" id="jamkerja" class="form-control <?= $validation->hasError('jamkerja') ? 'is-invalid' : ''; ?>">
                            <option value="">:: PILIH ::</option>
                            <?php
                            $jamkerjaModel = new \App\Models\JamkerjaModel();
                            foreach ($jamkerjaModel->orderBy('jk_mulai', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['jk_id']; ?>" <?= old('jamkerja') == $row['jk_id'] ? 'selected' : ''; ?>><?= strtoupper($row['jk_mulai']); ?> - <?= strtoupper($row['jk_selesai']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('jamkerja'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jamkerja2">Jam Kerja (Sabtu)</label>
                        <select name="jamkerja2" id="jamkerja2" class="form-control <?= $validation->hasError('jamkerja2') ? 'is-invalid' : ''; ?>">
                            <option value="">:: PILIH ::</option>
                            <?php
                            $jamkerjaModel = new \App\Models\JamkerjaModel();
                            foreach ($jamkerjaModel->orderBy('jk_mulai', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['jk_id']; ?>" <?= old('jamkerja2') == $row['jk_id'] ? 'selected' : ''; ?>><?= strtoupper($row['jk_mulai']); ?> - <?= strtoupper($row['jk_selesai']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('jamkerja2'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input required type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>