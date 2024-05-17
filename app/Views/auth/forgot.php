<?php
$settingModel = new \App\Models\SettingModel();
$setting = $settingModel->find(1);
?>
<?= $this->extend('auth/template'); ?>

<?= $this->section('content'); ?>
<!-- Login Content -->
<div class="container-login">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-5 col-md-5">
            <div class="card shadow-sm my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="login-form">
                                <div class="text-center mb-4">
                                    <?php if ($setting['setting_logo']) { ?>
                                        <img src="/assets/img/<?= $setting['setting_logo']; ?>" alt="<?= $setting['setting_nama']; ?>" style="max-width: 200px;" class="img-fluid mb-3">
                                    <?php } ?>
                                    <h1 class="h5 text-gray-900 mt-4 text-uppercase font-weight-bold">LUPA PASSWORD</h1>
                                </div>
                                <?= session()->get('pesan'); ?>
                                <form class="user" action="/auth/reset" method="POST">
                                    <?= csrf_field(); ?>
                                    <div class="form-group">
                                        <input type="text" name="user" class="form-control <?= $validation->hasError('user') ? 'is-invalid' : ''; ?>" id="user" placeholder="Username atau Email" autofocus value="<?= old('user'); ?>">
                                    </div>
                                    <input type="hidden" name="number1" value="<?= $captcha['number1']; ?>">
                                    <input type="hidden" name="number2" value="<?= $captcha['number2']; ?>">
                                    <div class="form-group">
                                        <p class="mb-1 mt-3 small text-left font-weight-bold text-muted">Hasil dari &nbsp;<strong><?= $captcha['number1']; ?> + <?= $captcha['number2']; ?> =</strong></p>
                                        <label for="captcha" class="sr-only">Captcha</label>
                                        <input required type="number" name="captcha" class="form-control <?= $validation->hasError('captcha') ? 'is-invalid' : ''; ?>" id="captcha" aria-describedby="captcha" placeholder="..........">
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block">Lupa Password</button>
                                    </div>
                                </form>
                                <p class="small text-center mt-4 mb-0"><a href="/auth">Masuk Ke Dashboard</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login Content -->
<?= $this->endSection(); ?>