<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=$title_bar.xls");
header("Pragma: no-cache");
header("Expires: 0");

$request = \Config\Services::request();
$barangModel = new \App\Models\BarangModel();
$unitModel = new \App\Models\UnitModel();
$pengaturanModel = new \App\Models\SettingModel();
$pengaturan = $pengaturanModel->find(1);
?>
<style>
    .str {
        mso-number-format: \@;
    }
</style>
<table>
    <tr>
        <td colspan="<?= $request->getVar('jenis') == 'keluar' ? '8' : '7'; ?>"><b><?= strtoupper($title_bar); ?></b></td>
    </tr>
    <?php if ($request->getVar('jenis') == 'keluar' && $request->getVar('unit')) { ?>
        <tr>
            <td colspan="<?= $request->getVar('jenis') == 'keluar' ? '8' : '7'; ?>"><b>UNIT :</b> <?= strtoupper($unitModel->find($request->getVar('unit'))['unit_nama']); ?></td>
        </tr>
    <?php } ?>
    <?php if ($request->getVar('startDate') && $request->getVar('endDate')) { ?>
        <tr>
            <td colspan="<?= $request->getVar('jenis') == 'keluar' ? '8' : '7'; ?>"><b>TANGGAL :</b> <?= date('d/m/Y', strtotime($request->getVar('startDate'))); ?> - <?= date('d/m/Y', strtotime($request->getVar('endDate'))); ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="<?= $request->getVar('jenis') == 'keluar' ? '8' : '7'; ?>"></td>
    </tr>
</table>

<table border="1">
    <tr>
        <td style="background-color: #eaecf4; font-weight: bold;">#</td>
        <td style="background-color: #eaecf4; font-weight: bold;">TANGGAL</td>
        <td style="background-color: #eaecf4; font-weight: bold;">NAMA</td>
        <td style="background-color: #eaecf4; font-weight: bold;">SATUAN</td>
        <?php if ($request->getVar('jenis') == 'keluar') { ?>
            <td style="background-color: #eaecf4; font-weight: bold;">UNIT</td>
        <?php } ?>
        <td style="background-color: #eaecf4; font-weight: bold;">JUMLAH</td>
        <td style="background-color: #eaecf4; font-weight: bold;">HARGA</td>
        <td style="background-color: #eaecf4; font-weight: bold;">NILAI</td>
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
                    <td class="str"><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>
                    <td><?= strtoupper($row['barang_nama']); ?></td>
                    <td><?= strtoupper($row['satuan_nama']); ?></td>
                    <td class="str"><?= number_format($qtymasuk, 2, ',', '.'); ?></td>
                    <td class="str"><?= number_format($harga, 2, ',', '.'); ?></td>
                    <td class="str"><?= number_format($subtotal, 2, ',', '.'); ?></td>
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
                    <td class="str"><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>
                    <td><?= strtoupper($row['barang_nama']); ?></td>
                    <td><?= strtoupper($row['satuan_nama']); ?></td>
                    <td><?= strtoupper($row['unit_nama']); ?></td>
                    <td class="str"><?= number_format($qty, 2, ',', '.'); ?></td>
                    <td class="str"><?= number_format($harga, 2, ',', '.'); ?></td>
                    <td class="str"><?= number_format($subtotal, 2, ',', '.'); ?></td>
                </tr>
        <?php }
        endforeach;
    } else { ?>
        <tr>
            <td colspan="<?= $request->getVar('jenis') == 'keluar' ? '8' : '7'; ?>">Data belum tersedia.</td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="<?= $request->getVar('jenis') == 'keluar' ? '7' : '6'; ?>" style="background-color: #eaecf4; font-weight: bold;">GRAND TOTAL</td>
        <td style="background-color: #eaecf4; font-weight: bold;" class="str"><?= number_format($grandTotal, 2, ',', '.'); ?></td>
    </tr>
</table>