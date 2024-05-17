<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$settingModel = new \App\Models\SettingModel();
$userModel = new \App\Models\UserModel();
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$setting = $settingModel->find(1);
$user = $userModel->where('usr_id', session()->get('usr_id'))->join('jamkerja', 'jamkerja.jk_id = user.usr_jamkerja', 'left')
    ->select('user.*, jamkerja.jk_mulai, jamkerja.jk_selesai')->first();

$checkinoutModel = new \App\Models\CheckInOutModel();
$checkInOut = $checkinoutModel->where(['ci_user' => $user['usr_id']])->groupStart()->like('created_at', date('Y-m-d'))->groupEnd()->first();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/absen"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?= session()->get('pesan'); ?>

        <form id="checkinout" action="/dashboard/absenInsert" method="post">
            <?= csrf_field(); ?>
            <div class="row">
                <div class="col-md-6">
                    <div id="map" class="mb-3 border rounded" style="width: 100%; height:600px;"></div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body text-center small">
                            <em>Silahkan <a href="/dashboard/absen" class="font-weight-bold"><span class="badge badge-primary">RELOAD</span></a> halaman ini jika map tidak tampil.</em>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <input type="hidden" name="id" id="id">
                            <input type="hidden" name="type" id="type" value="in">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="tanggal">Tanggal</label>
                                        <input readonly type="text" name="tanggal" id="tanggal" value="<?= date('d/m/Y'); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="waktu">Pukul</label>
                                        <input readonly type="time" name="waktu" id="waktu" value="<?= date('H:i'); ?>" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="latitude" id="currentLat" class="form-control">
                            <input type="hidden" name="longitude" id="currentLong" class="form-control">
                            <div class="form-group">
                                <label for="absen">Absen</label>
                                <select <?= $checkInOut ? 'disabled' : 'required'; ?> name="absen" id="absen" class="form-control">
                                    <option value=""> -PILIH- </option>
                                    <option value="ijin">IJIN</option>
                                    <option value="sakit">SAKIT</option>
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea <?= $checkInOut ? 'disabled' : 'required'; ?> name="deskripsi" id="deskripsi" rows="5" class="form-control <?= $validation->hasError('deskripsi') ? 'is-invalid' : ''; ?>""></textarea>
                    </div>
                    <div class=" row justify-content-center">
                        <div class="col-md-6">
                            <button <?= $checkInOut ? 'disabled' : ''; ?> type="submit" id="btn" class="btn btn-primary btn-block btnCheck">Absen</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script>
    x = navigator.geolocation;
    x.getCurrentPosition(success, failure);

    function success(position) {
        const myLat = position.coords.latitude;
        const myLong = position.coords.longitude;
        const coords = new google.maps.LatLng(myLat, myLong);

        const mapOptions = {
            zoom: 18,
            center: coords,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        const map = new google.maps.Map(document.getElementById("map"), mapOptions);

        new google.maps.Marker({
            map: map,
            position: coords
        });

        $('#currentLat').val(myLat);
        $('#currentLong').val(myLong);
        // console.log({
        //     lat: myLat,
        //     lng: myLong
        // });

        $.ajax({
            url: '/dashboard/getAbsen',
            data: {
                lat: myLat,
                lng: myLong
            },
            method: 'post',
            dataType: 'json',
            success: function(response) {
                if (response.ab_id) {
                    $('form').attr('action', '/dashboard/absenUpdate');
                    $('#id').val(response.ab_id);
                    $("#absen option[value=" + response.ab_jenis + "]").prop("selected", true);
                    $('#deskripsi').val(response.ab_deskripsi);
                    $('.btnCheck').html('Perbarui');
                }
            }
        });
    }

    function failure() {
        console.log("error");
    }
</script>
<?= $this->endSection(); ?>