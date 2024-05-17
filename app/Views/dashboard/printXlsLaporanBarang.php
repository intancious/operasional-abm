<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=$title_bar.xls");
header("Pragma: no-cache");
header("Expires: 0");

$request = \Config\Services::request();
$pembelianItemModel = new \App\Models\PembelianItemModel();
$barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
?>
<style>
    .str {
        mso-number-format: \@;
    }
</style>
<table>
    <tr>
        <td colspan="8"><b><?= strtoupper($title_bar); ?></b></td>
    </tr>
    <?php if ($request->getVar('kategori')) { ?>
        <tr>
            <td colspan="8"><b>KATEGORI :</b> <?= strtoupper($kabarModel->find($request->getVar('kategori'))['kabar_nama']); ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="8"></td>
    </tr>
</table>
<table border="1">
    <tr>
        <td colspan="4" class="text-center" style="background-color: #eaecf4;"><b>BAHAN</b></td>
        <td colspan="3" class="text-center" style="background-color: #eaecf4;"><b>STOK AWAL</b></td>
        <td colspan="3" class="text-center" style="background-color: #eaecf4;"><b>PEMBELIAN</b></td>
        <td colspan="3" class="text-center" style="background-color: #eaecf4;"><b>PEMAKAIAN</b></td>
        <td colspan="3" class="text-center" style="background-color: #eaecf4;"><b>STOK AKHIR</b></td>
    </tr>
    <tr>
        <td class="text-center" style="background-color: #eaecf4;"><b>#</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>NAMA BARANG</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>SATUAN</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>KATEGORI</b></td>

        <td class="text-center" style="background-color: #eaecf4;"><b>QTY</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>HARGA</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>NILAI</b></td>

        <td class="text-center" style="background-color: #eaecf4;"><b>QTY</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>HARGA</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>NILAI</b></td>

        <td class="text-center" style="background-color: #eaecf4;"><b>QTY</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>HARGA</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>NILAI</b></td>

        <td class="text-center" style="background-color: #eaecf4;"><b>QTY</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>HARGA</b></td>
        <td class="text-center" style="background-color: #eaecf4;"><b>NILAI</b></td>
    </tr>
    <?php
    $i = 1;
    $grandTotalAwal = 0;
    $grandTotalBeli = 0;
    $grandTotalPakai = 0;
    $grandTotalAkhir = 0;
    if ($barang) {
        foreach ($barang as $row) :
            $stokAwal = $row['barang_jumlah'] ? str_replace(',', '.', $row['barang_jumlah']) : 0;
            $hargaAwal = $row['barang_harga'] ? str_replace(',', '.', $row['barang_harga']) : 0;
            $subtotalAwal = $stokAwal * $hargaAwal;
            $grandTotalAwal += $subtotalAwal;

            $pembelian = $pembelianItemModel->itemPembelian($row['barang_id']);
            $grandTotalBeli += $pembelian['total'];

            $barangkeluar = $barangKeluarItemModel->itemKeluar($row['barang_id']);
            $grandTotalPakai += $barangkeluar['total'];

            $stokSaatIni = $barangModel->getStokSaatIni($row['barang_id']);

            $rataRataHarga = $barangModel->rataRataHarga($row['barang_id']);
            $subtotalAkhir = $stokSaatIni * $rataRataHarga;
            $grandTotalAkhir += $subtotalAkhir;
            if ($inStok) {
                if ($stokSaatIni > 0) { ?>
                    <tr>
                        <td align="center"><?= $i++; ?></td>
                        <td class="text-uppercase <?= $stokSaatIni <= $row['barang_minstok'] ? 'text-danger' : ''; ?>"><?= $row['barang_nama'] ? strtoupper($row['barang_nama']) : '-'; ?></td>
                        <td align="center" class="text-uppercase"><?= $row['barang_satuan'] ? strtoupper($row['satuan_nama']) : ''; ?></td>
                        <td class="text-uppercase"><?= $row['barang_kategori'] ? strtoupper($row['kabar_nama']) : '-'; ?></td>

                        <td align="center" style="background-color: #eaecf4;" class="str"><?= ($row['barang_jumlah'] ? number_format($row['barang_jumlah'], 2, ',', '.') : 0); ?></td>
                        <td align="right" style="background-color: #eaecf4;" class="str"><?= number_format($row['barang_harga'], 2, ',', '.'); ?></td>
                        <td align="right" style="background-color: #eaecf4;" class="str"><?= number_format($subtotalAwal, 2, ',', '.'); ?></td>

                        <td align="center" class="str"><?= number_format($pembelian['qty'], 2, ',', '.'); ?></td>
                        <td align="right" class="str"><?= number_format($pembelian['harga'], 2, ',', '.'); ?></td>
                        <td align="right" class="str"><?= number_format($pembelian['total'], 2, ',', '.'); ?></td>

                        <td align="center" style="background-color: #eaecf4;" class="str"><?= number_format($barangkeluar['qty'], 2, ',', '.'); ?></td>
                        <td align="right" style="background-color: #eaecf4;" class="str"><?= number_format($barangkeluar['harga'], 2, ',', '.'); ?></td>
                        <td align="right" style="background-color: #eaecf4;" class="str"><?= number_format($barangkeluar['total'], 2, ',', '.'); ?></td>

                        <td align="center" class="str"><?= number_format($stokSaatIni, 2, ',', '.'); ?></td>
                        <td align="right" class="str"><?= number_format($rataRataHarga, 2, ',', '.'); ?></td>
                        <td align="right" class="str"><?= number_format($subtotalAkhir, 2, ',', '.'); ?></td>
                    </tr>
                <?php }
            } else {
                ?>
                <tr>
                    <td align="center"><?= $i++; ?></td>
                    <td class="text-uppercase <?= $stokSaatIni <= $row['barang_minstok'] ? 'text-danger' : ''; ?>"><?= $row['barang_nama'] ? strtoupper($row['barang_nama']) : '-'; ?></td>
                    <td align="center" class="text-uppercase"><?= $row['barang_satuan'] ? strtoupper($row['satuan_nama']) : ''; ?></td>
                    <td class="text-uppercase"><?= $row['barang_kategori'] ? strtoupper($row['kabar_nama']) : '-'; ?></td>

                    <td align="center" style="background-color: #eaecf4;" class="str"><?= ($row['barang_jumlah'] ? number_format($row['barang_jumlah'], 2, ',', '.') : 0); ?></td>
                    <td align="right" style="background-color: #eaecf4;" class="str"><?= number_format($row['barang_harga'], 2, ',', '.'); ?></td>
                    <td align="right" style="background-color: #eaecf4;" class="str"><?= number_format($subtotalAwal, 2, ',', '.'); ?></td>

                    <td align="center" class="str"><?= number_format($pembelian['qty'], 2, ',', '.'); ?></td>
                    <td align="right" class="str"><?= number_format($pembelian['harga'], 2, ',', '.'); ?></td>
                    <td align="right" class="str"><?= number_format($pembelian['total'], 2, ',', '.'); ?></td>

                    <td align="center" style="background-color: #eaecf4;" class="str"><?= number_format($barangkeluar['qty'], 2, ',', '.'); ?></td>
                    <td align="right" style="background-color: #eaecf4;" class="str"><?= number_format($barangkeluar['harga'], 2, ',', '.'); ?></td>
                    <td align="right" style="background-color: #eaecf4;" class="str"><?= number_format($barangkeluar['total'], 2, ',', '.'); ?></td>

                    <td align="center" class="str"><?= number_format($stokSaatIni, 2, ',', '.'); ?></td>
                    <td align="right" class="str"><?= number_format($rataRataHarga, 2, ',', '.'); ?></td>
                    <td align="right" class="str"><?= number_format($subtotalAkhir, 2, ',', '.'); ?></td>
                </tr>
        <?php }
        endforeach;
    } else { ?>
        <tr>
            <td colspan="10" class="text-center font-italic">Data belum tersedia.</td>
        </tr>
    <?php } ?>
    <tr style="background-color: #eaecf4;">
        <td colspan="4" class="text-center" style="background-color: #eaecf4;"><b>GRAND TOTAL</b></td>
        <td colspan="2" class="text-center" style="background-color: #eaecf4;"><b>STOK AWAL</b></td>
        <td align="right" class="str" style="background-color: #eaecf4;"><b><?= number_format($grandTotalAwal, 2, ',', '.'); ?></b></td>
        <td colspan="2" class="text-center" style="background-color: #eaecf4;"><b>PEMBELIAN</b></td>
        <td align="right" class="str" style="background-color: #eaecf4;"><b><?= number_format($grandTotalBeli, 2, ',', '.'); ?></b></td>
        <td colspan="2" class="text-center" style="background-color: #eaecf4;"><b>PEMAKAIAN</b></td>
        <td align="right" class="str" style="background-color: #eaecf4;"><b><?= number_format($grandTotalPakai, 2, ',', '.'); ?></b></td>
        <td colspan="2" class="text-center" style="background-color: #eaecf4;"><b>STOK AKHIR</b></td>
        <td align="right" class="str" style="background-color: #eaecf4;"><b><?= number_format($grandTotalAkhir, 2, ',', '.'); ?></b></td>
    </tr>
</table>