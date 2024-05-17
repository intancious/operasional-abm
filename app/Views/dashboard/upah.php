<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$request = \Config\Services::request();
$rekeningModel = new \App\Models\KoderekeningModel();
$barangModel = new \App\Models\BarangModel();
// $totalItems = ($totalRows / 25);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/addUpah"><i class="fas fa-plus-circle ml-2"></i>
            <a href="/dashboard/upah"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?= session()->get('pesan'); ?>
        <div class="table-responsive">
            <table id="customTable" class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                <thead class="thead-light font-weight-bold">
                    <tr>
                        <th>#</th>
                        <th>NO. AKUN</th>
                        <th>NAMA</th>
                        <th>SATUAN</th>
                        <th>KATEGORI</th>
                        <th>NILAI</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    $grandTotal = 0;
                    if ($upah) {
                        foreach ($upah as $row) :
                            $rekening = $rekeningModel->find($row['up_rekening']);
                            $nilai = ($row['up_nilai'] ? $row['up_nilai'] : 0);
                            $grandTotal += $nilai;
                    ?>
                            <tr class="text-uppercase">
                                <td><?= $i++; ?></td>
                                <td>
                                    <a href="/dashboard/editUpah/<?= $row['up_id']; ?>">
                                        <?= $rekening['rek_kode'] . '.' . $row['up_kode']; ?>
                                    </a>
                                </td>
                                <td><?= $row['up_nama'] ? $row['up_nama'] : '-'; ?></td>
                                <td><?= $row['up_satuan'] ? $row['satuan_nama'] : ''; ?></td>
                                <td><?= $row['up_kategori'] ? $row['kabar_nama'] : ''; ?></td>
                                <td align="right"><?= number_format($nilai, 2, ',', '.'); ?></td>
                                <td>
                                    <form class="d-inline" action="/dashboard/deleteUpah" method="post">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $row['up_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach;
                    } else { ?>
                        <tr>
                            <td colspan="7" class="text-center font-italic">Data belum tersedia.</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                        <td colspan="5" class="text-center font-weight-bold">GRAND TOTAL</td>
                        <td align="right" class="font-weight-bold"><?= number_format($grandTotal, 2, ',', '.'); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>