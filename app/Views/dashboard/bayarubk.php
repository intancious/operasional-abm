<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();

$trxupahModel = new \App\Models\TrxupahModel();
$trxupahItemModel = new \App\Models\TrxupahItemModel();
$trxupahBayarModel = new \App\Models\TrxupahBayarModel();
$rekeningModel = new \App\Models\KoderekeningModel();
$kasbonModel = new \App\Models\KasbonModel();
$tukangModel = new \App\Models\TukangModel();
$userModel = new \App\Models\UserModel();

$bon = $trxupah['tu_bon'] ? str_replace(',', '.', $trxupah['tu_bon']) : 0;
$lembur = $trxupah['tu_lembur'] ? str_replace(',', '.', $trxupah['tu_lembur']) : 0;
$total = $trxupah['tu_totalupah'] ? str_replace(',', '.', $trxupah['tu_totalupah']) : 0;
$totalUpah = ($total + $lembur) - $bon;

$totalBayar = $trxupahBayarModel->totalBayarUpah($trxupah['tu_id']);
$sisaUpah = $totalUpah - $totalBayar;
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="/dashboard/ubk"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<?= session()->get('pesan'); ?>

<div class="row">
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered small">
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">NO. FAKTUR</td>
                        <td><?= $trxupah['tu_nomor']; ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">TGL. TRANSAKSI</td>
                        <td><?= date('d/m/Y H:i', strtotime($trxupah['tu_tanggal'])); ?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">TUKANG</td>
                        <td><?= $trxupah['tu_tukang'] ? strtoupper($trxupah['tk_nama']) : '-'; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><?= $trxupah['tu_keterangan'] ? strtoupper($trxupah['tu_keterangan']) : '-'; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="/dashboard/prosesbayarupah" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="id" id="id" value="<?= $trxupah['tu_id']; ?>">
                    <table class="table table-bordered small">
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">TGL. BAYAR</td>
                            <td colspan="2">
                                <input <?= $sisaUpah <= 0 ? 'disabled' : 'required'; ?> type="text" name="tanggal" id="tanggalTrx" value="<?= date('d-m-Y'); ?>" class="form-control form-control-sm">
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">UPAH HARIAN</td>
                            <td colspan="2"><?= number_format($total, 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">LEMBUR</td>
                            <td colspan="2"><?= number_format($lembur, 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">TOTAL UPAH</td>
                            <input type="hidden" name="tupah" value="<?= $total + $lembur; ?>">
                            <td style="vertical-align: middle;"><?= number_format($total + $lembur, 2, ',', '.'); ?></td>
                            <td>
                                <div class="form-group">
                                    <label for="debetTupah">DEBET</label>
                                    <select name="debetTupah" id="debetTupah" class="form-control form-control-sm <?= $validation->hasError('debetTupah') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                        <option value="" data-tokens="">:: PILIH ::</option>
                                        <?php
                                        foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                            <option value="<?= $row['rek_id']; ?>" <?= '801' == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">BON</td>
                            <input type="hidden" name="tbon" value="<?= $bon; ?>">
                            <td style="vertical-align: middle;"><?= number_format($bon, 2, ',', '.'); ?></td>
                            <td>
                                <div class="form-group">
                                    <label for="kreditBon">KREDIT</label>
                                    <select name="kreditBon" id="kreditBon" class="form-control form-control-sm <?= $validation->hasError('kreditBon') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                        <option value="" data-tokens="">:: PILIH ::</option>
                                        <?php
                                        foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                            <option value="<?= $row['rek_id']; ?>" <?= '694' == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">SISA UPAH</td>
                            <?php $supah = ($total + $lembur) - $bon; ?>
                            <input type="hidden" name="supah" value="<?= $supah; ?>">
                            <td style="vertical-align: middle;"><?= number_format($supah, 2, ',', '.'); ?></td>
                            <td>
                                <div class="form-group">
                                    <label for="kreditSupah">KREDIT</label>
                                    <select name="kreditSupah" id="kreditSupah" class="form-control form-control-sm <?= $validation->hasError('kreditSupah') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                        <option value="" data-tokens="">:: PILIH ::</option>
                                        <?php
                                        foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                            <option value="<?= $row['rek_id']; ?>" <?= '21' == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">SUDAH DIBAYAR</td>
                            <td colspan="2"><?= number_format($totalBayar, 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">BAYAR</td>
                            <td colspan="2">
                                <input <?= $sisaUpah <= 0 ? 'disabled' : 'readonly'; ?> type="text" name="bayar" id="bayar" class="form-control form-control-sm" value="<?= $supah - $totalBayar; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">KETERANGAN</td>
                            <td colspan="2"><textarea <?= $sisaUpah <= 0 ? 'disabled' : ''; ?> name="catatan" id="catatan" rows="2" class="form-control form-control-sm"></textarea></td>
                        </tr>
                    </table>

                    <!-- <table class="table table-bordered small mt-4">
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
                    </table> -->
                    <div class="form-group mt-4 mb-0">
                        <button onclick="return confirm('Yakin ingin melanjutkan?');" type="submit" <?= $sisaUpah <= 0 ? 'disabled' : ''; ?> class="btn btn-primary btn-sm"><i class="fas fa-sm fa-credit-card fa-sm mr-1"></i> BAYAR</button>
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
                                <th>TOTAL UPAH</th>
                                <th>DEBET</th>
                                <th>BON</th>
                                <th>KREDIT</th>
                                <th>SISA UPAH</th>
                                <th>KREDIT</th>
                                <th>BAYAR</th>
                                <th>KETERANGAN</th>
                                <th>USER</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pembayarans = $trxupahBayarModel->where('trxupah_bayar.tub_trxupah', $trxupah['tu_id'])
                                ->join('user', 'user.usr_id = trxupah_bayar.tub_user', 'left')
                                ->select('trxupah_bayar.*, user.usr_nama')
                                ->orderBy('trxupah_bayar.tub_id', 'DESC')->findAll();
                            $grandUpah = 0;
                            $grandBon = 0;
                            $grandSisa = 0;
                            $totalBayarUpah = 0;
                            if ($pembayarans) {
                                $i = 1;
                                foreach ($pembayarans as $row) {
                                    $totalUpah = $row['tub_tupah'] ? str_replace(',', '.', $row['tub_tupah']) : 0;
                                    $totalBon = $row['tub_tbon'] ? str_replace(',', '.', $row['tub_tbon']) : 0;
                                    $sisaUpah = $row['tub_tsupah'] ? str_replace(',', '.', $row['tub_tsupah']) : 0;
                                    $bayar = $row['tub_bayar'] ? str_replace(',', '.', $row['tub_bayar']) : 0;

                                    $grandUpah += $totalUpah;
                                    $grandBon += $totalBon;
                                    $grandSisa += $sisaUpah;
                                    $totalBayarUpah += $bayar;

                                    $tupahDebet = $rekeningModel->find($row['tub_tupahdebet']);
                                    $tbonKredit = $rekeningModel->find($row['tub_tbonkredit']);
                                    $supahKredit = $rekeningModel->find($row['tub_tsupahkredit']);

                                    $rekDebet = $rekeningModel->find($row['tub_debet']);
                                    $rekKredit = $rekeningModel->find($row['tub_kredit']);
                            ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['tub_tanggal'])); ?></td>
                                        <td><?= $row['tub_nomor']; ?></td>
                                        <td><?= number_format($totalUpah, 2, ',', '.'); ?></td>
                                        <td><?= $row['tub_tupahdebet'] ? '(' . $tupahDebet['rek_kode'] . ') ' . $tupahDebet['rek_nama'] : '-'; ?></td>
                                        <td><?= number_format($totalBon, 2, ',', '.'); ?></td>
                                        <td><?= $row['tub_tbonkredit'] ? '(' . $tbonKredit['rek_kode'] . ') ' . $tbonKredit['rek_nama'] : '-'; ?></td>
                                        <td><?= number_format($sisaUpah, 2, ',', '.'); ?></td>
                                        <td><?= $row['tub_tsupahkredit'] ? '(' . $supahKredit['rek_kode'] . ') ' . $supahKredit['rek_nama'] : '-'; ?></td>
                                        <td><?= number_format($bayar, 2, ',', '.'); ?></td>
                                        <td><?= $row['tub_keterangan'] ? $row['tub_keterangan'] : '-'; ?></td>
                                        <td><?= $row['tub_user'] ? $row['usr_nama'] : '-'; ?></td>
                                        <td>
                                            <form class="d-inline ml-1" action="/dashboard/deleteitembayarupah" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['tub_id']; ?>">
                                                <button <?= $sisaUpah <= 0 ? 'disabled' : ''; ?> type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="13" class="small font-italic text-center">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="3" class="font-weight-bold text-right">GRAND TOTAL</td>
                                <td class="font-weight-bold"><?= number_format($grandUpah, 2, ',', '.'); ?></td>
                                <td></td>
                                <td class="font-weight-bold"><?= number_format($grandBon, 2, ',', '.'); ?></td>
                                <td></td>
                                <td class="font-weight-bold"><?= number_format($grandSisa, 2, ',', '.'); ?></td>
                                <td></td>
                                <td class="font-weight-bold"><?= number_format($totalBayarUpah, 2, ',', '.'); ?></td>
                                <td></td>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentUpahModal" tabindex="-1" role="dialog" aria-labelledby="paymentUpahModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paymentUpahModalLabel">Perbarui Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <form action="/dashboard/updatePembayaranItemUpah" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" name="idUpah" id="idUpah">
                    <input type="hidden" name="idTrx" id="idTrx">
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