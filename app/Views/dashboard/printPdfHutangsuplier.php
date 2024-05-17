<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define('DOMPDF_ENABLE_PHP', true);
ob_get_clean();
ob_end_clean();

$request = \Config\Services::request();
$pembelianModel = new \App\Models\PembelianModel();
$suplierModel = new \App\Models\SuplierModel();
$hbModel = new \App\Models\HutangsuplierBayarModel();
$userModel = new \App\Models\UserModel();

$pengaturanModel = new \App\Models\SettingModel();
$pengaturan = $pengaturanModel->find(1);

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
            /* font-family: Arial, Helvetica, sans-serif; */
            font-size: 12px;
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
    <table>
        <tr>
            <td colspan="9"><b><?= strtoupper($title_bar); ?></b></td>
        </tr>
        <?php if ($request->getVar('supplier')) { ?>
            <tr>
                <td colspan="9"><b>SUPLIER :</b> <?= strtoupper($suplierModel->find($request->getVar('supplier'))['suplier_nama']); ?></td>
            </tr>
        <?php } ?>
        <?php if ($request->getVar('startDate') && $request->getVar('endDate')) { ?>
            <tr>
                <td colspan="9"><b>TANGGAL :</b> <?= date('d/m/Y', strtotime($request->getVar('startDate'))); ?> - <?= date('d/m/Y', strtotime($request->getVar('endDate'))); ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="9"></td>
        </tr>
    </table>
    <table border="1" width="100%" cellspacing="0" cellpadding="5">
        <tr>
            <td style="background-color: #eaecf4; font-weight: bold;">#</ts>
            <td style="background-color: #eaecf4; font-weight: bold;">TANGGAL</td>
            <td style="background-color: #eaecf4; font-weight: bold;">NO. FAKTUR</td>
            <td style="background-color: #eaecf4; font-weight: bold;">NO. PEMBELIAN</td>
            <td style="background-color: #eaecf4; font-weight: bold;">SUPLIER</td>
            <td style="background-color: #eaecf4; font-weight: bold;">TOTAL HUTANG</td>
            <td style="background-color: #eaecf4; font-weight: bold;">DIBAYAR</td>
            <td style="background-color: #eaecf4; font-weight: bold;">SISA HUTANG</td>
            <td style="background-color: #eaecf4; font-weight: bold;">STATUS</th>
            <!-- <td style="background-color: #eaecf4; font-weight: bold;">USER</td> -->
        </tr>
        <?php
        $grandTotal = 0;
        $grandTotalOngkir = 0;
        $grandTotalPlusOngkir = 0;
        $grandTotalDibayar = 0;
        $grandTotalSisa = 0;
        if ($hutangsuplier) {
            $i = 1;
            foreach ($hutangsuplier as $row) {
                $pembelian = $pembelianModel->find($row['hs_pembelian']);
                $ongkir = $pembelian['pb_ongkir'] ? str_replace(',', '.', $pembelian['pb_ongkir']) : 0;
                $total = $row['hs_total'] ? str_replace(',', '.', $row['hs_total']) : 0;
                $totalHutang = $total + $ongkir;

                $grandTotal += $total;
                $grandTotalOngkir += $ongkir;
                $grandTotalPlusOngkir += $totalHutang;

                $dibayar = $hbModel->totalBayar($row['hs_id']);
                $grandTotalDibayar += $dibayar;

                $sisa = $totalHutang - $dibayar;
                $grandTotalSisa += $sisa;
        ?>
                <tr>
                    <td><?= $i++; ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['hs_tanggal'])); ?></td>
                    <td><?= $row['hs_nomor']; ?></td>
                    <td><?= $row['pb_nomor']; ?></td>
                    <td><?= strtoupper($row['suplier_nama']); ?></td>
                    <td class="str" align="right"><?= number_format($totalHutang, 2, ',', '.'); ?></td>
                    <td class="str" align="right"><?= number_format($dibayar, 2, ',', '.'); ?></td>
                    <td class="str" align="right"><?= number_format($sisa, 2, ',', '.'); ?></td>
                    <td><?= $dibayar < $totalHutang ? 'BELUM LUNAS' : ($dibayar >= $totalHutang ? 'LUNAS' : ''); ?></td>
                    <!-- <td><?= $row['hs_user'] ? strtoupper($userModel->find($row['hs_user'])['usr_nama']) : '-'; ?></td> -->
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="10" align="center">Data belum tersedia.</td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="5" style="background-color: #eaecf4; font-weight: bold;">GRAND TOTAL</td>
            <td style="background-color: #eaecf4; font-weight: bold;" class="str" align="right"><?= number_format($grandTotalPlusOngkir, 2, ',', '.'); ?></td>
            <td style="background-color: #eaecf4; font-weight: bold;" class="str" align="right"><?= number_format($grandTotalDibayar, 2, ',', '.'); ?></td>
            <td style="background-color: #eaecf4; font-weight: bold;" class="str" align="right"><?= number_format($grandTotalSisa, 2, ',', '.'); ?></td>
            <td colspan="1" style="background-color: #eaecf4; font-weight: bold;">&nbsp;</td>
        </tr>
    </table>
</body>

</html>