<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$request = \Config\Services::request();
$rekeningModel = new \App\Models\KoderekeningModel();
$satuanModel = new \App\Models\SatuanModel();
$kabarModel = new \App\Models\KabarModel();
$barangModel = new \App\Models\BarangModel();
$upahModel = new \App\Models\UpahModel();
// $totalItems = ($totalRows / 25);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <?php if ($request->getVar('tipe')) { ?>
            <a href="/dashboard/addRap?tipe=<?= $request->getVar('tipe'); ?>"><i class="fas fa-plus-circle ml-2"></i></a>
            <a href="/dashboard/rap?tipe=<?= $request->getVar('tipe'); ?>"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
        <?php } ?>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow mb-4 bg-primary text-white">
            <div class="card-body">
                <form action="" method="get">
                    <div class="form-group">
                        <label for="tipe" class="ml-1 small">TIPE</label>
                        <select required name="tipe" id="tipe" class="form-control form-control-sm <?= $validation->hasError('tipe') ? 'is-invalid' : ''; ?>">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            $typesModel = new \App\Models\TypesModel();
                            $types = $typesModel->orderBy('type_nama', 'ASC')->findAll();
                            foreach ($types as $row) { ?>
                                <option value="<?= $row['type_id']; ?>" data-tokens="<?= $row['type_nama']; ?>" <?= $request->getVar('tipe') == $row['type_id'] ? 'selected' : ''; ?>><?= strtoupper($row['type_nama']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php if ($request->getVar('tipe')) { ?>
                        <div class="form-group">
                            <label for="src" class="ml-1 small">CARI BARANG / PEKERJAAN</label>
                            <input type="text" name="src" id="src" class="form-control form-control-sm" placeholder="Cari Barang / Pekerjaan" value="<?= $request->getVar('src'); ?>">
                        </div>
                    <?php } ?>
                    <div class="row justify-content-center mt-4">
                        <div class="col-md">
                            <button class="btn btn-light btn-sm" type="submit" id="filter">FILTER</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($request->getVar('tipe')) { ?>
    <div class="card shadow mb-4">
        <div class="card-body">
            <?= session()->get('pesan'); ?>
            <div class="table-responsive">
                <table id="customTable" class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                    <thead class="thead-light font-weight-bold">
                        <tr>
                            <th class="text-center">NO.</th>
                            <th class="text-center">TIPE</th>
                            <th class="text-center">BARANG / PEKERJAAN</th>
                            <th class="text-center">SATUAN</th>
                            <th class="text-center">VOLUME</th>
                            <th class="text-center">HARGA</th>
                            <th class="text-center">SUBTOTAL</th>
                            <th class="text-center">KETERANGAN</th>
                            <th class="text-center">HAPUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grandTotal = 0;
                        if ($raps) {
                            foreach ($kabar as $kb) { ?>
                                <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                    <td colspan="9"><?= strtoupper($kb['kabar_nama']); ?></td>
                                </tr>
                                <?php
                                $i = 1;
                                foreach ($raps as $row) :
                                    if ($row['rap_barang']) {
                                        $brgupah = $barangModel->find($row['rap_barang']);
                                        $satuan = $brgupah ? $satuanModel->find($brgupah['barang_satuan']) : NULL;
                                        $kategori = $brgupah ? $kabarModel->find($brgupah['barang_kategori']) : NULL;
                                    } else
                                    if ($row['rap_upah']) {
                                        $brgupah = $upahModel->find($row['rap_upah']);
                                        $satuan = $satuanModel->find($brgupah['up_satuan']);
                                        $kategori = $kabarModel->find($brgupah['up_kategori']);
                                    }

                                    if (isset($brgupah) && isset($satuan) && isset($kategori)) {
                                        if ($kategori['kabar_id'] == $kb['kabar_id']) {
                                            $subtotal = ($row['rap_volume'] ? floatval($row['rap_volume']) : 0) * ($row['rap_harga'] ? floatval($row['rap_harga']) : 0);
                                            $grandTotal += $subtotal;
                                ?>
                                            <tr>
                                                <td class="text-center"><?= $i++; ?></td>
                                                <td class="text-center"><?= $row['type_nama'] ? $row['type_nama'] : '-'; ?></td>
                                                <td>
                                                    <?php if (in_array("editRap", $akses)) { ?>
                                                        <a href="/dashboard/editRap/<?= $row['rap_id']; ?>">
                                                            <?= $row['rap_barang'] ? strtoupper($brgupah['barang_nama']) : ($row['rap_upah'] ? strtoupper($brgupah['up_nama']) : '-'); ?>
                                                        </a>
                                                    <?php } else { ?>
                                                        <?= $row['rap_barang'] ? strtoupper($brgupah['barang_nama']) : ($row['rap_upah'] ? strtoupper($brgupah['up_nama']) : '-'); ?>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center"><?= isset($satuan) ? strtoupper($satuan['satuan_nama']) : '-'; ?></td>
                                                <td class="text-center"><?= $row['rap_volume'] ? $row['rap_volume'] : '0'; ?></td>
                                                <td align="right"><?= $row['rap_harga'] ? number_format(str_replace(',', '.', $row['rap_harga']), 0, ".", ".") : '0'; ?></td>
                                                <td align="right"><?= number_format($subtotal, 0, ".", "."); ?></td>
                                                <td><?= $row['rap_keterangan'] ? $row['rap_keterangan'] : '-'; ?></td>
                                                <td class="text-center">
                                                    <?php if (in_array("deleteRap", $akses)) { ?>
                                                        <form class="d-inline" action="/dashboard/deleteRap" method="post">
                                                            <?= csrf_field(); ?>
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <input type="hidden" name="id" value="<?= $row['rap_id']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                                        </form>
                                                    <?php } else { ?>
                                                        <button type="button" disabled class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                            <?php }
                                    }
                                endforeach;
                            }
                        } else { ?>
                            <tr>
                                <td colspan="9" class="text-center font-italic">Data belum tersedia.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                            <td colspan="6" class="text-center font-weight-bold">GRAND TOTAL</td>
                            <td class="font-weight-bold" align="right"><?= number_format($grandTotal, 0, ".", "."); ?></td>
                            <td colspan="2" class="text-center">&nbsp;</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<?php } ?>
<?= $this->endSection(); ?>