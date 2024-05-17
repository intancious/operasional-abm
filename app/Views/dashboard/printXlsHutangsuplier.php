<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=$title_bar.xls");
header("Pragma: no-cache");
header("Expires: 0");

$request = \Config\Services::request();
$pembelianModel = new \App\Models\PembelianModel();
$suplierModel = new \App\Models\SuplierModel();
$hbModel = new \App\Models\HutangsuplierBayarModel();
$userModel = new \App\Models\UserModel();
?>
<style>
    .str {
        mso-number-format: \@;
    }
</style>
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
<table border="1">
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
                <td class="str"><?= number_format($totalHutang, 2, ',', '.'); ?></td>
                <td class="str"><?= number_format($dibayar, 2, ',', '.'); ?></td>
                <td class="str"><?= number_format($sisa, 2, ',', '.'); ?></td>
                <td><?= $dibayar < $totalHutang ? 'BELUM LUNAS' : ($dibayar >= $totalHutang ? 'LUNAS' : ''); ?></td>
                <!-- <td><?= $row['hs_user'] ? $userModel->find($row['hs_user'])['usr_nama'] : '-'; ?></td> -->
            </tr>
        <?php }
    } else { ?>
        <tr>
            <td colspan="9" align="center">Data belum tersedia.</td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="4" style="background-color: #eaecf4; font-weight: bold;">GRAND TOTAL</td>
        <td style="background-color: #eaecf4; font-weight: bold;" class="str"><?= number_format($grandTotalPlusOngkir, 2, ',', '.'); ?></td>
        <td style="background-color: #eaecf4; font-weight: bold;" class="str"><?= number_format($grandTotalDibayar, 2, ',', '.'); ?></td>
        <td style="background-color: #eaecf4; font-weight: bold;" class="str"><?= number_format($grandTotalSisa, 2, ',', '.'); ?></td>
        <td colspan="1" style="background-color: #eaecf4; font-weight: bold;">&nbsp;</td>
    </tr>
</table>