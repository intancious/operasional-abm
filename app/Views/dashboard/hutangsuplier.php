<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$totalItems = ($totalRows / 100);
$pembelianModel = new \App\Models\PembelianModel();
$supplierModel = new \App\Models\SuplierModel();
$hbModel = new \App\Models\HutangsuplierBayarModel();
$userModel = new \App\Models\UserModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/hutangsuplier"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                                <label for="supplier" class="small">SUPLIER</label>
                                <select name="supplier" id="supplier" class="form-control form-control-sm selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php foreach ($supplierModel->orderBy('suplier_nama')->findAll() as $row) { ?>
                                        <option value="<?= $row['suplier_id']; ?>" data-tokens="<?= $row['suplier_nama']; ?>" <?= $request->getVar('supplier') == $row['suplier_id'] ? 'selected' : ''; ?>><?= strtoupper($row['suplier_nama']); ?></option>
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
                                    <option value="<?= current_url(); ?>?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&supplier=<?= $request->getVar('supplier'); ?>&faktur=<?= $request->getVar('faktur'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <a href="/dashboard/printXlsHutangsuplier<?= $request->uri->getQuery() ? '?' . $request->uri->getQuery() : ''; ?>" target="_blank" class="btn btn-primary btn-sm float-right"><i class="fas fa-file-excel mr-2"></i> PRINT XLS</a>
                        <a href="/dashboard/printPdfHutangsuplier<?= $request->uri->getQuery() ? '?' . $request->uri->getQuery() : ''; ?>" target="_blank" class="btn btn-primary btn-sm float-right mr-2"><i class="fas fa-file-pdf mr-2"></i> PRINT PDF</a>
                    </div>
                </div>

                <div class="table-responsive my-4">
                    <table class="table table-bordered small" id="customTable" style="width:100%">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <th>#</th>
                                <th>TANGGAL</th>
                                <th>NO. FAKTUR</th>
                                <th>NO. PEMBELIAN</th>
                                <th>SUPLIER</th>
                                <!-- <th>PEMBELIAN</th> -->
                                <!-- <th>ONGKIR</th> -->
                                <th>TOTAL HUTANG</th>
                                <th>DIBAYAR</th>
                                <th>SISA HUTANG</th>
                                <th>STATUS</th>
                                <th>USER</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grandTotal = 0;
                            $grandTotalOngkir = 0;
                            $grandTotalPlusOngkir = 0;
                            $grandTotalDibayar = 0;
                            $grandTotalSisa = 0;
                            if ($hutangsuplier) {
                                $i = 1;
                                foreach ($hutangsuplier as $row) {
                                    $pembelian = $pembelianModel->find($row['hs_pembelian']);
                                    $ongkir = $pembelian['pb_ongkir'] ? str_replace(',', '.', $pembelian['pb_ongkir']) : 0;
                                    $total = $row['hs_total'] ? str_replace(',', '.', $row['hs_total']) : 0;
                                    $totalHutang = $total + $ongkir;

                                    $grandTotal += $total;
                                    $grandTotalOngkir += $ongkir;
                                    $grandTotalPlusOngkir += $totalHutang;

                                    $dibayar = $hbModel->totalBayar($row['hs_id']);
                                    $grandTotalDibayar += $dibayar;

                                    $sisa = $totalHutang - $dibayar;
                                    $grandTotalSisa += $sisa;
                            ?>
                                    <tr class="text-uppercase">
                                        <td style="vertical-align: middle;"><?= $i++; ?></td>
                                        <td style="vertical-align: middle;"><?= date('d/m/Y H:i', strtotime($row['hs_tanggal'])); ?></td>
                                        <td style="vertical-align: middle;">
                                            <a href="/dashboard/bayarhutangsuplier/<?= $row['hs_id']; ?>">
                                                <?= $row['hs_nomor']; ?>
                                            </a>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <a href="/dashboard/pembelian?startDate=&endDate=&supplier=&jenis=&faktur=<?= $row['pb_nomor']; ?>" target="_blank">
                                                <?= $row['pb_nomor']; ?>
                                            </a>
                                        </td>
                                        <td style="vertical-align: middle;"><?= strtoupper($row['suplier_nama']); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($totalHutang, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($dibayar, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($sisa, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= $dibayar < $totalHutang ? '<span class="badge badge-secondary">BELUM LUNAS</span>' : ($dibayar >= $totalHutang ? '<span class="badge badge-success">LUNAS</span>' : ''); ?></td>
                                        <td style="vertical-align: middle;"><?= $row['hs_user'] ? $userModel->find($row['hs_user'])['usr_nama'] : '-'; ?></td>
                                        <td style="vertical-align: middle;">
                                            <form class="d-inline ml-1" action="/dashboard/deleteHutangsuplier" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['hs_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="11" class="font-italic text-center">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="5" class="font-weight-bold text-right">GRAND TOTAL</td>
                                <td><?= number_format($grandTotalPlusOngkir, 2, ',', '.'); ?></td>
                                <td><?= number_format($grandTotalDibayar, 2, ',', '.'); ?></td>
                                <td><?= number_format($grandTotalSisa, 2, ',', '.'); ?></td>
                                <td colspan="3">&nbsp;</td>
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
                                    <option value="<?= current_url(); ?>?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&supplier=<?= $request->getVar('supplier'); ?>&faktur=<?= $request->getVar('faktur'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
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