<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$totalItems = ($totalRows / 100);
$bpModel = new \App\Models\BiayapenjualanModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/addPenjualan"><i class="fas fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/penjualanunit"><i class="fas fa-sync-alt ml-2"></i></a>
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
                        <label for="jenis">JENIS TRANSAKSI</label>
                        <select name="jenis" id="jenis" class="form-control form-control-sm <?= $validation->hasError('jenis') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: SEMUA ::</option>
                            <option value="cash" data-tokens="cash" <?= $request->getVar('jenis') == 'cash' ? 'selected' : ''; ?>>CASH</option>
                            <option value="kpr" data-tokens="kpr" <?= $request->getVar('jenis') == 'kpr' ? 'selected' : ''; ?>>KPR</option>
                            <option value="kredit" data-tokens="kredit" <?= $request->getVar('jenis') == 'kredit' ? 'selected' : ''; ?>>IN HOUSE</option>
                        </select>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kpr">KPR</label>
                                <select name="kpr" id="kpr" class="form-control form-control-sm<?= $validation->hasError('kpr') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: SEMUA ::</option>
                                    <?php
                                    $kprModel = new \App\Models\KprModel();
                                    $kpr = $kprModel->orderBy('kpr_nama', 'ASC')->findAll();
                                    foreach ($kpr as $row) { ?>
                                        <option value="<?= $row['kpr_id']; ?>" data-tokens="<?= $row['kpr_nama']; ?>" <?= $request->getVar('kpr') == $row['kpr_id'] ? 'selected' : ''; ?>><?= strtoupper($row['kpr_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor">NO. TRANSAKSI</label>
                                <input type="text" name="nomor" id="nomor" class="form-control form-control-sm" value="<?= $request->getVar('nomor'); ?>" placeholder="NO. TRANSAKSI">
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer">CUSTOMER</label>
                                <select name="customer" id="customer" class="form-control form-control-sm <?= $validation->hasError('customer') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: SEMUA ::</option>
                                    <?php
                                    $customerModel = new \App\Models\CustomerModel();
                                    $customers = $customerModel->orderBy('cust_nama', 'ASC')->findAll();
                                    foreach ($customers as $row) { ?>
                                        <option value="<?= $row['cust_id']; ?>" data-tokens="<?= $row['cust_nama']; ?>" <?= $request->getVar('customer') == $row['cust_id'] ? 'selected' : ''; ?>><?= strtoupper($row['cust_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit">UNIT</label>
                                <select name="unit" id="unit" class="form-control form-control-sm <?= $validation->hasError('unit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: SEMUA ::</option>
                                    <?php
                                    $unitModel = new \App\Models\UnitModel();
                                    $units = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
                                        ->select('unit.*, types.type_nama')->orderBy('unit.unit_nama', 'ASC')->findAll();
                                    foreach ($units as $row) { ?>
                                        <option value="<?= $row['unit_id']; ?>" data-tokens="<?= $row['unit_nama']; ?> (<?= $row['type_nama']; ?>)" <?= $request->getVar('unit') == $row['unit_id'] ? 'selected' : ''; ?>><?= strtoupper($row['unit_nama']); ?> (<?= $row['type_nama']; ?>)</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1 justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="startDate">MULAI TANGGAL</label>
                                <input type="text" name="startDate" id="startDate" class="form-control form-control-sm" value="<?= $request->getVar('startDate') ? $request->getVar('startDate') : ''; ?>" placeholder="PILIH TANGGAL">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="endDate">SAMPAI TANGGAL</label>
                                <input type="text" name="endDate" id="endDate" class="form-control form-control-sm" value="<?= $request->getVar('endDate') ? $request->getVar('endDate') : ''; ?>" placeholder="PILIH TANGGAL">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
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
                                    <option value="<?= current_url(); ?>?page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
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
                                <th>NO. TRANSAKSI</th>
                                <th>NAMA CUSTOMER</th>
                                <th>JENIS PENJUALAN</th>
                                <th>UNIT</th>
                                <th>KPR</th>
                                <th>STATUS KPR</th>
                                <th>HARGA TRANSAKSI</th>

                                <th>ACC KPR</th>
                                <th>PENCAIRAN KPR</th>
                                <th>SISA KPR</th>

                                <th>BAYAR</th>
                                <th>SISA</th>
                                <th>TOTAL SISA</th>

                                <th>STATUS</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            $grandTotalHarga = 0;
                            $grandTotalKpr = 0;
                            $grandTotalBayarKpr = 0;
                            $grandTotalSisaKpr = 0;
                            $grandTotalBayar = 0;
                            $grandTotalSisa = 0;
                            $grandTotalSisaPiutang = 0;
                            if ($penjualans) {
                                $tagModel = new \App\Models\TagihanPuModel();
                                $bpModel = new \App\Models\BiayapenjualanModel();
                                foreach ($penjualans as $row) :
                                    $nup = $bpModel->where(['bp_penjualan' => $row['pu_id'], 'bp_biayalain' => 1])->first();
                                    $totalBayar = $row['pu_status'] == 1 ? $tagModel->getTotalBayar($row['pu_id'], 1) : 0;
                                    $totalBayarKpr = $row['pu_status'] == 1 ? $tagModel->getTotalBayar($row['pu_id'], 2) : 0;
                                    $bpenjualan = $bpModel->where('biaya_penjualan.bp_penjualan', $row['pu_id'])
                                        ->join('biayalain', 'biayalain.bl_id = biaya_penjualan.bp_biayalain', 'left')
                                        ->select('biaya_penjualan.*, biayalain.bl_nama')
                                        ->findAll();

                                    $bkembali = $bpModel->where(['biaya_penjualan.bp_penjualan' => $row['pu_id'], 'biaya_penjualan.bp_kembali' => 1])
                                        ->join('biayalain', 'biayalain.bl_id = biaya_penjualan.bp_biayalain', 'left')
                                        ->select('biaya_penjualan.*, biayalain.bl_nama')
                                        ->findAll();

                                    $pengembalian = 0;
                                    foreach ($bkembali as $bk) {
                                        $pengembalian += $bk['bp_nominal'];
                                    }

                                    $biayalain = [];
                                    foreach ($bpenjualan as $bp) {
                                        $biayalain[] = $bp['bl_nama'];
                                    }

                                    $hargaTrx =  $row['pu_status'] == 1 ? ($row['pu_harga'] ? str_replace(',', '.', $row['pu_harga']) : 0) : 0;
                                    $nilaiKpr =  $row['pu_status'] == 1 ? ($row['pu_nilaiAccKpr'] ? str_replace(',', '.', $row['pu_nilaiAccKpr']) : 0) : 0;
                                    $sisaKpr = $nilaiKpr - $totalBayarKpr;
                                    $sisa = ($hargaTrx - $nilaiKpr) - $totalBayar;
                                    $totalSisa = $sisaKpr + $sisa;

                                    $grandTotalHarga += $hargaTrx;
                                    $grandTotalKpr += $nilaiKpr;
                                    $grandTotalBayarKpr += $totalBayarKpr;
                                    $grandTotalSisaKpr += $sisaKpr;

                                    $grandTotalBayar += $totalBayar;
                                    $grandTotalSisa += $sisa;
                                    $grandTotalSisaPiutang += $totalSisa;
                            ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td class="text-uppercase">
                                            <?= date('d/m/Y', strtotime($row['created_at'])); ?>
                                        </td>
                                        <td>
                                            <a href="/dashboard/piutangpenjualan/<?= $row['pu_id']; ?>">
                                                <?= $row['pu_nomor'] ? $row['pu_nomor'] : '-'; ?>
                                            </a>
                                        </td>
                                        <td class="text-uppercase"><?= $row['cust_nama'] ? $row['cust_nama'] : '-'; ?></td>
                                        <td class="text-uppercase"><?= $row['pu_jenis'] ? ($row['pu_jenis'] == 'kredit' ? 'IN HOUSE' : $row['pu_jenis']) : '-'; ?></td>
                                        <td class="text-uppercase"><?= $row['unit_nama'] ? $row['unit_nama'] . ' (' . $row['type_nama'] . ')' : '-'; ?></td>
                                        <td><?= $row['pu_kpr'] ? $row['kpr_nama'] : '-'; ?></td>
                                        <td>
                                            <?php if ($row['pu_jenis'] == 'kpr') {
                                                if ($row['pu_nilaiAccKpr'] > 0) {
                                                    if ($row['pu_tglRealisasiKpr']) {
                                            ?>
                                                        <span class="badge badge-success">REALISASI</span>
                                                    <?php } else { ?>
                                                        <span class="badge badge-info">BLM. REALISASI</span>
                                                    <?php }
                                                } else { ?>
                                                    <span class="badge badge-info">BLM. ACC</span>
                                                <?php }
                                            } else { ?>
                                                -
                                            <?php } ?>
                                        </td>
                                        <td align="right"><?= number_format(($row['pu_status'] == 1 ? $hargaTrx : 0), 0, ".", "."); ?></td>

                                        <td align="right"><?= number_format(($row['pu_status'] == 1 ? $nilaiKpr : 0), 0, ".", "."); ?></td>
                                        <td align="right"><?= number_format(($row['pu_status'] == 1 ? $totalBayarKpr : 0), 0, ".", "."); ?></td>
                                        <td align="right"><?= number_format(($row['pu_status'] == 1 ? $sisaKpr : 0), 0, ".", "."); ?></td>

                                        <td align="right"><?= number_format(($row['pu_status'] == 1 ? $totalBayar : 0), 0, ".", "."); ?></td>
                                        <td align="right"><?= number_format(($row['pu_status'] == 1 ? $sisa : 0), 0, ".", "."); ?></td>
                                        <td align="right"><?= number_format(($row['pu_status'] == 1 ? $totalSisa : 0), 0, ".", "."); ?></td>

                                        <td align="center">
                                            <?php
                                            if ($row['pu_status'] == 1) {
                                                if ($totalSisa > 0) { ?>
                                                    <span class="badge badge-info">PROSES</span>
                                                <?php } else if ($totalSisa == 0) { ?>
                                                    <span class="badge badge-success">LUNAS</span>
                                                <?php } else if ($totalSisa < 0) { ?>
                                                    <span class="badge badge-info">LEBIH BAYAR</span>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <span class="badge badge-danger">DIBATALKAN</span><br /><br />
                                                <span class="text-dark">PENGEMBALIAN:</span> <?= number_format($pengembalian, 0, ".", "."); ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <a href="/dashboard/editPenjualan/<?= $row['pu_id']; ?>" class="btn btn-warning btn-sm mr-2 mb-2"><i class="fas fa-edit"></i></a>
                                            <form class="d-inline" action="/dashboard/deletePenjualan" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['pu_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm mb-2" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            } else { ?>
                                <tr>
                                    <td colspan="17" class="text-center font-italic">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="8" class="font-weight-bold text-uppercase" align="center">GRAND TOTAL</td>
                                <td align="right" class="font-weight-bold text-uppercase"><?= number_format($grandTotalHarga, 0, ".", "."); ?></td>
                                <td align="right" class="font-weight-bold text-uppercase"><?= number_format($grandTotalKpr, 0, ".", "."); ?></td>
                                <td align="right" class="font-weight-bold text-uppercase"><?= number_format($grandTotalBayarKpr, 0, ".", "."); ?></td>
                                <td align="right" class="font-weight-bold text-uppercase"><?= number_format($grandTotalSisaKpr, 0, ".", "."); ?></td>
                                <td align="right" class="font-weight-bold text-uppercase"><?= number_format($grandTotalBayar, 0, ".", "."); ?></td>
                                <td align="right" class="font-weight-bold text-uppercase"><?= number_format($grandTotalSisa, 0, ".", "."); ?></td>
                                <td align="right" class="font-weight-bold text-uppercase"><?= number_format($grandTotalSisaPiutang, 0, ".", "."); ?></td>
                                <td colspan="4"></td>
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
<?= $this->endSection(); ?>