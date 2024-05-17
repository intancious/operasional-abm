<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$barangModel = new \App\Models\BarangModel();
$kategoriModel = new \App\Models\KabarModel();
$satuanModel = new \App\Models\SatuanModel();
$kasKecilModel = new \App\Models\KasKecilModel();
$supplierModel = new \App\Models\SuplierModel();
$pembelianModel = new \App\Models\PembelianModel();
$pembelianItemModel = new \App\Models\PembelianItemModel();
$rekeningModel = new \App\Models\KoderekeningModel();
$bagianModel = new \App\Models\BagianModel();
$bagian = $bagianModel->find(session()->get('usr_bagian'));
$akses = explode(',', $bagian['bagian_akses']);

$kasKecil = $kasKecilModel->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
    ->where(['kas_kecil.kk_status' => 1, 'kas_kecil.kk_jenis' => 1])
    ->select('kas_kecil.*, user.usr_nama')
    ->orderBy('kas_kecil.kk_tanggal', 'DESC')->findAll();
$categories = $kategoriModel->orderBy('kabar_nama', 'ASC')->findAll();
$satuan = $satuanModel->orderBy('satuan_nama', 'ASC')->findAll();
$suppliers = $supplierModel->orderBy('suplier_nama', 'ASC')->findAll();
$barang = $barangModel->orderBy('barang_nama', 'ASC')->findAll();

$pembelianItems = $pembelianItemModel->where('pembelian_item.pi_pembelian', $pembelian['pb_id'])
    ->join('barang', 'barang.barang_id = pembelian_item.pi_barang', 'left')
    ->join('suplier', 'suplier.suplier_id = pembelian_item.pi_suplier', 'left')
    ->select('pembelian_item.*, barang.barang_nama, suplier.suplier_id, suplier.suplier_nama')
    ->orderBy('pembelian_item.pi_id', 'DESC')->findAll();

