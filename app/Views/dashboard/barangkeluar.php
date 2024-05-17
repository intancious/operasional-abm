<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$totalItems = ($totalRows / 100);
$supplierModel = new \App\Models\SuplierModel();
$barangModel = new \App\Models\BarangModel();
$userModel = new \App\Models\UserModel();
$barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/addBarangKeluar"><i class="fas fa-sm fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/barangkeluar"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                        <div class="col-md">
                            <div class="form-group">
                                <label for="faktur" class="small">NOMOR FAKTUR</label>
                                <input type="text" name="faktur" id="faktur" class="form-control form-control-sm" placeholder="Cari Nomor Faktur" value="<?= $request->getVar('faktur'); ?>">
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
                <div class="row justify-content-start">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="page_view">Halaman</label>
                            </div>
                            <select class="custom-select pagin" name="page_view" id="page_view">
                                <option value="<?= current_url(); ?>">--</option>
                                <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                                    <option value="<?= current_url(); ?>?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&faktur=<?= $request->getVar('faktur'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
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
                                <th>NO. FAKTUR</th>
                                <th>UNIT</th>
                                <th>TOTAL BARANG</th>
                                <th>NILAI</th>
                                <th>USER</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grandTotal = 0;
                            if ($barangkeluar) {
                                $i = 1;
                                foreach ($barangkeluar as $row) {
                                    $barang = $barangKeluarItemModel->getItems($row['bk_id']);
                                    $grandTotal += $barang['nilaiBarang'];
                            ?>
                                    <tr class="text-uppercase">
                                        <td style="vertical-align: middle;"><?= $i++; ?></td>
                                        <td style="vertical-align: middle;"><?= date('d/m/Y H:i', strtotime($row['bk_tanggal'])); ?></td>
                                        <td style="vertical-align: middle;">
                                            <a href="/dashboard/editBarangKeluar/<?= $row['bk_id']; ?>">
                                                <?= $row['bk_nomor']; ?>
                                            </a>
                                        </td>
                                        <td style="vertical-align: middle;"><?= strtoupper($row['unit_nama']); ?> (<?= $row['type_nama']; ?>)</td>
                                        <td style="vertical-align: middle;"><?= number_format($barang['totalBarang'], 0, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($barang['nilaiBarang'], 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= $row['bk_user'] ? $userModel->find($row['bk_user'])['usr_nama'] : '-'; ?></td>
                                        <td style="vertical-align: middle;">
                                            <form class="d-inline ml-1" action="/dashboard/deleteBarangKeluar" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['bk_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                                            </form>
                                        </td>
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

                <div class="row justify-content-end">
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="page_view">Halaman</label>
                            </div>
                            <select class="custom-select pagin" name="page_view" id="page_view">
                                <option value="<?= current_url(); ?>">--</option>
                                <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                                    <option value="<?= current_url(); ?>?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&faktur=<?= $request->getVar('faktur'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
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