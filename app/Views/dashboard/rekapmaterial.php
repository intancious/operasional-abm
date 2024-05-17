<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$barangModel = new \App\Models\BarangModel();
$pembelianItemModel = new \App\Models\PembelianItemModel();
$barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
$totalItems = ($totalRows / 100);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/rekapmaterial"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                    <div class="row mb-3 justify-content-start">
                        <div class="col-md-6">
                            <label for="jenis" class="small">REKAP</label>
                            <select required name="jenis" id="jenis" class="form-control form-control-sm">
                                <option value="">-PILIH-</option>
                                <option value="masuk" <?= $request->getVar('jenis') == 'masuk' ? 'selected' : ''; ?>>BARANG MASUK</option>
                                <option value="keluar" <?= $request->getVar('jenis') == 'keluar' ? 'selected' : ''; ?>>BARANG KELUAR</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="showUnit">
                            <label for="unit" class="small">UNIT</label>
                            <select name="unit" id="unit" class="form-control form-control-sm selectpicker <?= $validation->hasError('unit') ? 'is-invalid' : ''; ?>" data-live-search="true">
                                <option value="" data-tokens="">:: SEMUA ::</option>
                                <?php
                                $unitModel = new \App\Models\UnitModel();
                                $unit = $unitModel->orderBy('unit_nama', 'ASC')->findAll();
                                foreach ($unit as $row) { ?>
                                    <option value="<?= $row['unit_id']; ?>" data-tokens="<?= $row['unit_nama']; ?>" <?= $request->getVar('unit') == $row['unit_id'] ? 'selected' : ''; ?>><?= strtoupper($row['unit_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-1 justify-content-center">
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
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-light btn-sm mr-2"><i class="fas fa-sm fa-filter text-secondary mr-1"></i> FILTER</button>
                        <?php if ($request->getVar('jenis')) { ?>
                            <a href="/dashboard/printXlsRekapmaterial<?= $request->uri->getQuery() ? '?' . $request->uri->getQuery() : ''; ?>" target="_blank" class="btn btn-light btn-sm text-secondary"><i class="fas fa-file-excel text-secondary mr-2"></i> PRINT XLS</a>
                            <a href="/dashboard/printPdfRekapmaterial<?= $request->uri->getQuery() ? '?' . $request->uri->getQuery() : ''; ?>" target="_blank" class="btn btn-light btn-sm ml-2 text-secondary"><i class="fas fa-file-pdf text-secondary mr-2"></i> PRINT PDF</a>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($request->getVar('jenis')) { ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?= session()->get('pesan'); ?>
                    <div class="row justify-content-start my-3">
                        <div class="col-md-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="page_view">Halaman</label>
                                </div>
                                <select class="custom-select pagin" name="page_view" id="page_view">
                                    <option value="<?= current_url(); ?>">--</option>
                                    <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                                        <option value="<?= current_url(); ?>?jenis=<?= $request->getVar('jenis'); ?>&startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
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
                                    <th>NAMA</th>
                                    <th>SATUAN</th>
                                    <?php if ($request->getVar('jenis') == 'keluar') { ?>
                                        <th>UNIT</th>
                                    <?php } ?>
                                    <th>JUMLAH</th>
                                    <th>HARGA</th>
                                    <th>NILAI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $grandTotal = 0;
                                if ($barang) {
                                    foreach ($barang as $row) :
                                        if ($request->getVar('jenis') == 'masuk') {
                                            $qtymasuk = $row['pi_qtymasuk'] ? str_replace(',', '.', $row['pi_qtymasuk']) : 0;
                                            $harga = $row['pi_harga'] ? str_replace(',', '.', $row['pi_harga']) : 0;
                                            $subtotal = $qtymasuk * $harga;
                                            $grandTotal += $subtotal;
                                ?>
                                            <tr class="text-uppercase <?= $row['pi_jenis'] == 1 ? 'text-primary' : ''; ?>">
                                                <td><?= $i++; ?></td>
                                                <td><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                                <td><?= $row['barang_nama']; ?></td>
                                                <td><?= $row['satuan_nama']; ?></td>
                                                <td><?= number_format($qtymasuk, 2, ',', '.'); ?></td>
                                                <td align="right"><?= number_format($harga, 2, ',', '.'); ?></td>
                                                <td align="right"><?= number_format($subtotal, 2, ',', '.'); ?></td>
                                            </tr>
                                        <?php }
                                        if ($request->getVar('jenis') == 'keluar') {
                                            $qty = $row['bki_qty'] ? str_replace(',', '.', $row['bki_qty']) : 0;
                                            $harga = $barangModel->rataRataHarga($row['bki_barang']);
                                            $subtotal = $qty * $harga;
                                            $grandTotal += $subtotal;
                                        ?>
                                            <tr class="text-uppercase">
                                                <td><?= $i++; ?></td>
                                                <td><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                                <td><?= $row['barang_nama']; ?></td>
                                                <td><?= $row['satuan_nama']; ?></td>
                                                <td><?= $row['unit_nama']; ?></td>
                                                <td><?= number_format($qty, 2, ',', '.'); ?></td>
                                                <td align="right"><?= number_format($harga, 2, ',', '.'); ?></td>
                                                <td align="right"><?= number_format($subtotal, 2, ',', '.'); ?></td>
                                            </tr>
                                    <?php }
                                    endforeach;
                                } else { ?>
                                    <tr>
                                        <td colspan="<?= $request->getVar('jenis') == 'keluar' ? '8' : '7'; ?>" class="text-center font-italic">Data belum tersedia.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                    <td colspan="<?= $request->getVar('jenis') == 'keluar' ? '7' : '6'; ?>" class="text-center">GRAND TOTAL</td>
                                    <td align="right"><?= number_format($grandTotal, 2, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row justify-content-end my-3">
                        <div class="col-md-2">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="page_view">Halaman</label>
                                </div>
                                <select class="custom-select pagin" name="page_view" id="page_view">
                                    <option value="<?= current_url(); ?>">--</option>
                                    <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                                        <option value="<?= current_url(); ?>?page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?= $this->endSection(); ?>