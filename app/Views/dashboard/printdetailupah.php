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
    <style>
        header {
            position: fixed;
            width: 100%;
        }

        html {
            margin: 15px
        }

        * {
            font-family: 'Arial';
            font-size: small;
        }

        hr.style2 {
            margin-top: 5px;
            margin-bottom: 10px;
            border-top: 3px double #8c8b8b;
        }

        .typeWriter {
            font-family: Lucida Sans Typewriter, Lucida Console, monaco, Bitstream Vera Sans Mono, monospace;
        }

        div.page_break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <?php
    $tukang = $tukangModel->find($trxupah['tu_tukang']);
    $user = $userModel->find($trxupah['tu_user']);
    ?>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td style="vertical-align: top;">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <h2 style="margin-bottom: 0; margin-top: 0; font-size: 20px;"><?= strtoupper($pengaturan['setting_nama']); ?></h2>
                            <!-- <img src="<?= convBase64($pengaturan['setting_logo']); ?>" alt="<?= strtoupper($pengaturan['setting_nama']); ?>" width="200px"> -->
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;" align="right">
                <h2 style="margin-bottom: 5px; margin-top: 0; font-size: 20px;">
                    SLIP GAJI
                </h2>
            </td>
        </tr>
    </table>
    <hr class="style2" />

    <table width="100%" cellspacing="5" cellpadding="0">
        <tr>
            <td><b>NO. FAKTUR</b></td>
            <td>: <?= strtoupper($trxupah['tu_nomor']); ?></td>
            <td><b>TGL. FAKTUR</b></td>
            <td>: <?= date('d/m/Y', strtotime($trxupah['tu_tanggal'])); ?></td>
        </tr>
        <tr>
            <td><b>ATAS NAMA</b></td>
            <td>: <?= strtoupper($tukang['tk_nama']); ?></td>
            <td><b>KETERANGAN</b></td>
            <td>: <?= $trxupah['tu_keterangan'] ? strtoupper($trxupah['tu_keterangan']) : '-'; ?></td>
        </tr>

    </table>

    <p style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">UPAH PEKERJAAN</p>
    <table border="1" width="100%" cellspacing="0" cellpadding="5">
        <tr style="background-color: #F0F0F0; font-weight: bold;">
            <th class="typeWriter">#</th>
            <th class="typeWriter">TANGGAL</th>
            <th class="typeWriter">UNIT</th>
            <th class="typeWriter">UPAH</th>
            <th class="typeWriter">JUMLAH</th>
            <th class="typeWriter">NILAI</th>
            <th class="typeWriter">TOTAL</th>
        </tr>
        <?php
        $grandTotal = 0;
        if ($trxupahItem) {
            $i = 1;
            foreach ($trxupahItem as $item) {
                $grandTotal += str_replace(',', '.', $item['tui_total']);
        ?>
                <tr>
                    <td class="typeWriter"><?= $i++; ?></td>
                    <td class="typeWriter"><?= date('d/m/Y', strtotime($item['tui_tanggal'])); ?></td>
                    <td class="typeWriter"><?= strtoupper($item['unit_nama']); ?></td>
                    <td class="typeWriter"><?= strtoupper($item['up_nama']); ?></td>
                    <td class="typeWriter" align="center"><?= strtoupper($item['tui_jumlah']); ?></td>
                    <td class="typeWriter" align="right"><?= number_format(str_replace(',', '.', $item['tui_nilai']), 0, ',', '.'); ?></td>
                    <td class="typeWriter" align="right"><?= number_format(str_replace(',', '.', $item['tui_total']), 0, ',', '.'); ?></td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="7" class="typeWriter">&nbsp;</td>
            </tr>
        <?php } ?>
        <tr style="background-color: #F0F0F0; font-weight: bold;">
            <td colspan="6" class="typeWriter">TOTAL UPAH</td>
            <td class="typeWriter" align="right"><?= number_format($grandTotal, 0, ',', '.') ?></td>
        </tr>
    </table>
    <p style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">LEMBUR DAN KAS BON</p>
    <table border="1" width="100%" cellspacing="0" cellpadding="5">
        <tr style="background-color: #F0F0F0; font-weight: bold;">
            <th class="typeWriter">#</th>
            <th class="typeWriter">TANGGAL</th>
            <th class="typeWriter">TRANSAKSI</th>
            <th class="typeWriter">NOMINAL</th>
        </tr>
        <?php
        $totalLembur = 0;
        $totalKasbon = 0;
        if ($trxupahLain) {
            $kasKecilModel = new \App\Models\KasKecilModel();
            $koderekeningModel = new \App\Models\KoderekeningModel();

            $i = 1;
            foreach ($trxupahLain as $row) {
                if ($row['tul_jenis'] == 1) {
                    $totalKasbon += str_replace(',', '.', $row['tul_nominal']);
                }
                if ($row['tul_jenis'] == 2) {
                    $totalLembur += str_replace(',', '.', $row['tul_nominal']);
                }

                $kasKecilbon = $kasKecilModel->where('kk_id', $row['tul_kaskecil'])
                    ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
                    ->select('kas_kecil.*, user.usr_nama')->first();
                $rekeningDebet = $koderekeningModel->find($row['tul_debet']);
                $rekeningKredit = $koderekeningModel->find($row['tul_kredit']);
        ?>
                <tr>
                    <td class="typeWriter"><?= $i++; ?></td>
                    <td class="typeWriter"><?= date('d/m/Y', strtotime($row['tul_tanggal'])); ?></td>
                    <td class="typeWriter"><?= $row['tul_jenis'] == 1 ? 'KAS BON' : ($row['tul_jenis'] == 2 ? 'LEMBUR' : ''); ?></td>
                    <td class="typeWriter" align="right"><?= number_format(str_replace(',', '.', $row['tul_nominal']), 0, ',', '.'); ?></td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="4" class="typeWriter">&nbsp;</td>
            </tr>
        <?php } ?>
        <tr style="background-color: #F0F0F0; font-weight: bold;">
            <td colspan="3" class="typeWriter">TOTAL LEMBUR</td>
            <td class="typeWriter" align="right"><?= number_format($totalLembur, 0, ',', '.') ?></td>
        </tr>
        <tr style="background-color: #F0F0F0; font-weight: bold;">
            <td colspan="3" class="typeWriter">TOTAL KAS BON</td>
            <td class="typeWriter" align="right"><?= number_format($totalKasbon, 0, ',', '.') ?></td>
        </tr>
    </table>
    <p style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">RINCIAN PEMBAYARAN</p>
    <table border="1" width="100%" cellspacing="0" cellpadding="5">
        <tr style="background-color: #F0F0F0; font-weight: bold;">
            <th class="typeWriter">#</th>
            <th class="typeWriter">KETERANGAN</th>
            <th class="typeWriter">NOMINAL</th>
        </tr>
        <tr>
            <td class="typeWriter">1</td>
            <td class="typeWriter">UPAH PEKERJAAN</td>
            <td class="typeWriter" align="right"><?= number_format($grandTotal, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="typeWriter">2</td>
            <td class="typeWriter">UPAH LEMBUR</td>
            <td class="typeWriter" align="right"><?= number_format($totalLembur, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="typeWriter">3</td>
            <td class="typeWriter">KAS BON</td>
            <td class="typeWriter" align="right"><?= number_format($totalKasbon, 0, ',', '.') ?></td>
        </tr>
        <tr style="background-color: #F0F0F0; font-weight: bold;">
            <td colspan="2" class="typeWriter">GRAND TOTAL</td>
            <td class="typeWriter" align="right"><?= number_format(($grandTotal + $totalLembur) - $totalKasbon, 2, ',', '.') ?></td>
        </tr>
    </table>
    <br />
    <table width="100%" cellspacing="5" cellpadding="0" align="center">
        <tr>
            <td align="center"><b>PETUGAS</b></td>
            <td align="center"><b>TUKANG</b></td>
        </tr>
        <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
        </tr>
        <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
        </tr>
        <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
        </tr>
        <tr>
            <td align="center"><b><u><?= strtoupper($user['usr_nama']); ?></u></b></td>
            <td align="center"><b><u><?= strtoupper($tukang['tk_nama']); ?></u></b></td>
        </tr>
    </table>
</body>

</html>