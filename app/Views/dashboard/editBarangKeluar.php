<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$request = \Config\Services::request();
$barangModel = new \App\Models\BarangModel();
$kategoriModel = new \App\Models\KabarModel();
$satuanModel = new \App\Models\SatuanModel();
$unitModel = new \App\Models\UnitModel();
$barangKeluarModel = new \App\Models\BarangKeluarModel();
$barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
$rekeningModel = new \App\Models\KoderekeningModel();

$categories = $kategoriModel->orderBy('kabar_nama', 'ASC')->findAll();
$satuan = $satuanModel->orderBy('satuan_nama', 'ASC')->findAll();
$units = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
    ->orderBy('unit.unit_nama', 'ASC')
    ->select('unit.*, types.type_nama')
    ->findAll();
$barang = $barangModel->orderBy('barang_nama', 'ASC')->findAll();

$keluarItems = $barangKeluarItemModel->where('barangkeluar_item.bki_barangkeluar', $barangkeluar['bk_id'])
    ->join('barang', 'barang.barang_id = barangkeluar_item.bki_barang', 'left')
    ->select('barangkeluar_item.*, barang.barang_nama')
    ->orderBy('barangkeluar_item.bki_id', 'DESC')->findAll();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="/dashboard/barangkeluar"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
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
                <input type="hidden" name="id" id="id" value="<?= $barangkeluar['bk_id']; ?>">
                <table class="table table-bordered small">
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">TGL. TRANSAKSI</td>
                        <td>
                            <input type="text" name="tanggal" id="tanggalTrx" value="<?= date('d-m-Y', strtotime($barangkeluar['bk_tanggal'])); ?>" class="form-control form-control-sm">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">NO. FAKTUR</td>
                        <td>
                            <input readonly type="text" name="faktur" id="faktur" value="<?= $barangkeluar['bk_nomor']; ?>" class="form-control form-control-sm">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">UNIT</td>
                        <td>
                            <select required name="unit" id="unit" class="form-control form-control-sm selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php foreach ($units as $row) { ?>
                                    <option value="<?= $row['unit_id']; ?>" data-tokens="<?= $row['unit_nama']; ?> (<?= $row['type_nama']; ?>)" <?= $barangkeluar['bk_unit'] == $row['unit_id'] ? 'selected' : ''; ?>><?= $row['unit_nama']; ?> (<?= $row['type_nama']; ?>)</option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">CATATAN</td>
                        <td>
                            <textarea name="catatan" id="catatan" rows="2" class="form-control form-control-sm"><?= $barangkeluar['bk_keterangan']; ?></textarea>
                        </td>
                    </tr>
                </table>
                <table class="table table-bordered small mt-4">
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">DEBET</td>
                        <td style="vertical-align: middle; font-weight: bold;">KREDIT</td>
                    </tr>
                    <tr>
                        <td>
                            <select name="debet" id="debet" class="form-control form-control-sm <?= $validation->hasError('debet') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php
                                foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                    <option value="<?= $row['rek_id']; ?>" <?= $barangkeluar['bk_debet'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <select name="kredit" id="kredit" class="form-control form-control-sm <?= $validation->hasError('kredit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                                <option value="" data-tokens="">:: PILIH ::</option>
                                <?php
                                foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                    <option value="<?= $row['rek_id']; ?>" <?= $barangkeluar['bk_kredit'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
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
                        <td style="vertical-align: middle; font-weight: bold;">STOK</td>
                        <td>
                            <div id="stokSaatIni" class="text-uppercase">0</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: middle; font-weight: bold;">VOLUME RAP</td>
                        <td>
                            <div id="volumeRap" class="text-uppercase">0</div>
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
                </table>
                <div class="form-group mt-4 mb-0">
                    <button type="button" class="btn btn-primary btn-sm addCartKeluar"><i class="fas fa-sm fa-plus-circle mr-1"></i> TAMBAH</button>
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
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody id="cartListKeluar">
                        </tbody>
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
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped small" id="customTable" style="width:100%">
                        <thead class="thead-light font-weight-bold">
                            <tr>
                                <th>#</th>
                                <th>NAMA BARANG</th>
                                <th>QTY</th>
                                <th>HARGA</th>
                                <th>SUBTOTAL</th>
                                <th>HAPUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grandTotal = 0;
                            if ($keluarItems) {
                                $i = 1;
                                foreach ($keluarItems as $row) {
                                    $qty = $row['bki_qty'] ? str_replace(',', '.', $row['bki_qty']) : 0;
                                    // $harga = $barangModel->rataRataHarga($row['bki_barang']);
                                    $harga = $row['bki_harga'] ? str_replace(',', '.', $row['bki_harga']) : 0;
                                    $subtotal = $qty * $harga;
                                    $grandTotal += $subtotal;
                            ?>
                                    <tr class="text-uppercase">
                                        <td><?= $i++; ?></td>
                                        <td>
                                            <a href="#" class="editItemKeluar" data-bkid="<?= $row['bki_id']; ?>" data-toggle="modal" data-target="#itemKeluarModal" title="Edit">
                                                <?= $row['barang_nama']; ?>
                                            </a>
                                        </td>
                                        <td><?= number_format($qty, 2, ',', '.'); ?></td>
                                        <td><?= number_format($harga, 2, ',', '.'); ?></td>
                                        <td><?= number_format($subtotal, 2, ',', '.'); ?></td>
                                        <td>
                                            <form class="d-inline ml-1" action="/dashboard/deleteItemBarangKeluar" method="post">
                                                <?= csrf_field(); ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="id" value="<?= $row['bki_id']; ?>">
                                                <input type="hidden" name="bkId" value="<?= $barangkeluar['bk_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="6" class="font-italic text-center">Data belum tersedia.</td>
                                </tr>
                            <?php } ?>
                        <tfoot style="background-color: #eaecf4; border-color: #eaecf4;" class="font-weight-bold">
                            <tr>
                                <td colspan="4" align="right">GRAND TOTAL</td>
                                <td><?= number_format($grandTotal, 2, ',', '.'); ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="form-group text-right mt-3 mb-5">
            <button type="button" class="btn btn-primary updateKeluarStatusRek mr-3">SIMPAN</button>
        </div>
    </div>
</div>


<div class="modal fade" id="qtyKeluarModal" tabindex="-1" role="dialog" aria-labelledby="qtyKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-uppercase" id="qtyKeluarModalLabel">PERBARUI</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="outId" id="outId">
                <div class="form-group">
                    <label for="outQty">Quantity</label>
                    <input required type="text" name="outQty" id="outQty" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary updateQtyKeluar">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="itemKeluarModal" tabindex="-1" role="dialog" aria-labelledby="itemKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-uppercase" id="itemKeluarModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" name="bid" id="bid">
                    <input type="hidden" name="bkId" id="bkId" value="<?= $barangkeluar['bk_id']; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="namaBrg">BARANG</label>
                                <input required type="text" name="namaBrg" id="namaBrg" class="form-control text-uppercase" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qtyOut">QTY</label>
                                <input required type="text" name="qtyOut" id="qtyOut" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hargaOut">Harga</label>
                        <input required type="text" name="hargaOut" id="hargaOut" class="form-control">
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