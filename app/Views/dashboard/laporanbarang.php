<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$pembelianItemModel = new \App\Models\PembelianItemModel();
$barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/laporanbarang"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4 bg-primary text-white">
            <div class="card-body">
                <form action="" method="get">
                    <label for="kategori" class="ml-1 small">KATEGORI BARANG</label>
                    <div class="input-group">
                        <select name="kategori" id="kategori" class="form-control form-control-sm selectpicker <?= $validation->hasError('kategori') ? 'is-invalid' : ''; ?>" data-live-search="true">
                            <option value="" data-tokens="">:: SEMUA ::</option>
                            <?php
                            $kabarModel = new \App\Models\KabarModel();
                            $kabar = $kabarModel->orderBy('kabar_nama', 'ASC')->findAll();
                            foreach ($kabar as $row) { ?>
                                <option value="<?= $row['kabar_id']; ?>" data-tokens="<?= $row['kabar_nama']; ?>" <?= $request->getVar('kategori') == $row['kabar_id'] ? 'selected' : ''; ?>><?= strtoupper($row['kabar_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-light btn-sm mr-2" type="submit"><i class="fas fa-sm fa-filter text-secondary mr-1"></i> FILTER</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <?= session()->get('pesan'); ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <a href="/dashboard/printXlsLaporanBarang<?= $request->uri->getQuery() ? '?' . $request->uri->getQuery() : ''; ?>" target="_blank" class="btn btn-primary btn-sm float-right"><i class="fas fa-file-excel mr-2"></i> PRINT SEMUA</a>
                        <a href="/dashboard/printXlsLaporanBarang/1<?= $request->uri->getQuery() ? '?' . $request->uri->getQuery() : ''; ?>" target="_blank" class="btn btn-primary btn-sm float-right mr-2"><i class="fas fa-file-excel mr-2"></i> PRINT STOK</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered small" id="customFixedTable" style="width:100%">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <th colspan="4" class="text-center">BAHAN</th>
                                <th colspan="3" class="text-center">STOK AWAL</th>
                                <th colspan="3" class="text-center">PEMBELIAN</th>
                                <th colspan="3" class="text-center">PEMAKAIAN</th>
                                <th colspan="3" class="text-center">STOK AKHIR</th>
                            </tr>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">NAMA BARANG</th>
                                <th class="text-center">SATUAN</th>
                                <th class="text-center">KATEGORI</th>

                                <th class="text-center">QTY</th>
                                <th class="text-center">HARGA</th>
                                <th class="text-center">NILAI</th>

                                <th class="text-center">QTY</th>
                                <th class="text-center">HARGA</th>
                                <th class="text-center">NILAI</th>

                                <th class="text-center">QTY</th>
                                <th class="text-center">HARGA</th>
                                <th class="text-center">NILAI</th>

                                <th class="text-center">QTY</th>
                                <th class="text-center">HARGA</th>
                                <th class="text-center">NILAI</th>
                            </tr>
                        </thead>
                        <tbody>
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
                            ?>
                                    <tr>
                                        <td align="center"><?= $i++; ?></td>
                                        <td class="text-uppercase <?= $stokSaatIni <= $row['barang_minstok'] ? 'text-danger' : ''; ?>"><?= $row['barang_nama'] ? strtoupper($row['barang_nama']) : '-'; ?></td>
                                        <td align="center" class="text-uppercase"><?= $row['barang_satuan'] ? strtoupper($row['satuan_nama']) : ''; ?></td>
                                        <td class="text-uppercase"><?= $row['barang_kategori'] ? strtoupper($row['kabar_nama']) : '-'; ?></td>

                                        <td align="center" style="background-color: #eaecf4; border-color: #eaecf4;"><?= ($row['barang_jumlah'] ? number_format($row['barang_jumlah'], 2, ',', '.') : 0); ?></td>
                                        <td align="right" style="background-color: #eaecf4; border-color: #eaecf4;"><?= number_format($row['barang_harga'], 2, ',', '.'); ?></td>
                                        <td align="right" style="background-color: #eaecf4; border-color: #eaecf4;"><?= number_format($subtotalAwal, 2, ',', '.'); ?></td>

                                        <td align="center"><?= number_format($pembelian['qty'], 2, ',', '.'); ?></td>
                                        <td align="right"><?= number_format($pembelian['harga'], 2, ',', '.'); ?></td>
                                        <td align="right"><?= number_format($pembelian['total'], 2, ',', '.'); ?></td>

                                        <td align="center" style="background-color: #eaecf4; border-color: #eaecf4;"><?= number_format($barangkeluar['qty'], 2, ',', '.'); ?></td>
                                        <td align="right" style="background-color: #eaecf4; border-color: #eaecf4;"><?= number_format($barangkeluar['harga'], 2, ',', '.'); ?></td>
                                        <td align="right" style="background-color: #eaecf4; border-color: #eaecf4;"><?= number_format($barangkeluar['total'], 2, ',', '.'); ?></td>

                                        <td align="center"><?= number_format($stokSaatIni, 2, ',', '.'); ?></td>
                                        <td align="right"><?= number_format($rataRataHarga, 2, ',', '.'); ?></td>
                                        <td align="right"><?= number_format($subtotalAkhir, 2, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach;
                            } else { ?>
                                <tr>
                                    <td colspan="10" class="text-center font-italic">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="4" class="text-center">GRAND TOTAL</td>
                                <td colspan="2" class="text-center">STOK AWAL</td>
                                <td align="right"><?= number_format($grandTotalAwal, 2, ',', '.'); ?></td>
                                <td colspan="2" class="text-center">PEMBELIAN</td>
                                <td align="right"><?= number_format($grandTotalBeli, 2, ',', '.'); ?></td>
                                <td colspan="2" class="text-center">PEMAKAIAN</td>
                                <td align="right"><?= number_format($grandTotalPakai, 2, ',', '.'); ?></td>
                                <td colspan="2" class="text-center">STOK AKHIR</td>
                                <td align="right"><?= number_format($grandTotalAkhir, 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>