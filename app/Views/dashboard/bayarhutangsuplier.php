<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();

$pembelianModel = new \App\Models\PembelianModel();
$hsbModel = new \App\Models\HutangsuplierBayarModel();
$rekeningModel = new \App\Models\KoderekeningModel();

$pembelian = $pembelianModel->find($hutangsuplier['hs_pembelian']);
$ongkir = $pembelian['pb_ongkir'] ? str_replace(',', '.', $pembelian['pb_ongkir']) : 0;

$totalH = $hutangsuplier['hs_total'] ? str_replace(',', '.', $hutangsuplier['hs_total']) : 0;
$totalHutang = ($totalH + $ongkir);
$totalBayar = $hsbModel->totalBayar($hutangsuplier['hs_id']);
$sisaHutang = $totalHutang - $totalBayar;
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="/dashboard/hutangsuplier"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<?= session()->get('pesan'); ?>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered small">
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">NO. FAKTUR</td>
                        <td><?= $hutangsuplier['hs_nomor']; ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">TGL. TRANSAKSI</td>
                        <td><?= date('d/m/Y H:i', strtotime($hutangsuplier['hs_tanggal'])); ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">NAMA SUPLIER</td>
                        <td><?= $hutangsuplier['suplier_nama'] ? strtoupper($hutangsuplier['suplier_nama']) : '-'; ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">ALAMAT SUPLIER</td>
                        <td><?= $hutangsuplier['suplier_alamat'] ? strtoupper($hutangsuplier['suplier_alamat']) : '-'; ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">NO. HP/TELP</td>
                        <td><?= $hutangsuplier['suplier_telp'] ? $hutangsuplier['suplier_telp'] : '-'; ?></td>
                    </tr>
                    <?php
                    $tglTempo = date('d/m/Y', strtotime($hutangsuplier['hs_tempo']));
                    ?>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">TGL. JATUH TEMPO</td>
                        <td><?= $hutangsuplier['hs_tempo'] ? $tglTempo : '-'; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="/dashboard/prosesbayarhs" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="id" id="id" value="<?= $hutangsuplier['hs_id']; ?>">
                    <table class="table table-bordered small">
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">TGL. BAYAR</td>
                            <td>
                                <input <?= $sisaHutang <= 0 ? 'disabled' : 'required'; ?> type="text" name="tanggal" id="tanggalTrx" value="<?= date('d-m-Y'); ?>" class="form-control form-control-sm">
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">TOTAL HUTANG</td>
                            <td><?= number_format($totalHutang, 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">TOTAL BAYAR</td>
                            <td><?= number_format($totalBayar, 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">SISA HUTANG</td>
                            <td>
                                <div id="sisaHutang"><?= number_format(($sisaHutang > 0 ? $sisaHutang : 0), 2, ',', '.'); ?></div>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">BAYAR</td>
                            <td>
                                <input <?= $sisaHutang <= 0 ? 'disabled' : 'required'; ?> type="text" name="bayar" id="bayar" class="form-control form-control-sm">
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">KETERANGAN</td>
                            <td><textarea <?= $sisaHutang <= 0 ? 'disabled' : ''; ?> name="catatan" id="catatan" rows="2" class="form-control form-control-sm"></textarea></td>
                        </tr>
                    </table>

                    <table class="table table-bordered small mt-4">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <td style="vertical-align: middle; font-weight: bold;">DEBET</td>
                                <td style="vertical-align: middle; font-weight: bold;">KREDIT</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="debet" id="debet" class="form-control form-control-sm <?= $validation->hasError('debet') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                        <option value="" data-tokens="">:: PILIH ::</option>
                                        <?php
                                        foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                            <option value="<?= $row['rek_id']; ?>" <?= old('debet') == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="kredit" id="kredit" class="form-control form-control-sm <?= $validation->hasError('kredit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                        <option value="" data-tokens="">:: PILIH ::</option>
                                        <?php
                                        foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                            <option value="<?= $row['rek_id']; ?>" <?= old('kredit') == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group mt-4 mb-0">
                        <button onclick="return confirm('Yakin ingin melanjutkan?');" type="submit" <?= $sisaHutang <= 0 ? 'disabled' : ''; ?> class="btn btn-primary btn-sm"><i class="fas fa-sm fa-credit-card fa-sm mr-1"></i> BAYAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="d-sm-flex align-items-center justify-content-between mt-5 mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        RIWAYAT PEMBAYARAN
    </h1>
</div>
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered small" id="dataTable2" style="width:100%">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <th>#</th>
                                <th>TANGGAL</th>
                                <th>NOMOR</th>
                                <th>NOMINAL</th>
                                <th>DEBET</th>
                                <th>KREDIT</th>
                                <th>KETERANGAN</th>
                                <th>USER</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pembayarans = $hsbModel->where('hutangsuplier_bayar.hb_hutangsuplier', $hutangsuplier['hs_id'])
                                ->join('user', 'user.usr_id = hutangsuplier_bayar.hb_user', 'left')
                                ->select('hutangsuplier_bayar.*, user.usr_nama')
                                ->orderBy('hutangsuplier_bayar.hb_id', 'DESC')->findAll();
                            $totalBayar = 0;
                            if ($pembayarans) {
                                $i = 1;
                                foreach ($pembayarans as $row) {
                                    $bayar = $row['hb_bayar'] ? str_replace(',', '.', $row['hb_bayar']) : 0;
                                    $totalBayar += $bayar;
                                    $rekDebet = $rekeningModel->find($row['hb_debet']);
                                    $rekKredit = $rekeningModel->find($row['hb_kredit']);
                            ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['hb_tanggal'])); ?></td>
                                        <td>
                                            <a href="#" class="editPaymentHutang" data-id="<?= $row['hb_id']; ?>" data-toggle="modal" data-target="#paymentModal">
                                                <?= $row['hb_nomor']; ?>
                                            </a>
                                        </td>
                                        <td><?= number_format($bayar, 2, ',', '.'); ?></td>
                                        <td><?= $row['hb_debet'] ? '(' . $rekDebet['rek_kode'] . ') ' . $rekDebet['rek_nama'] : '-'; ?></td>
                                        <td><?= $row['hb_kredit'] ? '(' . $rekKredit['rek_kode'] . ') ' . $rekKredit['rek_nama'] : '-'; ?></td>
                                        <td><?= $row['hb_keterangan'] ? $row['hb_keterangan'] : '-'; ?></td>
                                        <td><?= $row['hb_user'] ? $row['usr_nama'] : '-'; ?></td>
                                        <td>
                                            <form class="d-inline ml-1" action="/dashboard/deleteitembayarhs" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['hb_id']; ?>">
                                                <button <?= $sisaHutang <= 0 ? 'disabled' : ''; ?> type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="9" class="small font-italic text-center">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="3" class="font-weight-bold text-right">TOTAL BAYAR</td>
                                <td class="font-weight-bold"><?= number_format($totalBayar, 2, ',', '.'); ?></td>
                                <td colspan="5">&nbsp;</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="row justify-content-end mt-4">
                    <div class="col-md-4">
                        <div class="table-responsive">
                            <table class="table table-bordered small" style="width:100%">
                                <!-- <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                    <td class="font-weight-bold text-right" align="right">PEMBELIAN</td>
                                    <td class="font-weight-bold" align="right"><?= number_format($totalH, 2, ',', '.'); ?></td>
                                </tr> -->
                                <!-- <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                    <td class="font-weight-bold text-right" align="right">ONGKOS KIRIM</td>
                                    <td class="font-weight-bold" align="right"><?= number_format($ongkir, 2, ',', '.'); ?></td>
                                </tr> -->
                                <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                    <td class="font-weight-bold text-right" align="right">TOTAL HUTANG</td>
                                    <td class="font-weight-bold" align="right"><?= number_format($totalHutang, 2, ',', '.'); ?></td>
                                </tr>
                                <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                    <td class="font-weight-bold text-right" align="right">TOTAL BAYAR</td>
                                    <td class="font-weight-bold" align="right"><?= number_format($totalBayar, 2, ',', '.'); ?></td>
                                </tr>
                                <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                    <td class="font-weight-bold text-right" align="right">SISA HUTANG</td>
                                    <td class="font-weight-bold" align="right"><?= number_format(($totalBayar <= $totalHutang ? $sisaHutang : 0), 2, ',', '.'); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paymentModalLabel">Perbarui Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <form action="/dashboard/updatePembayaranItemHs" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" name="idHb" id="idHb">
                    <input type="hidden" name="idHs" id="idHs">
                    <div class="form-group">
                        <label for="tanggalTrxEdit">Tanggal</label>
                        <input required type="text" name="tanggalTrxEdit" id="tanggalTrxEdit" class="form-control form-control-sm">
                    </div>
                    <div class="form-group">
                        <label for="bayarEdit">Bayar</label>
                        <input required type="text" name="bayarEdit" id="bayarEdit" class="form-control form-control-sm">
                    </div>
                    <div class="form-group">
                        <label for="keteranganEdit">Keterangan</label>
                        <textarea name="keteranganEdit" id="keteranganEdit" rows="3" class="form-control form-control-sm"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="debetEdit">Debet</label>
                        <select name="debetEdit" id="debetEdit" class="form-control form-control-sm <?= $validation->hasError('debetEdit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kreditEdit">Kredit</label>
                        <select name="kreditEdit" id="kreditEdit" class="form-control form-control-sm <?= $validation->hasError('kreditEdit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>