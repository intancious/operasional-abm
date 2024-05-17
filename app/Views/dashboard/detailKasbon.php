<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$barangModel = new \App\Models\BarangModel();
$userModel = new \App\Models\UserModel();
$tukangModel = new \App\Models\TukangModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="#" onclick="history.back()"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
        <a href="/dashboard/detailKasbon/<?= $kaskecil['kk_id']; ?>"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                                <th>NOMOR</th>
                                <th>NILAI</th>
                                <th>KAS KECIL</th>
                                <th>USER</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grandTotal = 0;
                            if ($kasbon) {
                                $trxupahModel = new \App\Models\TrxupahModel();
                                $i = 1;
                                foreach ($kasbon as $row) {
                                    $trxupah = $trxupahModel->find($row['tul_trxupah']);
                                    $total = $row['tul_nominal'] ? str_replace(',', '.', $row['tul_nominal']) : 0;
                                    $grandTotal += $total;
                            ?>
                                    <tr class="text-uppercase">
                                        <td style="vertical-align: middle;"><?= $i++; ?></td>
                                        <td style="vertical-align: middle;"><?= date('d/m/Y H:i', strtotime($row['tul_tanggal'])); ?></td>
                                        <td style="vertical-align: middle;"><?= $trxupah['tu_nomor']; ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($total, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;">(<?= $row['kk_nomor']; ?>) <?= $row['usr_nama']; ?></td>
                                        <td style="vertical-align: middle;"><?= $row['tul_user'] ? $userModel->find($row['tul_user'])['usr_nama'] : '-'; ?></td>
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
                                <td colspan="3" class="font-weight-bold text-right">GRAND TOTAL</td>
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