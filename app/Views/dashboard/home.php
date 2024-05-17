<?= $this->extend('/dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$settingModel = new \App\Models\SettingModel();
$setting = $settingModel->find(1);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard"><i class="fas fa-sm fa-sync-alt ml-2"></i></a>
    </h1>
</div>

<div class="row mb-2">
    <div class="col-lg">
        <p class="mt-0 text-uppercase">&nbsp;<?= $session['usr_nama']; ?>, Selamat Datang di Aplikasi <?= $setting['setting_nama']; ?></p>
    </div>
</div>
<?= $this->endSection(); ?>