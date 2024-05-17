<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$barangModel = new \App\Models\BarangModel();
$kasKecilModel = new \App\Models\KasKecilModel();
$supplierModel = new \App\Models\SuplierModel();
$pembelianModel = new \App\Models\PembelianModel();
$pembelianItemModel = new \App\Models\PembelianItemModel();

$kasKecil = $kasKecilModel->where('kas_kecil.kk_id', $pembelian['pb_kaskecil'])
    ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
    ->select('kas_kecil.*, user.usr_nama')->first();

$pembelianItems = $pembelianItemModel->where('pembelian_item.pi_pembelian', $pembelian['pb_id'])
    ->join('barang', 'barang.barang_id = pembelian_item.pi_barang', 'left')
    ->select('pembelian_item.*, barang.barang_nama')
    ->orderBy('pembelian_item.pi_id', 'DESC')->findAll();

$supplier = $supplierModel->find($pembelian['pb_supplier']);
$ongkir = $pembelian['pb_ongkir'] ? str_replace(',', '.', $pembelian['pb_ongkir']) : 0;
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="#" onclick="history.back()"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-9">
        <div class="card shadow mb-3">
            <div class="card-body small">
                <div class="table-responsive">
                    <table border="0" colspan="0" cellpadding="5" style="width:100%">
                        <tr>
                            <th style="font-weight: bold;">SUPLIER</th>
                            <td style="vertical-align: top;">: <?= strtoupper($supplier['suplier_nama']); ?></td>
                            <th style="font-weight: bold;">TGL. TRANSAKSI</th>
                            <td>: <?= date('d/m/Y H:i', strtotime($pembelian['pb_tanggal'])); ?></td>
                        </tr>
                        <?php if ($pembelian['pb_jenis'] == 1) { ?>
                            <tr>
                                <th style="font-weight: bold;">JENIS TRANSAKSI</th>
                                <td>: <?= $pembelian['pb_jenis'] == 1 ? 'TUNAI' : ($pembelian['pb_jenis'] == 2 ? 'HUTANG' : ''); ?></td>
                                <th style="font-weight: bold;">KAS KECIL</th>
                                <td>: <?= strtoupper($kasKecil['kk_nomor'] . ' - ' . $kasKecil['usr_nama']); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($pembelian['pb_jenis'] == 2) { ?>
                            <tr>
                                <th style="font-weight: bold;">JATUH TEMPO</th>
                                <td style="vertical-align: top;">: <?= $pembelian['pb_jatuhtempo'] ? date('d/m/Y', strtotime($pembelian['pb_jatuhtempo'])) : '-'; ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <th style="font-weight: bold;">KETERANGAN</th>
                            <td>: <?= strtoupper($pembelian['pb_keterangan'] ? $pembelian['pb_keterangan'] : '-'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-sm-flex align-items-center justify-content-between my-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        DATA BARANG
    </h1>
</div>
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped small" id="customTable" style="width:100%">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <th>#</th>
                                <th>NAMA BARANG</th>
                                <th>PEMBELIAN</th>
                                <th>DATANG</th>
                                <th>HARGA</th>
                                <th>SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grandTotal = 0;
                            if ($pembelianItems) {
                                $i = 1;
                                foreach ($pembelianItems as $row) {
                                    $qtybeli = $row['pi_qtybeli'] ? str_replace(',', '.', $row['pi_qtybeli']) : 0;
                                    $qtymasuk = $row['pi_qtymasuk'] ? str_replace(',', '.', $row['pi_qtymasuk']) : 0;
                                    $harga = $row['pi_harga'] ? str_replace(',', '.', $row['pi_harga']) : 0;
                                    if ($qtymasuk > 0) {
                                        $subtotal = $qtymasuk * $harga;
                                    } else {
                                        $subtotal = $qtybeli * $harga;
                                    }
                                    $grandTotal += $subtotal;
                            ?>
                                    <tr class="text-uppercase">
                                        <td><?= $i++; ?></td>
                                        <td><?= $row['barang_nama']; ?></td>
                                        <td><?= number_format($qtybeli, 2, ',', '.'); ?></td>
                                        <td><?= number_format($qtymasuk, 2, ',', '.'); ?></td>
                                        <td><?= number_format($harga, 2, ',', '.'); ?></td>
                                        <td><?= number_format($subtotal, 2, ',', '.'); ?></td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="6" class="font-italic text-center">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        <tfoot style="background-color: #eaecf4; border-color: #eaecf4;" class="font-weight-bold">
                            <tr>
                                <td colspan="5" align="right">TOTAL</td>
                                <td><?= number_format($grandTotal, 2, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right">ONGKOS KIRIM</td>
                                <td><?= number_format($ongkir, 2, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right">GRAND TOTAL</td>
                                <td><?= number_format($grandTotal + $ongkir, 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>