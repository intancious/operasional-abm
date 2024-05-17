<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define('DOMPDF_ENABLE_PHP', true);
ob_get_clean();
ob_end_clean();

$request = \Config\Services::request();
$pengaturanModel = new \App\Models\SettingModel();
$userModel = new \App\Models\UserModel();
$pengaturan = $pengaturanModel->find(1);
$startDate = $request->getVar('startDate');
$endDate = $request->getVar('endDate');

function convBase64($imageName)
{
    $path = './assets/img/' . $imageName;
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

$user = $userModel->find($kasbon['tul_user']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= $title_bar; ?></title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <style>
        html {
            margin-top: 10px;
            margin-left: 15px;
            margin-right: 15px;
            margin-bottom: 10px;
            font-family: Lucida Sans Typewriter, Lucida Console, monaco, Bitstream Vera Sans Mono, monospace;
            font-size: 10px;
        }

        hr.style2 {
            margin-top: 5px;
            margin-bottom: 10px;
            border-top: 1px double #8c8b8b;
        }

        .page_break {
            page-break-before: always;
        }
    </style>
</head>

<body style="text-align: center;">
    <h2 style="margin-bottom: 0; margin-top: 0; font-size: 12px;"><?= strtoupper($pengaturan['setting_nama']); ?></h2>
    <hr class="style2" />
    <p>
        <b>KAS BON<br />NO. : <?= $trxupah['tu_nomor']; ?>/<?= $kasbon['tul_id']; ?></b>
    </p>
    <p>
        <b>NAMA TUKANG:</b> <?= strtoupper($tukang['tk_nama']); ?><br />
        <b>TANGGAL:</b> <?= date('d/m/Y H:i', strtotime($kasbon['tul_tanggal'])); ?><br />
        <b>NILAI KAS BON:</b> Rp. <?= number_format(str_replace(',', '.', $kasbon['tul_nominal']), 0, ',', '.'); ?><br />
        <b>KETERANGAN:</b><br /><?= $kasbon['tul_keterangan'] ? $kasbon['tul_keterangan'] : '-'; ?>
    </p>
    <p>
        <b>PETUGAS</b>
        <br />
        <br />
        <b><u><?= $user['usr_nama']; ?></u></b>
    </p>
</body>

</html>