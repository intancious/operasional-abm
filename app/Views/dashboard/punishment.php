<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$userModel = new \App\Models\UserModel();
$bagianModel = new \App\Models\BagianModel();
$sesiBagian = session()->get('usr_bagian');
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$request = \Config\Services::request();
$totalItems = ($totalRows / 25);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="#" class="addPunishment" data-toggle="modal" data-target="#punishmentModal"><i class="fas fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/punishment"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                        <th>PUNISHMENT</th>
                        <th>USER</th>
                        <th>WAKTU (MENIT)</th>
                        <th>POTONGAN (RP)</th>
                        <th>KETERANGAN</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($punishment) {
                        $i = 1;
                        foreach ($punishment as $row) { ?>
                            <tr class="text-uppercase">
                                <td> <?= $i++; ?> </td>
                                <td> <a href="#" class="editPunishment" data-toggle="modal" data-target="#punishmentModal" data-id="<?= $row['pun_id']; ?>"><?= $row['pun_nama']; ?></a> </td>
                                <td> <?= $row['pun_user'] ? $row['usr_nama'] : '-'; ?> </td>
                                <td> <?= $row['pun_waktu']; ?> </td>
                                <td> <?= number_format($row['pun_potongan'], 0, ".", "."); ?> </td>
                                <td> <?= $row['pun_deskripsi'] ? $row['pun_deskripsi'] : '-'; ?> </td>
                                <td>
                                    <form class="d-inline" action="/dashboard/deletePunishment" method="post">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $row['pun_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?');"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
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
<div class="modal fade" id="punishmentModal" tabindex="-1" role="dialog" aria-labelledby="punishmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="punishmentModalLabel"></h5>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label for="punishment">Punishment</label>
                        <input required type="text" name="punishment" id="punishment" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="user" class="small">USER</label>
                        <select name="user" id="user" class="form-control selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php foreach ($userModel->orderBy('usr_nama', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['usr_id']; ?>" data-tokens="<?= $row['usr_nama']; ?>" <?= $request->getVar('user') == $row['usr_id'] ? 'selected' : ''; ?>><?= strtoupper($row['usr_nama']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="waktu">Waktu (Menit)</label>
                        <input required type="number" name="waktu" id="waktu" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="potongan">Potongan (Rp)</label>
                        <input required type="text" name="potongan" id="potongan" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3"></textarea>
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