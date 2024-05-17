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
                                </div>
                                <?= session()->get('pesan'); ?>
                                <form class="user" action="/auth/login" method="POST">
                                    <?= csrf_field(); ?>
                                    <div class="form-group">
                                        <input type="text" name="user" class="form-control <?= $validation->hasError('user') ? 'is-invalid' : ''; ?>" id="user" placeholder="Username atau Email" autofocus value="<?= old('user'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control <?= $validation->hasError('password') ? 'is-invalid' : ''; ?>" id="password" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                                    </div>
                                </form>
                                <p class="small text-center mt-4 mb-0"><a href="/auth/forgot">Lupa Password</a></p>
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