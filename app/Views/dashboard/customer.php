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
        <a href="/dashboard/addCustomer"><i class="fas fa-plus-circle ml-2"></i></a>
        <a href="/dashboard/customer"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
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
                        <th>NAMA</th>
                        <th>ALAMAT</th>
                        <th>TELP</th>
                        <th>HAPUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    if ($customers) {
                        foreach ($customers as $row) : ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td class="text-uppercase">
                                    <?php if (in_array("editCustomer", $akses)) { ?>
                                        <a href="/dashboard/editCustomer/<?= $row['cust_id']; ?>">
                                            <?= $row['cust_kode'] ? $row['rek_kode'] . '.' . $row['cust_kode'] : '-'; ?>
                                        </a>
                                    <?php } else {
                                        echo $row['cust_kode'] ? $row['rek_kode'] . '.' . $row['cust_kode'] : '-';
                                    } ?>
                                </td>
                                <td class="text-uppercase"><?= $row['cust_nama'] ? $row['cust_nama'] : '-'; ?></td>
                                <td class="text-uppercase"><?= $row['cust_alamat'] ? $row['cust_alamat'] : '-'; ?></td>
                                <td><?= $row['cust_telp'] ? $row['cust_telp'] : '-'; ?></td>
                                <td>
                                    <?php if (in_array("deleteCustomer", $akses)) { ?>
                                        <form class="d-inline" action="/dashboard/deleteCustomer" method="post">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="id" value="<?= $row['cust_id']; ?>">
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