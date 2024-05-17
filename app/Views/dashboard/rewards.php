<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$totalItems = ($totalRows / 100);
$userModel = new \App\Models\UserModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/rewards"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                    <div class="form-group">
                        <label for="user" class="small">USER</label>
                        <select name="user" id="user" class="form-control form-control-sm selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php foreach ($userModel->orderBy('usr_nama', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['usr_id']; ?>" data-tokens="<?= $row['usr_nama']; ?>" <?= $request->getVar('user') == $row['usr_id'] ? 'selected' : ''; ?>><?= strtoupper($row['usr_nama']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-light btn-sm mr-2"><i class="fas fa-sm fa-filter text-secondary mr-1"></i> FILTER</button>
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
                                    <option value="<?= current_url(); ?>?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&user=<?= $request->getVar('user'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
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
                                <th>TGL. PENGAJUAN</th>
                                <th>NAMA</th>
                                <th>DIAJUKAN</th>
                                <th>DISETUJUI</th>
                                <th>DATANG</th>
                                <th>REWARD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalDiajukan = 0;
                            $totalDisetujui = 0;
                            $totalDatang = 0;
                            if ($rewards) {
                                $i = 1;
                                foreach ($rewards as $row) {
                                    $totalDiajukan += $row['diajukan'];
                                    $totalDisetujui += $row['disetujui'];
                                    $totalDatang += $row['datang'];
                            ?>
                                    <tr class="text-uppercase">
                                        <td><?= $i++; ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['pb_tanggal'])); ?></td>
                                        <td><?= $row['usr_nama']; ?></td>
                                        <td><?= number_format($row['diajukan'], 0, ',', '.'); ?></td>
                                        <td><?= number_format($row['disetujui'], 0, ',', '.'); ?></td>
                                        <td><?= number_format($row['datang'], 0, ',', '.'); ?></td>
                                        <td><?= round($row['persen'], 2); ?>%</td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="7" class="font-italic text-center">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="3" class="font-weight-bold text-right">GRAND TOTAL</td>
                                <td><?= number_format($totalDiajukan, 0, ',', '.'); ?></td>
                                <td><?= number_format($totalDisetujui, 0, ',', '.'); ?></td>
                                <td><?= number_format($totalDatang, 0, ',', '.'); ?></td>
                                <td><?= $totalDatang > 0 ? round(($totalDatang / $totalDiajukan) * 100, 2) : 0; ?>%</td>
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
                                    <option value="<?= current_url(); ?>?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&user=<?= $request->getVar('user'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
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