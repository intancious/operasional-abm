<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=$title_bar.xls");
header("Pragma: no-cache");
header("Expires: 0");

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

if ($request->getVar('startDate') && $request->getVar('endDate')) {
    $startDate = date('Y-m-d', strtotime($request->getVar('startDate')));
    $endDate = date('Y-m-d', strtotime($request->getVar('endDate')));
    $tanggal = $tanggalLibrary->list($startDate, $endDate);
?>
    <style>
        .str {
            mso-number-format: \@;
        }
    </style>
    <table border="1">
        <tr>
            <td colspan="<?= count($tanggal) + 6; ?>">REKAP KEHADIRAN</td>
        </tr>
        <tr>
            <td colspan="<?= count($tanggal) + 6; ?>">PER TANGGAL <?= date('d/m/Y', strtotime($request->getVar('startDate'))); ?> - <?= date('d/m/Y', strtotime($request->getVar('endDate'))); ?></td>
        </tr>
        <tr style="background-color: #eaecf4;">
            <td>#</td>
            <td>NAMA</td>
            <?php foreach ($tanggal as $tgl) { ?>
                <td align="center" class="str"><?= date('d', strtotime($tgl)); ?></td>
            <?php } ?>
            <td align="center">H</td>
            <td align="center">I</td>
            <td align="center">S</td>
            <td align="center">PUNISHMENT</td>
        </tr>
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
                        <td align="center" class="str">
                            <?php
                            if ($masuk && $pulang) {
                                if ($in['ci_telat'] == 'yes') {
                                    echo '<span style="color:red;">' . date('H:i', strtotime($masuk)) . ' - ' . date('H:i', strtotime($pulang)) . '</span>';
                                } else {
                                    echo '' . date('H:i', strtotime($masuk)) . ' - ' . date('H:i', strtotime($pulang)) . '</a>';
                                }
                            } else {
                                if ($absen) {
                                    echo strtoupper($absen['ab_jenis']);
                                } else {
                                    if ($masuk && !$pulang) {
                                        if ($in['ci_telat'] == 'yes') {
                                            echo '<span style="color:red;">' . date('H:i', strtotime($masuk)) . ' - NULL</span><a/>';
                                        } else {
                                            echo '' . date('H:i', strtotime($masuk)) . ' - NULL</a>';
                                        }
                                    } else if (!$masuk && $pulang) {
                                        echo '<span style="color:red;">NULL - ' . date('H:i', strtotime($pulang)) . '</span>';
                                    }
                                }
                            }
                            ?>
                        </td>
                    <?php } ?>
                    <td align="center" style="vertical-align: top;">
                        <?= $checkInOutModel->countHadir($user['usr_id'], $request->getVar('startDate'), $request->getVar('endDate')); ?>
                    </td>
                    <td align="center" style="vertical-align: top;" class="str"><?= $absenModel->countAbsen($user['usr_id'], 'ijin', $request->getVar('startDate'), $request->getVar('endDate')); ?></td>
                    <td align="center" style="vertical-align: top;" class="str"><?= $absenModel->countAbsen($user['usr_id'], 'sakit', $request->getVar('startDate'), $request->getVar('endDate')); ?></td>
                    <td><?= number_format($punishmentTotal, 0, ',', '.'); ?></td>
                </tr>
    <?php }
        }
    } ?>
    </table>