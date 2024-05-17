<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define('DOMPDF_ENABLE_PHP', true);
ob_get_clean();
ob_end_clean();

$request = \Config\Services::request();
$barangModel = new \App\Models\BarangModel();
$unitModel = new \App\Models\UnitModel();
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
    <table width="100%">
        <tr>
            <td><b><?= strtoupper($title_bar); ?></b></td>
        </tr>
        <?php if ($request->getVar('jenis') == 'keluar' && $request->getVar('unit')) { ?>
            <tr>
                <td><b>UNIT :</b> <?= strtoupper($unitModel->find($request->getVar('unit'))['unit_nama']); ?></td>
            </tr>
        <?php } ?>
        <?php if ($request->getVar('startDate') && $request->getVar('endDate')) { ?>
            <tr>
                <td><b>TANGGAL :</b> <?= date('d/m/Y', strtotime($request->getVar('startDate'))); ?> - <?= date('d/m/Y', strtotime($request->getVar('endDate'))); ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td></td>
        </tr>
    </table>

    <table width="100%" border="1" cellspacing="0" cellpadding="5">
        <tr style="background-color: #eaecf4; font-weight: bold;">
            <th>#</th>
            <th>TANGGAL</th>
            <th>NAMA</th>
            <th>SATUAN</th>
            <?php if ($request->getVar('jenis') == 'keluar') { ?>
                <th>UNIT</th>
            <?php } ?>
            <th>JUMLAH</th>
            <th>HARGA</th>
            <th>NILAI</th>
        </tr>
        <?php
        $i = 1;
        $grandTotal = 0;
        if ($barang) {
            foreach ($barang as $row) :
                if ($request->getVar('jenis') == 'masuk') {
                    $qtymasuk = $row['pi_qtymasuk'] ? str_replace(',', '.', $row['pi_qtymasuk']) : 0;
                    $harga = $row['pi_harga'] ? str_replace(',', '.', $row['pi_harga']) : 0;
                    $subtotal = $qtymasuk * $harga;
                    $grandTotal += $subtotal;
        ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>
                        <td><?= strtoupper($row['barang_nama']); ?></td>
                        <td><?= strtoupper($row['satuan_nama']); ?></td>
                        <td><?= number_format($qtymasuk, 2, ',', '.'); ?></td>
                        <td align="right"><?= number_format($harga, 2, ',', '.'); ?></td>
                        <td align="right"><?= number_format($subtotal, 2, ',', '.'); ?></td>
                    </tr>
                <?php }
                if ($request->getVar('jenis') == 'keluar') {
                    $qty = $row['bki_qty'] ? str_replace(',', '.', $row['bki_qty']) : 0;
                    $harga = $barangModel->rataRataHarga($row['bki_barang']);
                    $subtotal = $qty * $harga;
                    $grandTotal += $subtotal;
                ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>
                        <td><?= strtoupper($row['barang_nama']); ?></td>
                        <td><?= strtoupper($row['satuan_nama']); ?></td>
                        <td><?= strtoupper($row['unit_nama']); ?></td>
                        <td><?= number_format($qty, 2, ',', '.'); ?></td>
                        <td align="right"><?= number_format($harga, 2, ',', '.'); ?></td>
                        <td align="right"><?= number_format($subtotal, 2, ',', '.'); ?></td>
                    </tr>
            <?php }
            endforeach;
        } else { ?>
            <tr>
                <td colspan="<?= $request->getVar('jenis') == 'keluar' ? '8' : '7'; ?>">Data belum tersedia.</td>
            </tr>
        <?php } ?>
        <tr style="background-color: #eaecf4; font-weight: bold;">
            <td colspan="<?= $request->getVar('jenis') == 'keluar' ? '7' : '6'; ?>">GRAND TOTAL</td>
            <td align="right"><?= number_format($grandTotal, 2, ',', '.'); ?></td>
        </tr>
    </table>
</body>

</html>