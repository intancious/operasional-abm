<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$supplierModel = new \App\Models\SuplierModel();
$barangModel = new \App\Models\BarangModel();
$userModel = new \App\Models\UserModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="#" onclick="history.back()"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
        <a href="/dashboard/detailPembelianTunai/<?= $kaskecil['kk_id']; ?>"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <?= session()->get('pesan'); ?>
                <div class="table-responsive">
                    <table class="table table-bordered small" id="customTable" style="width:100%">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <th>#</th>
                                <th>TANGGAL</th>
                                <th>NO. FAKTUR</th>
                                <th>PEMBELIAN</th>
                                <th>STATUS</th>
                                <th>USER</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grandTotal = 0;
                            $grandTotalOngkir = 0;
                            $grandTotalPlusOngkir = 0;
                            if ($pembelian) {
                                $i = 1;
                                foreach ($pembelian as $row) {
                                    $total = $row['pb_total'] ? str_replace(',', '.', $row['pb_total']) : 0;
                                    $ongkir = $row['pb_ongkir'] ? str_replace(',', '.', $row['pb_ongkir']) : 0;
                                    $grandTotal += $total;
                                    $grandTotalOngkir += $ongkir;
                                    $grandTotalPlusOngkir += $total + $ongkir;
                            ?>
                                    <tr class="text-uppercase">
                                        <td style="vertical-align: middle;"><?= $i++; ?></td>
                                        <td style="vertical-align: middle;"><?= date('d/m/Y H:i', strtotime($row['pb_tanggal'])); ?></td>
                                        <td style="vertical-align: middle;">
                                            <!-- <a href="/dashboard/detailPembelian/<?= $row['pb_id']; ?>"> -->
                                            <?= $row['pb_nomor']; ?>
                                            <!-- </a> -->
                                        </td>
                                        <td style="vertical-align: middle;"><?= number_format($total, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= $row['pb_status'] == 1 ? 'PROSES' : ($row['pb_status'] == 2 ? 'SELESAI' : ''); ?></td>
                                        <td style="vertical-align: middle;"><?= $row['pb_user'] ? $userModel->find($row['pb_user'])['usr_nama'] : '-'; ?></td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="6" class="font-italic text-center">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="3" class="font-weight-bold text-right">TOTAL</td>
                                <td><?= number_format($grandTotal, 2, ',', '.'); ?></td>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>