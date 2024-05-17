<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$settingModel = new \App\Models\SettingModel();
$timeworkModel = new \App\Models\JamkerjaModel();
$userModel = new \App\Models\UserModel();
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$setting = $settingModel->find(1);
$user = $userModel->find(session()->get('usr_id'));
if (date('l') == 'Saturday' && $user['usr_jamkerja2']) {
    $usrJamKerja = $timeworkModel->find($user['usr_jamkerja2']);
} else {
    $usrJamKerja = $timeworkModel->find($user['usr_jamkerja']);
}
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/checkout"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?= session()->get('pesan'); ?>
        <form action="/dashboard/checkInsert" method="post">
            <?= csrf_field(); ?>
            <div class="row">
                <div class="col-md-6">
                    <div id="map" class="mb-3 border rounded" style="width: 100%; height:600px;"></div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            Absen hanya bisa dilakukan di radius lokasi kantor</strong><br />pada pukul <strong><span id="startTime"><?= $usrJamKerja['jk_mulai'] ? $usrJamKerja['jk_mulai'] : '-'; ?></span> s/d <span id="endTime"><?= $usrJamKerja['jk_selesai'] ? $usrJamKerja['jk_selesai'] : '-'; ?></span> WIB</strong>.

                            <span class="small">
                                <br /><br />
                                Silahkan <a href="/dashboard/<?= $request->uri->getSegment(2); ?>" class="font-weight-bold"><span class="badge badge-primary">RELOAD</span></a> halaman jika map tidak tampil.
                            </span>
                        </div>
                    </div>
                    <?php
                    $checkinoutModel = new \App\Models\CheckInOutModel();
                    $checkin =  $checkinoutModel->checkJikaSudahAbsen('in');
                    $checkout =  $checkinoutModel->checkJikaSudahAbsen('out');
                    ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <input type="hidden" name="type" id="type" value="out">
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
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="latitude">Latitude</label>
                                        <input readonly type="text" name="latitude" id="currentLat" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <label for="longitude">Longitude</label>
                                        <input readonly type="text" name="longitude" id="currentLong" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="deskripsi">Deksripsi</label>
                                <textarea <?= $checkout || !$checkin ? 'disabled' : '' ?> type="text" name="deskripsi" id="deskripsi" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($checkout) { ?>
                        <div class="alert alert-warning small text-center" role="alert">
                            CHECK OUT PUKUL <?= date('H:i', strtotime($checkout['created_at'])); ?> WIB
                        </div>
                        <?php } else {
                        if ($checkin) {
                        ?>
                            <div id="warning"></div>
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <button type="submit" id="btn" class="btn btn-primary btn-block">Check Out</button>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-warning small text-center" role="alert">Anda belum check in hari ini.</div>
                    <?php }
                    } ?>
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
        const coords = new google.maps.LatLng(<?= $setting['setting_latitude']; ?>, <?= $setting['setting_longitude']; ?>); // lokasi kantor

        const myLat = position.coords.latitude;
        const myLong = position.coords.longitude;
        const currentLocation = new google.maps.LatLng(myLat, myLong); // lokasi user

        const mapOptions = {
            zoom: 20,
            center: coords,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        const map = new google.maps.Map(document.getElementById("map"), mapOptions);

        const marker = new google.maps.Marker({
            map: map,
            position: coords
        });

        new google.maps.Marker({
            map: map,
            position: currentLocation,
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
            }
        });

        const sunCircle = {
            strokeColor: "#c3fc49",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#c3fc49",
            fillOpacity: 0.35,
            map: map,
            center: coords,
            radius: <?= $setting['setting_radius']; ?> // meter
        };

        circle = new google.maps.Circle(sunCircle)
        circle.bindTo('center', marker, 'position');
        const bounds = circle.getBounds()

        $('#currentLat').val(myLat);
        $('#currentLong').val(myLong);

        if (bounds.contains(currentLocation)) {
            // true
            $("#btn").attr("disabled", false);
            $('#warning').hide();
        } else {
            // false
            $("#btn").attr("disabled", true);
            $('#warning').show();
            $('#warning').html(`<div class="alert alert-warning small text-center" role="alert">Lokasi tidak sesuai.</div>`);
        }
    }

    function failure() {
        console.log("error");
    }
</script>
<?= $this->endSection(); ?>