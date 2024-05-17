<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$totalItems = ($totalRows / 100);
$userModel = new \App\Models\UserModel();
$pembelianModel = new \App\Models\PembelianModel();
$rekeningModel = new \App\Models\KoderekeningModel();
$kkModel = new \App\Models\KasKecilModel();
$ongkirModel = new \App\Models\OngkirPembelianModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/addKaskecil"><i class="fas fa-sm fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/kaskecil"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                        <label for="q" class="small">NOMOR</label>
                        <input type="text" name="q" id="q" class="form-control form-control-sm" placeholder="Nomor Transaksi" value="<?= $request->getVar('q'); ?>">
                    </div>
                    <div class="form-group mb-0 mt-4">
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
                                    <option value="<?= current_url(); ?>?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&q=<?= $request->getVar('q'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
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
                                <th>NOMOR</th>
                                <th>JENIS</th>
                                <th>PENERIMA</th>
                                <th>NOMINAL</th>
                                <th>DIGUNAKAN</th>
                                <th>DIKEMBALIKAN</th>
                                <th>SISA</th>
                                <th>STATUS</th>
                                <th>USER</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grandTotal = 0;
                            $grandTotalPembelian = 0;
                            $grandTotalKembali = 0;
                            $grandTotalSaldo = 0;
                            if ($kaskecil) {
                                $i = 1;
                                foreach ($kaskecil as $row) {
                                    $nominal = $row['kk_nominal'] ? str_replace(',', '.', $row['kk_nominal']) : 0;
                                    $kembali = $row['kk_kembali'] ? str_replace(',', '.', $row['kk_kembali']) : 0;
                                    $grandTotal += $nominal;
                                    $grandTotalKembali += $kembali;

                                    $digunakan = $kkModel->getTransaksi($row['kk_jenis'], $row['kk_id']);

                                    $saldoAkhir = $nominal - $digunakan - $kembali;
                                    $grandTotalPembelian += $digunakan;
                                    $grandTotalSaldo += $saldoAkhir;

                                    if ($row['kk_jenis'] == 1) {
                                        $link = 'detailPembelianTunai';
                                    } else if ($row['kk_jenis'] == 4) {
                                        $link = 'detailKasbon';
                                    } else {
                                        $link = 'detailOperasional/' . $row['kk_jenis'];
                                    }
                            ?>
                                    <tr class="text-uppercase">
                                        <td style="vertical-align: middle;"><?= $i++; ?></td>
                                        <td style="vertical-align: middle;"><?= date('d/m/Y H:i', strtotime($row['kk_tanggal'])); ?></td>
                                        <td style="vertical-align: middle;">
                                            <a href="/dashboard/editKaskecil/<?= $row['kk_id']; ?>">
                                                <?= $row['kk_nomor']; ?>
                                            </a>
                                        </td>
                                        <td style="vertical-align: middle;"><?= $kkModel->getJenisKas($row['kk_jenis'])['name']; ?></td>
                                        <td style="vertical-align: middle;"><?= strtoupper($row['usr_nama']); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($nominal, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;">
                                            <a href="/dashboard/<?= $link; ?>/<?= $row['kk_id']; ?>">
                                                <?= number_format($digunakan, 2, ',', '.'); ?>
                                            </a>
                                        </td>
                                        <td style="vertical-align: middle;"><?= number_format($kembali, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($saldoAkhir, 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;">
                                            <a href="#" data-id="<?= $row['kk_id']; ?>" class="editStatusKk" data-toggle="modal" data-target="#statusKasKecil">
                                                <?= $row['kk_status'] == 1 ? 'BELUM DILAPORKAN' : ($row['kk_status'] == 2 ? 'TELAH DILAPORKAN' : ''); ?>
                                            </a>
                                        </td>
                                        <td style="vertical-align: middle;"><?= $row['kk_approval'] ? $userModel->find($row['kk_approval'])['usr_nama'] : '-'; ?></td>
                                        <td style="vertical-align: middle;">
                                            <?php if ($digunakan <= 0) { ?>
                                                <form class="d-inline ml-1" action="/dashboard/deleteKaskecil" method="post">
                                                    <?= csrf_field(); ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="id" value="<?= $row['kk_id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                                                </form>
                                            <?php } else { ?>
                                                <button type="submit" disabled class="btn btn-danger btn-sm"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="12" class="font-italic text-center">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="5" class="font-weight-bold text-right">GRAND TOTAL</td>
                                <td><?= number_format($grandTotal, 2, ',', '.'); ?></td>
                                <td><?= number_format($grandTotalPembelian, 2, ',', '.'); ?></td>
                                <td><?= number_format($grandTotalKembali, 2, ',', '.'); ?></td>
                                <td><?= number_format($grandTotalSaldo, 2, ',', '.'); ?></td>
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
                                    <option value="<?= current_url(); ?>?startDate=<?= $request->getVar('startDate'); ?>&endDate=<?= $request->getVar('endDate'); ?>&q=<?= $request->getVar('q'); ?>&page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="statusKasKecil" tabindex="-1" role="dialog" aria-labelledby="statusKasKecilLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-uppercase" id="statusKasKecilLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="diterima">Diterima</label>
                                <input type="text" name="diterima" id="diterima" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="digunakan">Digunakan</label>
                                <input type="text" name="digunakan" id="digunakan" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sisa">Sisa</label>
                        <input type="text" name="sisa" id="sisa" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="kembali">Dikembalikan</label>
                        <input type="text" name="kembali" id="kembali" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="statusKasKecil">Status</label>
                        <select name="statusKasKecil" id="statusKasKecil" class="form-control">
                            <option value="1">BELUM DILAPORKAN</option>
                            <option value="2">TELAH DILAPORKAN</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="debet">Debet</label>
                        <select name="debet" id="debet" class="form-control <?= $validation->hasError('debet') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= old('debet') == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('debet'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="kredit">Kredit</label>
                        <select name="kredit" id="kredit" class="form-control <?= $validation->hasError('kredit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= old('kredit') == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('kredit'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>