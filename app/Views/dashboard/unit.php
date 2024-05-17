<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$request = \Config\Services::request();
$rekeningModel = new \App\Models\KoderekeningModel();
$request = \Config\Services::request();
$totalItems = ($totalRows / 100);
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/addUnit"><i class="fas fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/unit"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                        <th>NO. AKUN</th>
                        <th>TIPE</th>
                        <th>NAMA</th>
                        <th>KETERANGAN</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    if ($unit) {
                        foreach ($unit as $row) :
                            $exRekening = explode('.', $rekeningModel->find($row['unit_rekening'])['rek_kode']);
                    ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td>
                                    <?php if (in_array("editUnit", $akses)) { ?>
                                        <a href="/dashboard/editUnit/<?= $row['unit_id']; ?>">
                                            <?= $exRekening[0] . '.' . ((isset($exRekening[1]) ? $exRekening[1] : 0) . '.' . $row['unit_kode']); ?>
                                        </a>
                                    <?php } else { ?>
                                        <?= $exRekening[0] . '.' . ((isset($exRekening[1]) ? $exRekening[1] : 0)  . '.' . $row['unit_kode']); ?>
                                    <?php } ?>
                                </td>
                                <td><?= $row['unit_tipe'] ? $row['type_nama'] : '-'; ?></td>
                                <td class="text-uppercase"><?= $row['unit_nama'] ? $row['unit_nama'] : '-'; ?></td>
                                <td><?= $row['unit_keterangan'] ? $row['unit_keterangan'] : '-'; ?></td>
                                <td>
                                    <?php if (in_array("deleteUnit", $akses)) { ?>
                                        <form class="d-inline" action="/dashboard/deleteUnit" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="id" value="<?= $row['unit_id']; ?>">
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
                            <td colspan="6" class="text-center font-italic">Data belum tersedia.</td>
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