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
    $start = date_create($start);
    $end = date_create($end);
    $diff = date_diff($start, $end);
    return $diff->format("%d%");
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
                            <td style="vertical-align: middle; font-weight: bold;">CATATAN</td>
                            <td>
                                <textarea name="catatan" id="catatan" rows="2" class="form-control form-control-sm"><?= $pembelian['pb_keterangan']; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: middle; font-weight: bold;" align="right" colspan="2">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-sm fa-save fa-sm mr-1"></i> SIMPAN</button>
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
                </table>
                <div class="form-group mt-3 mb-0">
                    <button type="button" class="btn btn-primary btn-sm addCartPembelian float-right"><i class="fas fa-sm fa-plus-circle mr-1"></i> TAMBAH</button>
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
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody id="cartListPembelian">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">
                                    <button class="btn btn-secondary btn-sm clearItem" type="button">
                                        <i class="fas fa-sm fa-broom fa-sm mr-1"></i> BERSIHKAN
                                    </button>
                                </td>
                                <td colspan="2">
                                    <button class="btn btn-primary btn-sm addNewItemPembelian float-right" type="button">
                                        <i class="fas fa-sm fa-save fa-sm mr-1"></i> SIMPAN
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
<form action="/dashboard/updateDataItemPembelian" method="post">
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
                                    <th>QTY</th>
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
                                ?>
                                        <tr class="text-uppercase">
                                            <input type="hidden" name="piId<?= $in; ?>" id="piId<?= $in; ?>" value="<?= $row['pi_id']; ?>">
                                            <td><?= $i++; ?></td>
                                            <td><?= $row['barang_nama']; ?></td>
                                            <td>
                                                <input class="form-control form-control-sm" type="text" name="qtybeli<?= $in; ?>" id="qtybeli<?= $in; ?>" value="<?= $qtybeli; ?>">
                                            </td>
                                            <td>
                                                <a href="/dashboard/deleteItemPembelian/<?= $row['pi_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></a>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="4" class="font-italic text-center">Data belum tersedia.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
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
                    <input type="hidden" name="pbId" id="pbId" value="<?= $pembelian['pb_id']; ?>">
                    <div class="form-group">
                        <label for="status" class="small font-weight-bold">STATUS</label>
                        <select name="status" id="status" class="form-control">
                            <?php if ($pembelian['pb_status'] == 1) { ?>
                                <option value="1" <?= $pembelian['pb_status'] == 1 ? 'selected' : ''; ?>>PROSES</option>
                            <?php } ?>
                            <?php if ($pembelian['pb_status'] == 2) { ?>
                                <option value="2" <?= $pembelian['pb_status'] == 2 ? 'selected' : ''; ?>>SELESAI</option>
                            <?php } ?>
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
<?= $this->endSection(); ?>