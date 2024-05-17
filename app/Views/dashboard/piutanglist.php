<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$totalItems = ($totalRows / 250);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/piutanglist"><i class="fas fa-sync-alt ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg">
        <div class="card shadow mb-4 bg-primary text-white">
            <div class="card-body">
                <form action="" method="get">
                    <!-- <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jenis">JENIS TRANSAKSI</label>
                                <select name="jenis" id="jenis" class="form-control form-control-sm <?= $validation->hasError('jenis') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: SEMUA ::</option>
                                    <option value="cash" data-tokens="cash" <?= $request->getVar('jenis') == 'cash' ? 'selected' : ''; ?>>CASH</option>
                                    <option value="kpr" data-tokens="kpr" <?= $request->getVar('jenis') == 'kpr' ? 'selected' : ''; ?>>KPR</option>
                                    <option value="kredit" data-tokens="kredit" <?= $request->getVar('jenis') == 'kredit' ? 'selected' : ''; ?>>IN HOUSE</option>
                                </select>
                            </div>
                        </div>
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
                    </div> -->
                    <div class="row justify-content-center">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nomor">NO. TRANSAKSI</label>
                                <input type="text" name="nomor" id="nomor" class="form-control form-control-sm" value="<?= $request->getVar('nomor'); ?>" placeholder="NO. TRANSAKSI">
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
                                <th>NO. TRANSAKSI</th>
                                <th>CUSTOMER</th>
                                <th>UNIT</th>
                                <th>TRANSAKSI</th>
                                <th>ANGSURAN</th>
                                <th>JTH. TEMPO</th>
                                <th>TAGIHAN</th>
                                <th>TGL. BAYAR</th>
                                <th>BAYAR</th>
                                <th>SISA</th>
                                <th>KETERANGAN</th>
                                <th>STATUS</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            $totalTagihan = 0;
                            $totalBayar = 0;
                            $totalSisa = 0;
                            if ($tagihan) {
                                foreach ($tagihan as $row) :
                                    $nilaiTagihan = $row['tp_nilai'] ? str_replace(',', '.', $row['tp_nilai']) : $row['tp_nilai'];
                                    $nilaiBayar = $row['tp_nominal'] ? str_replace(',', '.', $row['tp_nominal']) : $row['tp_nominal'];
                                    $sisa = $nilaiTagihan - $nilaiBayar;
                                    $totalTagihan += $nilaiTagihan;
                                    $totalBayar += $nilaiBayar;
                                    $totalSisa += $sisa;
                            ?>
                                    <tr <?= $sisa > 0 ? 'class="text-danger"' : ''; ?>>
                                        <td><?= $i++; ?></td>
                                        <td>
                                            <a <?= $sisa > 0 ? 'class="text-danger"' : ''; ?> href="/dashboard/bayarpiutang/<?= $row['tp_id']; ?>">
                                                <?= $row['tp_nomor'] ? $row['tp_nomor'] : '-'; ?>
                                            </a>
                                        </td>
                                        <td class="text-uppercase"><?= $row['cust_nama'] ? $row['cust_nama'] : '-'; ?></td>
                                        <td class="text-uppercase"><?= $row['unit_nama'] ? $row['unit_nama'] : '-'; ?></td>
                                        <td class="text-uppercase"><?= $row['tp_jenis'] ? ($row['tp_jenis'] == 1 ? 'BAYAR CUSTOMER' : ($row['tp_jenis'] == 2 ? 'BAYAR KPR' : '-')) : '-'; ?></td>
                                        <td class="text-uppercase"><?= $row['tp_angsuran'] ? $row['tp_angsuran'] : '-'; ?></td>
                                        <td><?= $row['tp_jthtempo'] ? date('d/m/Y', strtotime($row['tp_jthtempo'])) : '-'; ?></td>
                                        <td align="right"><?= number_format($nilaiTagihan, 0, ".", "."); ?></td>
                                        <td><?= $row['tp_tglbayar'] ? date('d/m/Y', strtotime($row['tp_tglbayar'])) : '-'; ?></td>
                                        <td align="right"><?= number_format($nilaiBayar, 0, ".", "."); ?></td>
                                        <td align="right"><?= number_format($sisa, 0, ".", "."); ?></td>
                                        <td class="text-uppercase"><?= $row['tp_keterangan'] ? $row['tp_keterangan'] : '-'; ?></td>
                                        <td>
                                            <?php
                                            if ($sisa <= 0) { ?>
                                                <span class="badge badge-success">LUNAS</span>
                                            <?php } else { ?>
                                                <span class="badge badge-danger">BLM. LUNAS</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <form class="d-inline" action="/dashboard/deletePiutang" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['tp_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm mb-2" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            } else { ?>
                                <tr>
                                    <td colspan="14" class="text-center font-italic">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="7" class="font-weight-bold text-uppercase" align="center">GRAND TOTAL</td>
                                <td align="right" class="font-weight-bold text-uppercase"><?= number_format($totalTagihan, 0, ".", "."); ?></td>
                                <td></td>
                                <td align="right" class="font-weight-bold text-uppercase"><?= number_format($totalBayar, 0, ".", "."); ?></td>
                                <td align="right" class="font-weight-bold text-uppercase"><?= number_format($totalSisa, 0, ".", "."); ?></td>
                                <td colspan="3"></td>
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