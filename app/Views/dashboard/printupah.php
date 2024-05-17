<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define('DOMPDF_ENABLE_PHP', true);
ob_get_clean();
ob_end_clean();

$request = \Config\Services::request();
$tukangModel = new \App\Models\TukangModel();
$userModel = new \App\Models\UserModel();
$trxupahItemModel = new \App\Models\TrxupahItemModel();
$trxupahLainModel = new \App\Models\TrxupahLainModel();
$pengaturanModel = new \App\Models\SettingModel();
$startDate = $request->getVar('startDate');
$endDate = $request->getVar('endDate');

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
            font-family: Lucida Sans Typewriter, Lucida Console, monaco, Bitstream Vera Sans Mono, monospace;
            font-size: 10px;
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
    <?php
    if ($trxupah) {
        foreach ($trxupah as $row) {
            $tukang = $tukangModel->find($row['tu_tukang']);
            $user = $userModel->find($row['tu_user']);
            $trxupahItem = $trxupahItemModel->where('tui_trxupah', $row['tu_id'])
                ->join('unit', 'unit.unit_id = trxupah_item.tui_unit', 'left')
                ->join('upah', 'upah.up_id = trxupah_item.tui_upah', 'left')
                ->select('trxupah_item.*, unit.unit_nama, upah.up_nama')
                ->findAll();

            $tglMulai = $trxupahItem[0]['tui_tanggal'];
            $tglAkhir = $trxupahItem[count($trxupahItem) - 1]['tui_tanggal'];

            $trxupahLain = $trxupahLainModel->where(['tul_trxupah' => $row['tu_id']])->findAll();
            $qtyKasbon = $trxupahLainModel->where(['tul_trxupah' => $row['tu_id'], 'tul_jenis' => 1])->countAllResults();
            $qtyLembur = $trxupahLainModel->where(['tul_trxupah' => $row['tu_id'], 'tul_jenis' => 2])->countAllResults();

            $grandTotal = 0;
            foreach ($trxupahItem as $item) {
                $grandTotal += str_replace(',', '.', $item['tui_total']);
            }

            $totalLembur = 0;
            $totalKasbon = 0;
            if ($trxupahLain) {
                foreach ($trxupahLain as $trxlain) {
                    if ($trxlain['tul_jenis'] == 1) {
                        $totalKasbon += str_replace(',', '.', $trxlain['tul_nominal']);
                    }
                    if ($trxlain['tul_jenis'] == 2) {
                        $totalLembur += str_replace(',', '.', $trxlain['tul_nominal']);
                    }
                }
            }
    ?>
            <!-- <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="vertical-align: top;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <img src="<?= convBase64($pengaturan['setting_logo2']); ?>" alt="<?= strtoupper($pengaturan['setting_nama']); ?>" width="200px">
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align: top;" align="right">
                        <h3 style="margin-bottom: 5px; margin-top: 0; font-size: 12px;">
                            BUKTI PEMBAYARAN<br />
                            NOMOR: <?= $row['tu_nomor']; ?>
                        </h3>
                    </td>
                </tr>
            </table>
            <hr class="style2" /> -->
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left"><span><b>NAMA TUKANG :</b> <?= strtoupper($tukang['tk_nama']); ?></span></td>
                    <td align="right"><span>JEMBER, <?= date('d/m/Y'); ?></span></td>
                </tr>
            </table>
            <br />
            <table border="1" width="100%" cellspacing="0" cellpadding="5">
                <tr style="background-color: #F0F0F0; font-weight: bold;">
                    <th>#</th>
                    <th>URAIAN</th>
                    <th>QTY</th>
                    <th>NOMINAL</th>
                </tr>
                <tr>
                    <td align="center">1</td>
                    <td>UPAH PEKERJAAN TGL. <?= date('d/m/Y', strtotime($tglMulai)); ?> - <?= date('d/m/Y', strtotime($tglAkhir)); ?></td>
                    <td align="center"><?= count($trxupahItem); ?></td>
                    <td align="right"><?= number_format($grandTotal, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td>UPAH LEMBUR</td>
                    <td align="center"><?= $qtyLembur; ?></td>
                    <td align="right"><?= number_format($totalLembur, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td>KAS BON</td>
                    <td align="center"><?= $qtyKasbon; ?></td>
                    <td align="right"><?= number_format($totalKasbon, 0, ',', '.') ?></td>
                </tr>
                <tr style="background-color: #F0F0F0; font-weight: bold;">
                    <td colspan="3">GRAND TOTAL</td>
                    <td align="right"><?= number_format(($grandTotal + $totalLembur) - $totalKasbon, 2, ',', '.') ?></td>
                </tr>
            </table>
            <!-- <table width="100%" cellspacing="5" cellpadding="0" align="center">
                <tr>
                    <td align="center" width="50%"><b>PETUGAS</b></td>
                    <td align="center" width="50%"><b>TUKANG</b></td>
                </tr>
                <tr>
                    <td align="center" width="50%">&nbsp;</td>
                    <td align="center" width="50%">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" width="50%">&nbsp;</td>
                    <td align="center" width="50%">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" width="50%"><b><u><?= strtoupper($user['usr_nama']); ?></u></b></td>
                    <td align="center" width="50%"><b><u><?= strtoupper($tukang['tk_nama']); ?></u></b></td>
                </tr>
            </table> -->
            <br />
    <?php }
    } ?>
</body>

</html>