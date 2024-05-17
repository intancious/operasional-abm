<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$totalItems = ($totalRows / 100);
$unitModel = new \App\Models\UnitModel();
$tukangModel = new \App\Models\TukangModel();
$upahModel = new \App\Models\UpahModel();
$userModel = new \App\Models\UserModel();
$trxupahModel = new \App\Models\TrxupahModel();
$trxupahItemModel = new \App\Models\TrxupahItemModel();

$tukang = $tukangModel->orderBy('tk_nama', 'ASC')->findAll();

$units = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
    ->select('unit.*, types.type_nama')
    ->orderBy('unit.unit_nama', 'ASC')->findAll();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/addUbk"><i class="fas fa-sm fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/ubk"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="startDate" class="small">MULAI TANGGAL</label>
                                <input type="text" name="startDate" id="startDate" class="form-control form-control-sm" value="<?= $request->getVar('startDate'); ?>" placeholder="Pilih Tanggal">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="endDate" class="small">SAMPAI TANGGAL</label>
                                <input type="text" name="endDate" id="endDate" class="form-control form-control-sm" value="<?= $request->getVar('endDate'); ?>" placeholder="Pilih Tanggal">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tukang" class="small">TUKANG</label>
                                <select id="tukang" name="tukang" class="form-control form-control-sm selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: Pilih ::</option>
                                    <?php foreach ($tukang as $row) { ?>
                                        <option value="<?= $row['tk_id']; ?>" data-tokens="<?= $row['tk_nama']; ?>" <?= $request->getVar('tukang') == $row['tk_id'] ? 'selected' : ''; ?>><?= strtoupper($row['tk_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="faktur" class="small">NOMOR FAKTUR</label>
                                <input type="text" name="faktur" id="faktur" class="form-control form-control-sm" placeholder="Cari Nomor Faktur" value="<?= $request->getVar('faktur'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-light btn-sm mr-2"><i class="fas fa-sm fa-filter text-secondary mr-1"></i> FILTER</button>
                        <?php if ($request->getVar('startDate') && $request->getVar('endDate')) { ?>
                            <a href="/dashboard/printupah?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&tukang=<?= $request->getVar('tukang'); ?>" class="btn btn-light btn-sm mr-2" target="_blank"><i class="fas fa-sm fa-print text-secondary mr-1"></i> PRINT</a>
                        <?php } ?>
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
                <div class="row justify-content-start">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="page_view">Halaman</label>
                            </div>
                            <select class="custom-select pagin" name="page_view" id="page_view">
                                <option value="<?= current_url(); ?>">--</option>
                                <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                                    <option value="<?= current_url(); ?>?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&faktur=<?= $request->getVar('faktur'); ?>&tukang=<?= $request->getVar('tukang'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive my-4">
                    <table class="table table-bordered small" id="customTable" style="width:100%">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <th>#</th>
                                <th>TANGGAL</th>
                                <th>FAKTUR</th>
                                <th>TUKANG</th>
                                <th>UPAH</th>
                                <th>LEMBUR</th>
                                <th>KAS BON</th>
                                <th>TOTAL UPAH</th>
                                <th>DIBAYAR</th>
                                <th>SISA UPAH</th>
                                <th>STATUS</th>
                                <th>USER</th>
                                <th>BAYAR</th>
                                <th>PRINT</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grandTotalUpah = 0;
                            $grandTotalLembur = 0;
                            $grandTotalKasbon = 0;
                            $grandTotal = 0;
                            $grandTotalBayar = 0;
                            $grandTotalSisa = 0;
                            if ($trxupah) {
                                $trxupahBayarModel = new \App\Models\TrxupahBayarModel();
                                $i = 1;
                                foreach ($trxupah as $row) {
                                    $totalUpah = $row['tu_totalupah'] ? str_replace(',', '.', $row['tu_totalupah']) : 0;
                                    $totalLembur = $row['tu_lembur'] ? str_replace(',', '.', $row['tu_lembur']) : 0;
                                    $totalBon = $row['tu_bon'] ? str_replace(',', '.', $row['tu_bon']) : 0;

                                    $totalBayarUpah = $trxupahBayarModel->totalBayarUpah($row['tu_id']);
                                    $total = ($totalUpah + $totalLembur) - $totalBon;
                                    $sisa = $total - $totalBayarUpah;

                                    $grandTotalUpah += $totalUpah;
                                    $grandTotalLembur += $totalLembur;
                                    $grandTotalKasbon += $totalBon;
                                    $grandTotal += $total;
                                    $grandTotalBayar += $totalBayarUpah;
                                    $grandTotalSisa += $sisa;
                            ?>
                                    <tr class="text-uppercase">
                                        <td style="vertical-align: middle;"><?= $i++; ?></td>
                                        <td style="vertical-align: middle;"><?= date('d/m/Y H:i', strtotime($row['tu_tanggal'])); ?></td>
                                        <td style="vertical-align: middle;">
                                            <a href="/dashboard/editUbk/<?= $row['tu_id']; ?>">
                                                <?= $row['tu_nomor']; ?>
                                            </a>
                                        </td>
                                        <td style="vertical-align: middle;"><?= $row['tk_nama']; ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($totalUpah, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($totalLembur, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($totalBon, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($total, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($totalBayarUpah, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($sisa, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;">
                                            <?= $totalBayarUpah < $total ? '<span class="badge badge-secondary">BELUM LUNAS</span>' : ($totalBayarUpah >= $total ? '<span class="badge badge-success">LUNAS</span>' : ''); ?>
                                        </td>
                                        <td style="vertical-align: middle;"><?= $row['usr_nama']; ?></td>
                                        <td style="vertical-align: middle;">
                                            <a href="/dashboard/bayarubk/<?= $row['tu_id']; ?>" class="btn btn-primary btn-sm">BAYAR</a>
                                        </td>
                                        <td style="vertical-align: middle;" align="center">
                                            <a href="/dashboard/printdetailupah/<?= $row['tu_id']; ?>" target="_blank"><i class="fas fa-print"></i></a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <form class="d-inline ml-1" action="/dashboard/deleteUbk" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['tu_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="15" class="font-italic text-center">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="4" class="font-weight-bold text-right">GRAND TOTAL</td>
                                <td><?= number_format($grandTotalUpah, 2, ',', '.'); ?></td>
                                <td><?= number_format($grandTotalLembur, 2, ',', '.'); ?></td>
                                <td><?= number_format($grandTotalKasbon, 2, ',', '.'); ?></td>
                                <td><?= number_format($grandTotal, 2, ',', '.'); ?></td>
                                <td><?= number_format($grandTotalBayar, 2, ',', '.'); ?></td>
                                <td><?= number_format($grandTotalSisa, 2, ',', '.'); ?></td>
                                <td colspan="5">&nbsp;</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row justify-content-end">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="page_view">Halaman</label>
                            </div>
                            <select class="custom-select pagin" name="page_view" id="page_view">
                                <option value="<?= current_url(); ?>">--</option>
                                <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                                    <option value="<?= current_url(); ?>?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&faktur=<?= $request->getVar('faktur'); ?>&tukang=<?= $request->getVar('tukang'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>