<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$userModel = new \App\Models\UserModel();
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$totalItems = ($totalRows / 100);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/addLedger"><i class="fas fa-sm fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/ledger"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                                <input type="text" name="startDate" id="startDate" class="form-control form-control-sm" value="<?= $request->getVar('startDate') ? $request->getVar('startDate') : ''; ?>" placeholder="PILIH TANGGAL">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="endDate" class="small">SAMPAI TANGGAL</label>
                                <input type="text" name="endDate" id="endDate" class="form-control form-control-sm" value="<?= $request->getVar('endDate') ? $request->getVar('endDate') : ''; ?>" placeholder="PILIH TANGGAL">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="search" class="small">NOMOR / URAIAN</label>
                        <input type="text" name="search" id="search" class="form-control form-control-sm" value="<?= $request->getVar('search'); ?>" placeholder="NOMOR ATAU URAIAN">
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
                <div class="row mb-3">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="page_view">Halaman</label>
                            </div>
                            <select class="custom-select pagin" name="page_view" id="page_view">
                                <option value="<?= current_url(); ?>">--</option>
                                <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                                    <option value="<?= current_url(); ?>?page_view=<?= $i; ?>&startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&search=<?= $request->getVar('search'); ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered small" id="customTable" style="width:100%">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <th>#</th>
                                <th>TANGGAL</th>
                                <th>NOMOR</th>
                                <th>URAIAN</th>
                                <th>KET. TRANSAKSI</th>
                                <th>NOMINAL</th>
                                <th>USER</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($ledgers as $row) {
                                $leds = $ledgerModel->where('gl_nomor', $row['gl_nomor'])->findAll();
                                $sumKredit = 0;
                                $sumDebet = 0;
                                foreach ($leds as $l) {
                                    $sumKredit += floatval(str_replace(',', '.', $l['gl_nominalKredit']));
                                    $sumDebet += floatval(str_replace(',', '.', $l['gl_nominalDebet']));
                                }
                            ?>
                                <tr <?= $sumDebet != $sumKredit ? 'class="text-danger font-weight-bold"' : '' ?>>
                                    <td><?= $i++; ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    <td class="text-uppercase">
                                        <a href="/dashboard/editLedger?nomor=<?= urlencode($row['gl_nomor']); ?>">
                                            <?= $row['gl_nomor']; ?>
                                        </a>
                                    </td>
                                    <td class="text-uppercase"><?= $row['gl_uraian']; ?></td>
                                    <td class="text-uppercase"><?= $row['gl_trx'] ? $row['gl_trx'] : '-'; ?></td>
                                    <td><?= $sumDebet == $sumKredit ? number_format($sumDebet, 0, ".", ".") : 0; ?></td>
                                    <td class="text-uppercase"><?= $userModel->find($row['gl_user'])['usr_nama']; ?></td>
                                    <td>
                                        <?php if (in_array("deleteLedger", $akses)) { ?>
                                            <form class="d-inline" action="/dashboard/deleteLedger" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['gl_nomor']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                            </form>
                                        <?php } else { ?>
                                            <button type="button" disabled class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="row justify-content-end my-3">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="page_view">Halaman</label>
                            </div>
                            <select class="custom-select pagin" name="page_view" id="page_view">
                                <option value="<?= current_url(); ?>">--</option>
                                <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                                    <option value="<?= current_url(); ?>?page_view=<?= $i; ?>&startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&search=<?= $request->getVar('search'); ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
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