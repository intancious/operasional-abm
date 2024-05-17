<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">&nbsp;<?= $title_bar; ?></h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?= session()->get('pesan'); ?>
        <form action="/dashboard/updateProfile" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" value="<?= $user['usr_id']; ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input required type="text" name="username" id="username" class="form-control <?= $validation->hasError('username') ? 'is-invalid' : ''; ?>" value="<?= old('username') ? old('username') : $user['usr_username']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('username'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input required type="email" name="email" id="email" class="form-control <?= $validation->hasError('email') ? 'is-invalid' : ''; ?>" value="<?= old('email') ? old('email') : $user['usr_email']; ?>">
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
                        <input required type="text" name="nama" id="nama" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" value="<?= old('nama') ? old('nama') : $user['usr_nama']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nama'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nohp">No. HP</label>
                        <input required type="number" name="nohp" id="nohp" class="form-control <?= $validation->hasError('nohp') ? 'is-invalid' : ''; ?>" value="<?= old('nohp') ? old('nohp') : $user['usr_nohp']; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nohp'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 mb-3">
                    <?php if ($user['usr_photo']) { ?>
                        <img id="preview" src="/assets/img/<?= $user['usr_photo']; ?>" class="img-thumbnail" alt="<?= $user['usr_nama']; ?>">
                    <?php } else { ?>
                        <img id="preview" src="/assets/img/undraw_profile.svg" class="img-thumbnail" alt="<?= $user['usr_nama']; ?>">
                    <?php } ?>
                </div>
                <div class="col-md-10 mb-3">
                    <div class="form-group">
                        <label for="photo">Photo</label>
                        <input type="file" class="form-control-file" name="photo" id="photo">
                        <div class="invalid-feedback">
                            <?= $validation->getError('photo'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <p class="small mt-0 mb-1 text-muted font-italic">* Biarkan kosong jika tidak ingin mengganti password.</p>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group mt-4 mb-0">
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>