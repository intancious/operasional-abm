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
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/whatsapp"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="row">
    <div class="col-md-auto">
        <div class="card mb-4">
            <div class="card-body">
                <?= session()->get('pesan'); ?>
                <img id="qrcode" src="https://c.tenor.com/I6kN-6X7nhAAAAAj/loading-buffering.gif" alt="" title="">
                <div id="msg"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    $(document).ready(function() {
        qrCode();
    });

    function qrCode() {
        $.ajax({
            url: "/dashboard/whatsappAuth",
            method: 'get',
            dataType: 'json',
            success: function(response) {
                if (response.authenticated) {
                    $('#qrcode').hide();
                    $('#msg').show();
                    $('#msg').html(`<h5 class="mb-0"><i class="fas fa-circle mr-2 text-success"></i> WhatsApp Is Online</h5>`);
                } else {
                    $('#msg').hide();
                    $('#qrcode').show();
                    $('#qrcode').attr('src', response.qrcode);
                    $('#qrcode').attr('alt', response.message);
                    $('#qrcode').attr('title', response.message);
                }
                setTimeout(function() {
                    qrCode();
                }, 5000);
            },
            error: function(err) {
                setTimeout(function() {
                    qrCode();
                }, 5000);
            }
        });
    }
</script>
<?= $this->endSection(); ?>