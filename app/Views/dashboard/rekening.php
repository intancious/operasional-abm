<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$request = \Config\Services::request();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/addRekening"><i class="fas fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/rekening"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                        <th>KODE</th>
                        <th>REKENING</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    if ($rekening) {
                        foreach ($rekening as $row) :
                            // if ($row['reksub5_id'] != '35' && $row['reksub5_id'] != '36' && $row['reksub4_id'] != '92' && $row['reksub3_id'] != '657') {
                    ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td class="text-uppercase">
                                        <a href="/dashboard/editRekening/<?= $row['rek_id']; ?>">
                                            <?= $row['rek_kode'] ? $row['rek_kode'] : '-'; ?>
                                        </a>
                                    </td>
                                    <td class="text-uppercase"><?= $row['rek_nama'] ? $row['rek_nama'] : '-'; ?></td>
                                    <td>
                                        <form class="d-inline" action="/dashboard/deleteRekening" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="id" value="<?= $row['rek_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                        <?php //}
                        endforeach;
                    } else { ?>
                        <tr>
                            <td colspan="4" class="text-center font-italic">Data belum tersedia.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>