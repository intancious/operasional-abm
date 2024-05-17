<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$request = \Config\Services::request();
$totalItems = ($totalRows / 25);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="#" class="addSatuan" data-toggle="modal" data-target="#satuanModal"><i class="fas fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/satuan"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?= session()->get('pesan'); ?>
        <div class="row justify-content-start my-3">
            <div class="col-md-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="page_view">Halaman</label>
                    </div>
                    <select class="custom-select pagin" name="page_view" id="page_view">
                        <option value="<?= current_url(); ?>">--</option>
                        <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                            <option value="<?= current_url(); ?>?page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="customTable" class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                <thead class="thead-light font-weight-bold">
                    <tr>
                        <th>#</th>
                        <th>SATUAN</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    if ($satuan) {
                        foreach ($satuan as $row) : ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td class="text-uppercase">
                                    <?php if (in_array("editSatuan", $akses)) { ?>
                                        <a href="#" data-id="<?= $row['satuan_id']; ?>" class="editSatuan" data-toggle="modal" data-target="#satuanModal">
                                            <?= $row['satuan_nama']; ?>
                                        </a>
                                    <?php } else {
                                        echo $row['satuan_nama'];
                                    } ?>
                                </td>
                                <td>
                                    <?php if (in_array("deleteSatuan", $akses)) { ?>
                                        <form class="d-inline" action="/dashboard/deleteSatuan" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="id" value="<?= $row['satuan_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                        </form>
                                    <?php } else { ?>
                                        <button type="button" disabled class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    <?php } ?>
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
        <div class="row justify-content-end my-3">
            <div class="col-md-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="page_view">Halaman</label>
                    </div>
                    <select class="custom-select pagin" name="page_view" id="page_view">
                        <option value="<?= current_url(); ?>">--</option>
                        <?php for ($i = 1; $i <= ceil($totalItems); $i++) { ?>
                            <option value="<?= current_url(); ?>?page_view=<?= $i; ?>" <?= $request->getVar('page_view') == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="satuanModal" tabindex="-1" role="dialog" aria-labelledby="satuanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-uppercase" id="satuanModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label for="nama">Nama Satuan</label>
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