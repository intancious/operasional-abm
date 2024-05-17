<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$userModel = new \App\Models\UserModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="#" onclick="history.back()"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
        <a href="/dashboard/detailOperasional/<?= $kaskecil['kk_jenis']; ?>/<?= $kaskecil['kk_id']; ?>"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">OPERASIONAL</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#ongkir" role="tab" aria-controls="profile" aria-selected="false">ONGKOS KIRIM</a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="table-responsive my-4">
                            <table class="table table-bordered small" id="customTable" style="width:100%">
                                <thead class="thead-light font-weight-bold">
                                    <tr>
                                        <th>#</th>
                                        <th>TANGGAL</th>
                                        <th>NOMOR</th>
                                        <th>JENIS</th>
                                        <th>KETERANGAN</th>
                                        <th>NILAI</th>
                                        <th>KAS KECIL</th>
                                        <th>USER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $grandTotal = 0;
                                    if ($operasional) {
                                        $i = 1;
                                        foreach ($operasional as $row) {
                                            $total = $row['tl_nominal'] ? str_replace(',', '.', $row['tl_nominal']) : 0;
                                            $grandTotal += $total;
                                    ?>
                                            <tr class="text-uppercase">
                                                <td style="vertical-align: middle;"><?= $i++; ?></td>
                                                <td style="vertical-align: middle;"><?= date('d/m/Y H:i', strtotime($row['tl_tanggal'])); ?></td>
                                                <td style="vertical-align: middle;"><?= $row['tl_nomor']; ?></td>
                                                <td style="vertical-align: middle;">
                                                    <?= $row['tl_jenis'] == 1 ? 'OPERASIONAL PRODUKSI' : 'OPERASIONAL KANTOR'; ?>
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <?= $row['tl_keterangan']; ?>
                                                </td>
                                                <td style="vertical-align: middle;"><?= number_format($total, 2, ',', '.'); ?></td>
                                                <td style="vertical-align: middle;">(<?= $row['kk_nomor']; ?>) <?= $row['usr_nama']; ?></td>
                                                <td style="vertical-align: middle;"><?= $row['tl_user'] ? $userModel->find($row['tl_user'])['usr_nama'] : '-'; ?></td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="8" class="font-italic text-center">Data belum tersedia.</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                        <td colspan="5" class="font-weight-bold text-right">GRAND TOTAL</td>
                                        <td><?= number_format($grandTotal, 2, ',', '.'); ?></td>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ongkir" role="tabpanel" aria-labelledby="ongkir-tab">
                        <div class="table-responsive my-4">
                            <table class="table table-bordered small" id="customTable" style="width:100%">
                                <thead class="thead-light font-weight-bold">
                                    <tr>
                                        <th>#</th>
                                        <th>TANGGAL</th>
                                        <th>NOMOR</th>
                                        <th>SUPLIER</th>
                                        <th>NILAI</th>
                                        <th>USER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $grandTotalOngkir = 0;
                                    if ($ongkir) {
                                        $i = 1;
                                        foreach ($ongkir as $row) {
                                            $total = $row['op_bayar'] ? str_replace(',', '.', $row['op_bayar']) : 0;
                                            $grandTotalOngkir += $total;
                                    ?>
                                            <tr class="text-uppercase">
                                                <td style="vertical-align: middle;"><?= $i++; ?></td>
                                                <td style="vertical-align: middle;"><?= date('d/m/Y H:i', strtotime($row['op_tanggal'])); ?></td>
                                                <td style="vertical-align: middle;"><?= $row['op_nomor']; ?></td>
                                                <td style="vertical-align: middle;">
                                                    <?= $row['suplier_nama']; ?>
                                                </td>
                                                <td style="vertical-align: middle;"><?= number_format($total, 2, ',', '.'); ?></td>
                                                <td style="vertical-align: middle;"><?= $row['op_user'] ? $userModel->find($row['op_user'])['usr_nama'] : '-'; ?></td>
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
                                        <td colspan="4" class="font-weight-bold text-right">GRAND TOTAL</td>
                                        <td><?= number_format($grandTotalOngkir, 2, ',', '.'); ?></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>