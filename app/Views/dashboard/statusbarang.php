<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="#" class="addSabar" data-toggle="modal" data-target="#sabarModal"><i class="fas fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/statusbarang"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                        <th>STATUS</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    if ($sabar) {
                        foreach ($sabar as $row) : ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td class="text-uppercase">
                                    <?php if (in_array("editStatusbarang", $akses)) { ?>
                                        <a href="#" data-id="<?= $row['sb_id']; ?>" class="editSabar" data-toggle="modal" data-target="#sabarModal">
                                            <?= $row['sb_nama']; ?>
                                        </a>
                                    <?php } else {
                                        echo $row['sb_nama'];
                                    } ?>
                                </td>
                                <td>
                                    <form class="d-inline ml-1" action="/dashboard/deleteStatusbarang" method="post">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $row['sb_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach;
                    } else { ?>
                        <tr>
                            <td colspan="3" class="text-center font-italic">Data belum tersedia.</td>
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
<div class="modal fade" id="sabarModal" tabindex="-1" role="dialog" aria-labelledby="sabarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-uppercase" id="sabarModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label for="nama">Status Barang</label>
                        <input required type="text" name="nama" id="nama" class="form-control">
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