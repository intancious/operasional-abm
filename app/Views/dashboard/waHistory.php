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
        <a href="/dashboard/waHistory"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?= session()->get('pesan'); ?>
        <a href="/dashboard/resendall" class="btn btn-sm btn-primary mb-3 float-right">RESEND ALL</a>
        <div class="table-responsive">
            <table id="customTable" class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                <thead class="thead-light font-weight-bold">
                    <tr>
                        <th>#</th>
                        <th>TANGGAL</th>
                        <th>STATUS</th>
                        <th>RESEND</th>
                        <th>PESAN</th>
                        <th>TUJUAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results) {
                        $i = 1;
                        foreach ($results as $row) {
                    ?>
                            <tr>
                                <td> <?= $i++; ?> </td>
                                <td> <?= date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td> <?= $row['wa_status'] == 1 ? '<span class="badge badge-success text-white">Sukses</span>' : '<span class="badge badge-danger text-white">Gagal</span>'; ?> </td>
                                <td>
                                    <?php if ($row['wa_status'] != 1) { ?>
                                        <a class="btn btn-primary btn-sm" href="/dashboard/resendwa/<?= $row['wa_id']; ?>">Resend</a>
                                    <?php } else {
                                        echo "-";
                                    } ?>
                                </td>
                                <td> <?= $row['wa_message']; ?> </td>
                                <td> <?= $row['wa_number']; ?></td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="6" align="center" class="font-italic">Data belum tersedia.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="float-right mt-3">
            <?= $pager->links('view', 'pagination'); ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>