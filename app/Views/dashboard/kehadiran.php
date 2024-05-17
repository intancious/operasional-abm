<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);

$checkInOutModel = new \App\Models\CheckInOutModel();
$absenModel = new \App\Models\AbsenModel();
$tanggalLibrary = new \App\Libraries\Tanggal();
$settingModel = new \App\Models\SettingModel();
$userModel = new \App\Models\UserModel();
$punModel = new \App\Models\PunishmentModel();
$setting = $settingModel->find(1);
$users = $userModel->orderBy('usr_nama', 'ASC')->findAll();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/kehadiran"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg">
        <div class="card shadow mb-4 bg-primary text-white">
            <div class="card-body">
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="startDate" class="small">TANGGAL MULAI</label>
                                <input type="text" name="startDate" id="startDate" class="form-control form-control-sm" value="<?= $request->getVar('startDate') ? $request->getVar('startDate') : date('d-m-Y', strtotime('-7 days')); ?>" placeholder="Pilih Tanggal">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="endDate" class="small">TANGGAL SAMPAI</label>
                                <input type="text" name="endDate" id="endDate" class="form-control form-control-sm" value="<?= $request->getVar('endDate') ? $request->getVar('endDate') : date('d-m-Y', strtotime('+7 days')); ?>" placeholder="Pilih Tanggal">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-light btn-sm mr-2"><i class="fas fa-sm fa-filter text-secondary mr-1"></i> FILTER</button>
                        <a class="btn btn-light btn-sm" href="/dashboard/rekapkehadiran?startDate=<?= $request->getVar('startDate') ? $request->getVar('startDate') : date('d-m-Y', strtotime('-7 days')); ?>&endDate=<?= $request->getVar('endDate') ? $request->getVar('endDate') : date('d-m-Y', strtotime('+7 days')); ?>">EXPORT XLS</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($request->getVar('startDate') && $request->getVar('endDate')) {
    $startDate = date('Y-m-d', strtotime($request->getVar('startDate')));
    $endDate = date('Y-m-d', strtotime($request->getVar('endDate')));
    $tanggal = $tanggalLibrary->list($startDate, $endDate);
?>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered small" id="customTable" style="width:100%">
                    <thead class="thead-light font-weight-bold">
                        <tr>
                            <th colspan="<?= count($tanggal) + 6; ?>">
                                <h5>REKAP KEHADIRAN</h5>
                                PER TANGGAL <?= date('d/m/Y', strtotime($request->getVar('startDate'))); ?> - <?= date('d/m/Y', strtotime($request->getVar('endDate'))); ?>
                            </th>
                        </tr>
                        <tr style="background-color: #eaecf4;">
                            <td>#</td>
                            <td>NAMA</td>
                            <?php foreach ($tanggal as $tgl) { ?>
                                <td align="center"><?= date('d', strtotime($tgl)); ?></td>
                            <?php } ?>
                            <td align="center">H</td>
                            <td align="center">I</td>
                            <td align="center">S</td>
                            <td align="center">PUNISHMENT</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($users) {
                            $i = 1;
                            foreach ($users as $user) { ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= strtoupper($user['usr_nama']); ?></td>
                                    <?php
                                    $punishmentTotal = 0;
                                    foreach ($tanggal as $tgl) {
                                        // absen yg di kantor
                                        $in = $checkInOutModel->getKehadiran('in', $user['usr_id'], date('Y-m-d', strtotime($tgl)));
                                        $out = $checkInOutModel->getKehadiran('out', $user['usr_id'], date('Y-m-d', strtotime($tgl)));
                                        $masuk = $in ? date('H:i', strtotime($in['created_at'])) : NULL;
                                        $pulang = $out ? date('H:i', strtotime($out['created_at'])) : NULL;
                                        $absen = $absenModel->getAbsen($user['usr_id'], date('Y-m-d', strtotime($tgl)));
                                        if ($in['ci_telat'] == 'yes') {
                                            $pun = $punModel->find($in['ci_punishment']);
                                            $punishmentTotal += $pun['pun_potongan'] ? str_replace(',', '.', $pun['pun_potongan']) : 0;
                                        }
                                    ?>
                                        <td align="center">
                                            <?php
                                            if ($masuk && $pulang) {
                                                if ($in['ci_telat'] == 'yes') {
                                                    echo '<a href="#" class="text-decoration-none absenKantor" data-toggle="modal" data-target="#detailAbsenKantor" data-id="' . $user['usr_id'] . '" data-tanggal="' . date('Y-m-d', strtotime($tgl)) . '"><span style="color:red;">' . date('H:i', strtotime($masuk)) . ' - ' . date('H:i', strtotime($pulang)) . '</span></a>';
                                                } else {
                                                    echo '<a href="#" class="text-decoration-none absenKantor" data-toggle="modal" data-target="#detailAbsenKantor" data-id="' . $user['usr_id'] . '" data-tanggal="' . date('Y-m-d', strtotime($tgl)) . '">' . date('H:i', strtotime($masuk)) . ' - ' . date('H:i', strtotime($pulang)) . '</a>';
                                                }
                                            } else {
                                                if ($absen) {
                                                    echo strtoupper($absen['ab_jenis']);
                                                } else {
                                                    if ($masuk && !$pulang) {
                                                        if ($in['ci_telat'] == 'yes') {
                                                            echo '<a href="#" class="text-decoration-none absenKantor" data-toggle="modal" data-target="#detailAbsenKantor" data-id="' . $user['usr_id'] . '" data-tanggal="' . date('Y-m-d', strtotime($tgl)) . '"><span style="color:red;">' . date('H:i', strtotime($masuk)) . ' - NULL</span><a/>';
                                                        } else {
                                                            echo '<a href="#" class="text-decoration-none absenKantor" data-toggle="modal" data-target="#detailAbsenKantor" data-id="' . $user['usr_id'] . '" data-tanggal="' . date('Y-m-d', strtotime($tgl)) . '">' . date('H:i', strtotime($masuk)) . ' - NULL</a>';
                                                        }
                                                    } else if (!$masuk && $pulang) {
                                                        echo '<a href="#" class="text-decoration-none absenKantor" data-toggle="modal" data-target="#detailAbsenKantor" data-id="' . $user['usr_id'] . '" data-tanggal="' . date('Y-m-d', strtotime($tgl)) . '"><span style="color:red;">NULL - ' . date('H:i', strtotime($pulang)) . '</span></a>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </td>
                                    <?php } ?>
                                    <td align="center" style="vertical-align: top;">
                                        <?= $checkInOutModel->countHadir($user['usr_id'], $request->getVar('startDate'), $request->getVar('endDate')); ?>
                                    </td>
                                    <td align="center" style="vertical-align: top;"><?= $absenModel->countAbsen($user['usr_id'], 'ijin', $request->getVar('startDate'), $request->getVar('endDate')); ?></td>
                                    <td align="center" style="vertical-align: top;"><?= $absenModel->countAbsen($user['usr_id'], 'sakit', $request->getVar('startDate'), $request->getVar('endDate')); ?></td>
                                    <td><?= number_format($punishmentTotal, 0, ',', '.'); ?></td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailAbsenKantor" tabindex="-1" role="dialog" aria-labelledby="detailAbsenKantorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-uppercase" id="detailAbsenKantorLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="mb-3">CHECKIN</h5>
                    <div class="form-group">
                        <label for="tanggalKantor">Tanggal & Waktu</label>
                        <input type="text" name="tanggalKantor" id="tanggalKantor" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label for="keteranganKantor">Deskripsi</label>
                        <textarea disabled name="keteranganKantor" id="keteranganKantor" rows="5" class="form-control"></textarea>
                    </div>

                    <div class="row bg-light" id="telat">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="terlambat">Keterlambatan</label>
                                <input type="text" name="terlambat" id="terlambat" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="punishment">Punishment</label>
                                <input type="text" name="punishment" id="punishment" class="form-control" disabled>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">CHECKOUT</h5>
                    <div class="form-group">
                        <label for="tanggalOutKantor">Tanggal & Waktu</label>
                        <input type="text" name="tanggalOutKantor" id="tanggalOutKantor" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label for="keteranganOutKantor">Deskripsi</label>
                        <textarea disabled name="keteranganOutKantor" id="keteranganOutKantor" rows="5" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?= $this->endSection(); ?>