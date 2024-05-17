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
        <form action="/dashboard/updatePengaturan" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <div class="form-group">
                <label for="nama">Nama Aplikasi</label>
                <input type="text" name="nama" id="nama" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" value="<?= old('nama') ? old('nama') : $pengaturan['setting_nama']; ?>">
                <div class="invalid-feedback">
                    <?= $validation->getError('nama'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <img id="preview_logo" src="/assets/img/<?= $pengaturan['setting_logo']; ?>" class="img-thumbnail">
                        </div>
                        <div class="col-md-9 mb-3">
                            <div class="form-group">
                                <label for="logo">Logo</label>
                                <input type="file" class="form-control-file" name="logo" id="logoSetting">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <img id="preview_logo2" src="/assets/img/<?= $pengaturan['setting_logo2']; ?>" class="img-thumbnail">
                        </div>
                        <div class="col-md-9 mb-3">
                            <div class="form-group">
                                <label for="logo2">Logo</label>
                                <input type="file" class="form-control-file" name="logo2" id="logo2Setting">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <img id="preview_favicon" src="/assets/img/<?= $pengaturan['setting_favicon']; ?>" class="img-thumbnail">
                        </div>
                        <div class="col-md-9 mb-3">
                            <div class="form-group">
                                <label for="favicon">Favicon</label>
                                <input type="file" class="form-control-file" name="favicon" id="faviconSetting">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="text" name="latitude" id="latitude" class="form-control" value="<?= $pengaturan['setting_latitude']; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="text" name="longitude" id="longitude" class="form-control" value="<?= $pengaturan['setting_longitude']; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="radius">Radius (Meter)</label>
                        <input type="number" name="radius" id="radius" class="form-control" value="<?= $pengaturan['setting_radius']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>