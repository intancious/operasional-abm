<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$rekeningModel = new \App\Models\KoderekeningModel();
$totalItems = ($totalRows / 50);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/bukubesar"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                    <div class="form-group">
                        <label for="kategori" class="small">PILIH REKENING</label>
                        <select name="rekening" id="rekening" class="form-control form-control-sm selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            $rekDropdown = $rekeningModel->orderBy('rek_kode', 'ASC')->findAll();
                            foreach ($rekDropdown as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= $row['rek_kode']; ?>) <?= $row['rek_nama']; ?>" <?= $idRek == $row['rek_id'] ? 'selected' : ''; ?>>(<?= $row['rek_kode']; ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="row mt-3 justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="startDate" class="small">MULAI TANGGAL</label>
                                <input type="text" name="startDate" id="startDate" class="form-control form-control-sm" value="<?= $request->getVar('startDate'); ?>" placeholder="PILIH TANGGAL">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="endDate" class="small">SAMPAI TANGGAL</label>
                                <input type="text" name="endDate" id="endDate" class="form-control form-control-sm" value="<?= $request->getVar('endDate'); ?>" placeholder="PILIH TANGGAL">
                            </div>
                        </div>
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
                <div class="row justify-content-start my-3">
                    <div class="col-md-2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="pageNum">Halaman</label>
                            </div>
                            <select class="custom-select pagin" name="pageNum" id="pageNum">
                                <option value="<?= current_url(); ?>">--</option>
                                <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                                    <option value="<?= current_url(); ?>?rekening=<?= $request->getVar('rekening'); ?>&startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&pageNum=<?= $i; ?>" <?= $request->getVar('pageNum') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="customTable" class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>TANGGAL</th>
                                <th>NO. BUKTI</th>
                                <th>URAIAN</th>
                                <th>REKENING</th>
                                <th>DEBET</th>
                                <th>KREDIT</th>
                                <th>SALDO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            if ($bukubesar) {
                                $rekeningModel = new \App\Models\KoderekeningModel()
                            ?>
                                <?php foreach ($bukubesar as $index => $row) {
                                    $rekAkun = $rekeningModel->find($row['rek']); ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                        <td class="text-uppercase"><?= $row['nomor']; ?></td>
                                        <td class="text-uppercase"><?= $row['uraian']; ?></td>
                                        <td><?= '(' . $rekAkun['rek_kode'] . ') ' . strtoupper($rekAkun['rek_nama']); ?></td>
                                        <td><?= number_format($row['debet'], 0, ".", "."); ?></td>
                                        <td><?= number_format($row['kredit'], 0, ".", "."); ?></td>
                                        <td>
                                            <?php
                                            $rekSub = substr($rekAkun['rek_kode'], 0, 2);
                                            if ($rekSub == 11 || $rekSub == 12 || $rekSub == 51 || $rekSub == 60 || $rekSub == 61 || $rekSub == 62 || $rekSub == 63 || $rekSub == 72) {
                                                $saldoDebet = 0;
                                                for ($in = 0; $in <= $index; $in++) {
                                                    $saldoDebet += $bukubesar[$in]['debet'];
                                                }

                                                $saldoKredit = 0;
                                                for ($in = 0; $in <= $index; $in++) {
                                                    $saldoKredit -= $bukubesar[$in]['kredit'];
                                                }
                                            }

                                            if ($rekSub == 21 || $rekSub == 22 || $rekSub == 31 || $rekSub == 41 || $rekSub == 42 || $rekSub == 71) {
                                                $saldoDebet = 0;
                                                for ($in = 0; $in <= $index; $in++) {
                                                    $saldoDebet -= $bukubesar[$in]['debet'];
                                                }

                                                $saldoKredit = 0;
                                                for ($in = 0; $in <= $index; $in++) {
                                                    $saldoKredit += $bukubesar[$in]['kredit'];
                                                }
                                            }

                                            if (substr($rekAkun['rek_kode'], 0, 1) == 8) {
                                                $saldoDebet = 0;
                                                for ($in = 0; $in <= $index; $in++) {
                                                    $saldoDebet += $bukubesar[$in]['debet'];
                                                }

                                                $saldoKredit = 0;
                                                for ($in = 0; $in <= $index; $in++) {
                                                    $saldoKredit -= $bukubesar[$in]['kredit'];
                                                }
                                            }

                                            if (substr($rekAkun['rek_kode'], 0, 1) == 9) {
                                                $saldoDebet = 0;
                                                for ($in = 0; $in <= $index; $in++) {
                                                    $saldoDebet += $bukubesar[$in]['debet'];
                                                }

                                                $saldoKredit = 0;
                                                for ($in = 0; $in <= $index; $in++) {
                                                    $saldoKredit -= $bukubesar[$in]['kredit'];
                                                }
                                            }

                                            $saldo = (isset($saldoDebet) ? $saldoDebet : 0) + (isset($saldoKredit) ? $saldoKredit : 0);
                                            echo number_format($saldo, 0, ".", ".");
                                            ?>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
                <div class="row justify-content-end my-3">
                    <div class="col-md-2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="pageNum">Halaman</label>
                            </div>
                            <select class="custom-select pagin" name="pageNum" id="pageNum">
                                <option value="<?= current_url(); ?>">--</option>
                                <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                                    <option value="<?= current_url(); ?>?rekening=<?= $request->getVar('rekening'); ?>&startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&pageNum=<?= $i; ?>" <?= $request->getVar('pageNum') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
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