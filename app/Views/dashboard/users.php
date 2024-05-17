<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$jamKerjaModel = new \App\Models\JamkerjaModel();
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$request = \Config\Services::request();
$totalItems = ($totalRows / 100);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/addUser"><i class="fas fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/users"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                        <th>NAMA</th>
                        <th>USERNAME</th>
                        <th>NO. HP</th>
                        <th>JAM KERJA</th>
                        <th>STATUS</th>
                        <th>BAGIAN</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    if ($users) {
                        foreach ($users as $row) :
                            $bagianuser = $bagianModel->find($row['usr_bagian']);
                            $jamkerja = $jamKerjaModel->find($row['usr_jamkerja']);
                            $jamkerja2 = $jamKerjaModel->find($row['usr_jamkerja2']);
                    ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td class="text-uppercase">
                                    <a href="/dashboard/editUser/<?= $row['usr_id']; ?>">
                                        <?= $row['usr_nama'] ? $row['usr_nama'] : '-'; ?>
                                    </a>
                                </td>
                                <td><?= $row['usr_username'] ? $row['usr_username'] : '-'; ?></td>
                                <td><?= $row['usr_nohp'] ? $row['usr_nohp'] : '-'; ?></td>
                                <td class="text-uppercase">
                                    <?= $row['usr_jamkerja'] ? $jamkerja['jk_mulai'] . ' - ' . $jamkerja['jk_selesai'] : '-'; ?>
                                    <?= $row['usr_jamkerja2'] ? '<br /><br />Sabtu<br />' . $jamkerja2['jk_mulai'] . ' - ' . $jamkerja2['jk_selesai'] : '-'; ?>
                                </td>
                                <td class="text-uppercase"><?= $row['usr_aktif'] == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>'; ?></td>
                                <td class="text-uppercase"><?= $bagianuser ? $bagianuser['bagian_nama'] : '-'; ?></td>
                                <td>
                                    <?php if ($row['usr_id'] == 1) { ?>
                                        <button type="button" disabled class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    <?php } else { ?>
                                        <form class="d-inline" action="/dashboard/deleteUser" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="id" value="<?= $row['usr_id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin melanjutkan?');"><i class="fas fa-trash"></i></button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php endforeach;
                    } else { ?>
                        <tr>
                            <td colspan="8" class="text-center font-italic">Data belum tersedia.</td>
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
<?= $this->endSection(); ?>