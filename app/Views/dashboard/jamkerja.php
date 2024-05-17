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
        <a href="#" class="addTimeWork" data-toggle="modal" data-target="#timeworkModal"><i class="fas fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/jamkerja"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                        <th>JAM KERJA</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jamkerja) {
                        $i = 1;
                        foreach ($jamkerja as $row) { ?>
                            <tr>
                                <td> <?= $i++; ?> </td>
                                <td> <a href="#" class="editTimeWork" data-toggle="modal" data-target="#timeworkModal" data-id="<?= $row['jk_id']; ?>"><?= $row['jk_mulai']; ?> s/d <?= $row['jk_selesai']; ?></a> </td>
                                <td>
                                    <?php if ($row['jk_id'] >= 4 && $row['jk_id'] <= 6) { ?>
                                        <button type="button" class="btn btn-secondary btn-sm"> &nbsp;<i class="fa fa-trash"></i></button>
                                    <?php } else { ?>
                                        <form class="d-inline" action="/dashboard/deleteTime" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="id" value="<?= $row['jk_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?');"> &nbsp;<i class="fa fa-trash"></i></button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="3" align="center" class="font-italic">Data belum tersedia.</td>
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

<!-- Modal -->
<div class="modal fade" id="timeworkModal" tabindex="-1" role="dialog" aria-labelledby="timeworkModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="timeworkModalLabel"></h5>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label for="mulai">Mulai Kerja</label>
                        <input required type="time" name="mulai" id="mulai" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="selesai">Selesai Kerja</label>
                        <input required type="time" name="selesai" id="selesai" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>