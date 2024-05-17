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
        <a href="/dashboard/neracaSaldo"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow">
    <div class="card-body">
        <?= session()->get('pesan'); ?>
        <div class="table-responsive">
            <table class="table table-bordered small" id="customTable" style="width:100%">
                <thead class="thead-light font-weight-bold">
                    <tr>
                        <th>#</th>
                        <th>NO. AKUN</th>
                        <th>NAMA AKUN</th>
                        <th>DEBET</th>
                        <th>KREDIT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    if ($rekening) {

                        $barangModel = new \App\Models\BarangModel();
                        $bukuBesarModel = new \App\Models\BukuBesarModel();
                        $rekModel = new \App\Models\KoderekeningModel();

                        $grandTotalDebet = 0;
                        $grandTotalKredit = 0;
                        $grandTotalSaldo = 0;

                        foreach ($rekening as $row) {
                            $rekSup = $rekModel->find($row['rek_id']);
                            if ($rekSup['reksub5_id'] != 35) {
                                $saldo = $bukuBesarModel->saldoAkhirBb($row['rek_id']);

                                $rekSub = substr($row['rek_kode'], 0, 2);
                                if ($rekSub == 21 || $rekSub == 22 || $rekSub == 31 || $rekSub == 41 || $rekSub == 42 || $rekSub == 71) {
                                    if ($saldo < 0) {
                                        $grandTotalDebet += abs($saldo);
                                    }
                                    if ($saldo > 0) {
                                        $grandTotalKredit += abs($saldo);
                                    }
                                } else {
                                    if ($saldo > 0) {
                                        $grandTotalDebet += abs($saldo);
                                    }
                                    if ($saldo < 0) {
                                        $grandTotalKredit += abs($saldo);
                                    }
                                }
                    ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= $row['rek_kode']; ?></td>
                                    <td class="text-uppercase"><a href="/dashboard/bukubesar?rekening=<?= $row['rek_id']; ?>&startDate=&endDate="><?= $row['rek_nama']; ?></a></td>
                                    <td align="right">
                                        <?php
                                        if ($rekSub == 21 || $rekSub == 22 || $rekSub == 31 || $rekSub == 41 || $rekSub == 42 || $rekSub == 71) {
                                            if ($saldo < 0) {
                                                echo number_format(abs($saldo), 0, ".", ".");
                                            } else {
                                                echo number_format(0, 0, ".", ".");
                                            }
                                        } else {
                                            if ($saldo > 0) {
                                                echo number_format(abs($saldo), 0, ".", ".");
                                            } else {
                                                echo number_format(0, 0, ".", ".");
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td align="right">
                                        <?php
                                        if ($rekSub == 21 || $rekSub == 22 || $rekSub == 31 || $rekSub == 41 || $rekSub == 42 || $rekSub == 71) {
                                            if ($saldo > 0) {
                                                echo number_format(abs($saldo), 0, ".", ".");
                                            } else {
                                                echo number_format(0, 0, ".", ".");
                                            }
                                        } else {
                                            if ($saldo < 0) {
                                                echo number_format(abs($saldo), 0, ".", ".");
                                            } else {
                                                echo number_format(0, 0, ".", ".");
                                            }
                                        }
                                        ?>
                                    </td>
                                </tr>
                        <?php }
                        }
                    } else { ?>
                        <tr>
                            <td colspan="5" align="center" class="small">Data belum tersedia.</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="font-weight-bold text-center">GRAND TOTAL</td>
                        <td class="font-weight-bold text-right"><?= number_format((isset($grandTotalDebet) ? $grandTotalDebet : 0), 0, ".", "."); ?></td>
                        <td class="font-weight-bold text-right"><?= number_format((isset($grandTotalKredit) ? $grandTotalKredit : 0), 0, ".", "."); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>