function hitungHari($start, $end)
{
    $dt1 = strtotime($start);
    $dt2 = strtotime($end);
    $diff = abs($dt2 - $dt1);
    return $diff / 86400; // 86400 detik sehari
}
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="/dashboard/pembelian"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
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
                <form action="/dashboard/updateFakturPembelian" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="id" id="id" value="<?= $pembelian['pb_id']; ?>">
                    <table class="table table-bordered small">
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">TGL. TRANSAKSI</td>
                            <td>
                                <input type="text" name="tanggal" id="tanggalTrx" value="<?= date('d-m-Y', strtotime($pembelian['pb_tanggal'])); ?>" class="form-control form-control-sm">
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">NO. FAKTUR</td>
                            <td>
                                <input readonly type="text" name="faktur" id="faktur" value="<?= $pembelian['pb_nomor']; ?>" class="form-control form-control-sm">
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">JENIS TRANSAKSI</td>
                            <td>
                                <select name="jenisTrx" id="jenisTrx" class="form-control form-control-sm">
                                    <option value="">:: PILIH ::</option>
                                    <option value="1" <?= $pembelian['pb_jenis'] == 1 ? 'selected' : ''; ?>>TUNAI</option>
                                    <option value="2" <?= $pembelian['pb_jenis'] == 2 ? 'selected' : ''; ?>>HUTANG</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="jenisTunai">
                            <td style="vertical-align: middle; font-weight: bold;">KAS KECIL</td>
                            <td>
                                <select name="kaskecil" id="kaskecil" class="form-control form-control-sm selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php foreach ($kasKecil as $row) { ?>
                                        <option value="<?= $row['kk_id']; ?>" data-tokens="(<?= $row['kk_nomor']; ?>) <?= $row['usr_nama']; ?>" <?= $pembelian['pb_kaskecil'] == $row['kk_id'] ? 'selected' : ''; ?>>(<?= $row['kk_nomor']; ?>) <?= strtoupper($row['usr_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="jenisTunai">
                            <td style="vertical-align: middle; font-weight: bold;">SALDO KAS</td>
                            <td>
                                <input type="text" name="saldoKas" id="saldoKas" class="form-control form-control-sm" value="0" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">CATATAN</td>
                            <td>
                                <textarea name="catatan" id="catatan" rows="2" class="form-control form-control-sm"><?= $pembelian['pb_keterangan']; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;" align="right" colspan="2">
                                <button type="submit" class="btn btn-primary btn-sm">SIMPAN</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered small">
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">SUPLIER</td>
                        <td>
                            <select required name="suplier" id="suplier" class="form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php foreach ($suppliers as $row) { ?>
                                    <option value="<?= $row['suplier_id']; ?>" data-tokens="<?= $row['suplier_nama']; ?>"><?= strtoupper($row['suplier_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">BARANG</td>
                        <td>
                            <select required name="barang" id="barang" class="form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php foreach ($barang as $row) { ?>
                                    <option value="<?= $row['barang_id']; ?>" data-tokens="<?= $row['barang_nama']; ?>" <?= old('barang') == $row['barang_id'] ? 'selected' : ''; ?>><?= strtoupper($row['barang_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">NAMA BARANG</td>
                        <td>
                            <div id="namaBarang" class="text-uppercase">-</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">KATEGORI / SATUAN</td>
                        <td>
                            <div id="kategoriSatuanBarang" class="text-uppercase">-</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">QUANTITY</td>
                        <td>
                            <input type="text" name="qty" id="qtyP" class="form-control form-control-sm" value="0">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">HARGA</td>
                        <td>
                            <input type="text" name="harga" id="harga" class="form-control form-control-sm" value="0">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">TOTAL</td>
                        <td>
                            <input readonly type="text" name="total" id="total" class="form-control form-control-sm" value="0">
                        </td>
                    </tr>
                    <tr class="jenisHutang">
                        <td style="vertical-align: middle; font-weight: bold;">JTH. TEMPO (HARI)</td>
                        <td>
                            <input type="number" name="tempo" id="tempo" class="form-control form-control-sm" value="0">
                        </td>
                    </tr>
                </table>
                <table class="table table-bordered small mt-4" style="display: none;">
                    <thead class="thead-light font-weight-bold">
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;">DEBET</td>
                            <td style="vertical-align: middle; font-weight: bold;">KREDIT</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="debet" id="debet" class="form-control form-control-sm selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" <?= '114.2' === $row['rek_kode'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select name="kredit" id="kredit" class="form-control form-control-sm selectpicker" data-live-search="true">
                                    <option value="" data-tokens="">:: PILIH ::</option>
                                    <?php
                                    foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                        <option value="<?= $row['rek_id']; ?>" <?= $pembelian['pb_kredit'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group mt-4 mb-0">
                    <button type="button" class="btn btn-primary btn-sm addApproveCartPembelian"><i class="fas fa-sm fa-plus-circle mr-1"></i> TAMBAH</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-sm-flex align-items-center justify-content-between my-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        DATA BARANG BARU
    </h1>
</div>
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped small" id="dataTable" style="width:100%">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <th>#</th>
                                <th>NAMA BARANG</th>
                                <th>QTY</th>
                                <th>HARGA</th>
                                <th>SUBTOTAL</th>
                                <th>SUPLIER</th>
                                <!-- <th>DEBET</th> -->
                                <!-- <th>KREDIT</th> -->
                                <th>TEMPO</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody id="cartApproveListPembelian">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button class="btn btn-secondary btn-sm clearItem" type="button">
                                        <i class="fas fa-sm fa-broom fa-sm"></i> BERSIHKAN
                                    </button>
                                </td>
                                <td colspan="5">
                                    <button class="btn btn-primary btn-sm updatePembelian float-right" type="button">
                                        <i class="fas fa-sm fa-save fa-sm"></i> SIMPAN
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-sm-flex align-items-center justify-content-between my-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        BARANG SAAT INI
    </h1>
</div>
<form action="/dashboard/approvePembelian" method="post">
    <?= csrf_field(); ?>
    <input type="hidden" name="totalData" id="totalData" value="<?= count($pembelianItems); ?>">
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped small" id="customTable" style="width:100%">
                            <thead class="thead-light font-weight-bold">
                                <tr>
                                    <th>#</th>
                                    <th>BARANG</th>
                                    <th>PEMBELIAN</th>
                                    <th>DISETUJUI</th>
                                    <th>DATANG</th>
                                    <th>HARGA</th>
                                    <th>SUBTOTAL</th>
                                    <th>SUPLIER</th>
                                    <th>TEMPO (HARI)</th>
                                    <th>HAPUS</th>
                                </tr>
                            </thead>
                            <tbody id="dataItem" data-total="<?= count($pembelianItems); ?>">
                                <?php
                                $grandTotal = 0;
                                if ($pembelianItems) {
                                    $i = 1;
                                    foreach ($pembelianItems as $in => $row) {
                                        $qtybeli = $row['pi_qtybeli'] ? str_replace(',', '.', $row['pi_qtybeli']) : 0;
                                        $qtysetuju = $row['pi_qtymasuk'] ? str_replace(',', '.', $row['pi_qtymasuk']) : 0;
                                        $qtydatang = $row['pi_qtydatang'] ? str_replace(',', '.', $row['pi_qtydatang']) : 0;
                                        $harga = $row['pi_harga'] ? str_replace(',', '.', $row['pi_harga']) : 0;
                                        if ($qtydatang > 0) {
                                            $subtotal = $qtydatang * $harga;
                                        } else if ($qtysetuju > 0) {
                                            $subtotal = $qtysetuju * $harga;
                                        } else {
                                            $subtotal = $qtybeli * $harga;
                                        }
                                        $grandTotal += $subtotal;
                                ?>
                                        <tr class="text-uppercase">
                                            <input type="hidden" name="piId<?= $in; ?>" id="piId<?= $in; ?>" value="<?= $row['pi_id']; ?>">
                                            <td><?= $i++; ?></td>
                                            <td><?= $row['barang_nama']; ?></td>
                                            <td>
                                                <input class="form-control form-control-sm" type="text" name="qtybeli<?= $in; ?>" id="qtybeli<?= $in; ?>" value="<?= str_replace('.', ',', $qtybeli); ?>">
                                            </td>
                                            <td>
                                                <input class="form-control form-control-sm" type="text" name="qtysetuju<?= $in; ?>" id="qtysetuju<?= $in; ?>" value="<?= str_replace('.', ',', $qtysetuju); ?>">
                                            </td>
                                            <td>
                                                <input class="form-control form-control-sm" type="text" name="qtydatang<?= $in; ?>" id="qtydatang<?= $in; ?>" value="<?= str_replace('.', ',', $qtydatang); ?>">
                                            </td>
                                            <td>
                                                <input class="form-control form-control-sm" type="text" name="hargabrg<?= $in; ?>" id="hargabrg<?= $in; ?>" value="<?= str_replace('.', ',', $harga); ?>">
                                            </td>
                                            <td><?= number_format($subtotal, 2, ',', '.'); ?></td>
                                            <td>
                                                <select class="form-control form-control-sm" name="suplier<?= $in; ?>" id="suplier<?= $in; ?>">
                                                    <option value="">PILIH</option>
                                                    <?php foreach ($suppliers as $sup) { ?>
                                                        <option value="<?= $sup['suplier_id']; ?>" <?= $sup['suplier_id'] == $row['suplier_id'] ? 'selected' : ''; ?>><?= strtoupper($sup['suplier_nama']); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="form-control form-control-sm" type="number" name="tempo<?= $in; ?>" id="tempo<?= $in; ?>" value="<?= $row['pi_jatuhtempo'] ? hitungHari(date('Y-m-d', strtotime($pembelian['pb_tanggal'])), date('Y-m-d', strtotime($row['pi_jatuhtempo']))) : 0; ?>">
                                            </td>
                                            <td>
                                                <a href="/dashboard/deleteItemPembelian/<?= $row['pi_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></a>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="12" class="font-italic text-center">Data belum tersedia.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot style="background-color: #eaecf4; border-color: #eaecf4;" class="font-weight-bold">
                                <tr>
                                    <td colspan="6" align="right">TOTAL</td>
                                    <td>
                                        <?= number_format($grandTotal, 2, ',', '.'); ?>
                                        <input type="hidden" name="grandTotal2" id="grandTotal2" value="<?= $grandTotal; ?>">
                                    </td>
                                    <td colspan="5"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <input type="hidden" name="jenisTrxId" id="jenisTrxId" value="<?= $pembelian['pb_jenis']; ?>">
                    <input type="hidden" name="pbId" id="pbId" value="<?= $pembelian['pb_id']; ?>">
                    <div class="form-group">
                        <label for="status" class="small font-weight-bold">STATUS</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" <?= $pembelian['pb_status'] == 1 ? 'selected' : ''; ?>>PROSES</option>
                            <option value="2" <?= $pembelian['pb_status'] == 2 ? 'selected' : ''; ?>>SELESAI</option>
                        </select>
                    </div>
                    <div class="form-group text-center mb-0 mt-4">
                        <button type="submit" class="btn btn-primary">SIMPAN</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="qtyHargaModal" tabindex="-1" role="dialog" aria-labelledby="qtyHargaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-uppercase" id="qtyHargaModalLabel">PERBARUI</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="itemId" id="itemId">
                <div class="form-group">
                    <label for="newQty">QUANTITY</label>
                    <input required type="text" name="newQty" id="newQty" class="form-control">
                </div>
                <div class="form-group">
                    <label for="newHarga">HARGA</label>
                    <input type="text" name="newHarga" id="newHarga" class="form-control" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary updateQtyHarga">Simpan</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>