<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define('DOMPDF_ENABLE_PHP', true);
ob_get_clean();
ob_end_clean();

$request = \Config\Services::request();
$pengaturanModel = new \App\Models\SettingModel();
$userModel = new \App\Models\UserModel();
$petugas = $userModel->find(session()->get('usr_id'));
$pengaturan = $pengaturanModel->find(1);
$terbilang = new \App\Libraries\Terbilang();

function convBase64($imageName)
{
    $path = './assets/img/' . $imageName;
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}
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
            /* font-size: 10px; */
        }

        hr.style2 {
            margin-top: 5px;
            margin-bottom: 10px;
            border-top: 3px double #8c8b8b;
        }

        .page_break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <table border="1" width="100%" cellspacing="0" cellpadding="10">
        <tr>
            <td width="25%" style="vertical-align:top;">
                <p><strong>Nomor:</strong> <?= $em['em_nomor']; ?></p>
                <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($em['em_tanggal'])); ?></p>
                <p><strong>Terima dari:</strong> <?= $em['em_nama']; ?></p>
                <p><strong>Jumlah:</strong> Rp <?= number_format($em['em_nominal'], 0, ',', '.'); ?></p>
                <p><strong>Untuk Pembayaran:</strong> Iuran Estate Management Bulan <?= date('m', strtotime($em['em_tanggal'])) . '/' . date('Y', strtotime($em['em_tanggal'])); ?></p>
            </td>
            <td width="75%" style="vertical-align:top;">
                <center>
                    <h3 style="margin-top: 10px;">KWITANSI PEMBAYARAN</h3>
                </center>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="left"><strong>Nomor:</strong> <?= $em['em_nomor']; ?></td>
                        <td align="right"><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($em['em_tanggal'])); ?></td>
                    </tr>
                </table>
                <p><strong>Terima dari:</strong> <?= $em['em_nama']; ?></p>
                <p><strong>Jumlah:</strong> Rp <?= number_format($em['em_nominal'], 0, ',', '.'); ?></p>
                <p><strong>Terbilang:</strong> <?= ucwords($terbilang->terbilang($em['em_nominal'])); ?> Rupiah</p>
                <p><strong>Untuk Pembayaran:</strong> Iuran Estate Management Bulan <?= date('m', strtotime($em['em_tanggal'])) . '/' . date('Y', strtotime($em['em_tanggal'])); ?></p>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td align="center">&nbsp;</td>
                        <td align="center">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center">................</td>
                        <td align="center">................</td>
                    </tr>
                    <tr>
                        <td align="center">Tanda Tangan Penerima</td>
                        <td align="center">Tanda Tangan Penyetor</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>