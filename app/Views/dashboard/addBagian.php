<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/bagian"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?= session()->get('pesan'); ?>
        <form action="/dashboard/insertBagian" method="post">
            <?= csrf_field(); ?>
            <div class="form-group">
                <label for="nama">Nama Bagian</label>
                <input required type="text" name="nama" id="nama" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" value="<?= old('nama') ? old('nama') : ''; ?>">
                <div class="invalid-feedback">
                    <?= $validation->getError('nama'); ?>
                </div>
            </div>

            <p class="mt-4 mb-3 font-weight-bold">Hak Akses</p>
            <?php
            $akses = old('bagian_akses');
            if (old('bagian_akses')) {
                foreach ($akses as $row => $value) {
                    $hakAkses[] = $value;
                }
            }
            ?>
            <div class="row" style="max-height: 500px; overflow-y: auto;">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user">Data User</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[1]" id="bagian_akses[1]" value="users" <?= isset($hakAkses) && in_array("users", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[1]">Data User</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[2]" id="bagian_akses[2]" value="addUser" <?= isset($hakAkses) && in_array("addUser", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[2]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[3]" id="bagian_akses[3]" value="insertUser" <?= isset($hakAkses) && in_array("insertUser", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[3]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[4]" id="bagian_akses[4]" value="editUser" <?= isset($hakAkses) && in_array("editUser", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[4]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[5]" id="bagian_akses[5]" value="updateUser" <?= isset($hakAkses) && in_array("updateUser", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[5]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[6]" id="bagian_akses[6]" value="deleteUser" <?= isset($hakAkses) && in_array("deleteUser", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[6]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="bagian">Data Bagian</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[7]" id="bagian_akses[7]" value="bagian" <?= isset($hakAkses) && in_array("bagian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[7]">Data Bagian</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[8]" id="bagian_akses[8]" value="addBagian" <?= isset($hakAkses) && in_array("addBagian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[8]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[9]" id="bagian_akses[9]" value="insertBagian" <?= isset($hakAkses) && in_array("insertBagian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[9]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[10]" id="bagian_akses[10]" value="editBagian" <?= isset($hakAkses) && in_array("editBagian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[10]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[11]" id="bagian_akses[11]" value="updateBagian" <?= isset($hakAkses) && in_array("updateBagian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[11]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[12]" id="bagian_akses[12]" value="deleteBagian" <?= isset($hakAkses) && in_array("deleteBagian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[12]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="rekening">Data Rekening</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[13]" id="bagian_akses[13]" value="rekening" <?= isset($hakAkses) && in_array("rekening", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[13]">Data Rekening</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[14]" id="bagian_akses[14]" value="addRekening" <?= isset($hakAkses) && in_array("addRekening", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[14]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[15]" id="bagian_akses[15]" value="insertRekening" <?= isset($hakAkses) && in_array("insertRekening", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[15]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[16]" id="bagian_akses[16]" value="editRekening" <?= isset($hakAkses) && in_array("editRekening", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[16]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[17]" id="bagian_akses[17]" value="updateRekening" <?= isset($hakAkses) && in_array("updateRekening", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[17]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[18]" id="bagian_akses[18]" value="deleteRekening" <?= isset($hakAkses) && in_array("deleteRekening", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[18]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="satuan">Data Satuan</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[19]" id="bagian_akses[19]" value="satuan" <?= isset($hakAkses) && in_array("satuan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[19]">Data Satuan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[20]" id="bagian_akses[20]" value="addSatuan" <?= isset($hakAkses) && in_array("addSatuan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[20]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[21]" id="bagian_akses[21]" value="insertSatuan" <?= isset($hakAkses) && in_array("insertSatuan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[21]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[22]" id="bagian_akses[22]" value="editSatuan" <?= isset($hakAkses) && in_array("editSatuan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[22]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[23]" id="bagian_akses[23]" value="updateSatuan" <?= isset($hakAkses) && in_array("updateSatuan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[23]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[24]" id="bagian_akses[24]" value="deleteSatuan" <?= isset($hakAkses) && in_array("deleteSatuan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[24]">Delete</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="suplier">Data Kategori Barang</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[25]" id="bagian_akses[25]" value="kategoriBarang" <?= isset($hakAkses) && in_array("kategoriBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[25]">Data Kategori Barang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[26]" id="bagian_akses[26]" value="addKategoriBarang" <?= isset($hakAkses) && in_array("addKategoriBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[26]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[27]" id="bagian_akses[27]" value="insertKategoriBarang" <?= isset($hakAkses) && in_array("insertKategoriBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[27]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[28]" id="bagian_akses[28]" value="editKategoriBarang" <?= isset($hakAkses) && in_array("editKategoriBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[28]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[29]" id="bagian_akses[29]" value="updateKategoriBarang" <?= isset($hakAkses) && in_array("updateKategoriBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[29]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[30]" id="bagian_akses[30]" value="deleteKategoriBarang" <?= isset($hakAkses) && in_array("deleteKategoriBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[30]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="barang">Data Barang</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[31]" id="bagian_akses[31]" value="barang" <?= isset($hakAkses) && in_array("barang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[31]">Data Barang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[32]" id="bagian_akses[32]" value="addBarang" <?= isset($hakAkses) && in_array("addBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[32]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[33]" id="bagian_akses[33]" value="insertBarang" <?= isset($hakAkses) && in_array("insertBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[33]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[34]" id="bagian_akses[34]" value="editBarang" <?= isset($hakAkses) && in_array("editBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[34]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[35]" id="bagian_akses[35]" value="updateBarang" <?= isset($hakAkses) && in_array("updateBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[35]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[36]" id="bagian_akses[36]" value="deleteBarang" <?= isset($hakAkses) && in_array("deleteBarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[36]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tipeUnit">Tipe Unit</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[37]" id="bagian_akses[37]" value="tipeUnit" <?= isset($hakAkses) && in_array("tipeUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[37]">Data Tipe Unit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[38]" id="bagian_akses[38]" value="addTipeUnit" <?= isset($hakAkses) && in_array("addTipeUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[38]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[39]" id="bagian_akses[39]" value="insertTipeUnit" <?= isset($hakAkses) && in_array("insertTipeUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[39]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[40]" id="bagian_akses[40]" value="editTipeUnit" <?= isset($hakAkses) && in_array("editTipeUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[40]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[41]" id="bagian_akses[41]" value="updateTipeUnit" <?= isset($hakAkses) && in_array("updateTipeUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[41]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[42]" id="bagian_akses[42]" value="deleteTipeUnit" <?= isset($hakAkses) && in_array("deleteTipeUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[42]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="unit">Data Unit</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[43]" id="bagian_akses[43]" value="unit" <?= isset($hakAkses) && in_array("unit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[43]">Data Unit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[44]" id="bagian_akses[44]" value="addUnit" <?= isset($hakAkses) && in_array("addUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[44]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[45]" id="bagian_akses[45]" value="insertUnit" <?= isset($hakAkses) && in_array("insertUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[45]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[46]" id="bagian_akses[46]" value="editUnit" <?= isset($hakAkses) && in_array("editUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[46]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[47]" id="bagian_akses[47]" value="updateUnit" <?= isset($hakAkses) && in_array("updateUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[47]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[48]" id="bagian_akses[48]" value="deleteUnit" <?= isset($hakAkses) && in_array("deleteUnit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[48]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="suplier">Data Suplier</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[49]" id="bagian_akses[49]" value="suplier" <?= isset($hakAkses) && in_array("suplier", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[49]">Data Suplier</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[50]" id="bagian_akses[50]" value="addSuplier" <?= isset($hakAkses) && in_array("addSuplier", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[50]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[51]" id="bagian_akses[51]" value="insertSuplier" <?= isset($hakAkses) && in_array("insertSuplier", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[51]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[52]" id="bagian_akses[52]" value="editSuplier" <?= isset($hakAkses) && in_array("editSuplier", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[52]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[53]" id="bagian_akses[53]" value="updateSuplier" <?= isset($hakAkses) && in_array("updateSuplier", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[53]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[54]" id="bagian_akses[54]" value="deleteSuplier" <?= isset($hakAkses) && in_array("deleteSuplier", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[54]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="customer">Data Customer</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[55]" id="bagian_akses[55]" value="customer" <?= isset($hakAkses) && in_array("customer", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[55]">Data Customer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[56]" id="bagian_akses[56]" value="addCustomer" <?= isset($hakAkses) && in_array("addCustomer", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[56]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[57]" id="bagian_akses[57]" value="insertCustomer" <?= isset($hakAkses) && in_array("insertCustomer", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[57]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[58]" id="bagian_akses[58]" value="editCustomer" <?= isset($hakAkses) && in_array("editCustomer", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[58]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[59]" id="bagian_akses[59]" value="updateCustomer" <?= isset($hakAkses) && in_array("updateCustomer", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[59]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[60]" id="bagian_akses[60]" value="deleteCustomer" <?= isset($hakAkses) && in_array("deleteCustomer", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[60]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tukang">Data Tukang</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[127]" id="bagian_akses[127]" value="tukang" <?= isset($hakAkses) && in_array("tukang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[127]">Data Tukang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[128]" id="bagian_akses[128]" value="addTukang" <?= isset($hakAkses) && in_array("addTukang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[128]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[129]" id="bagian_akses[129]" value="insertTukang" <?= isset($hakAkses) && in_array("insertTukang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[129]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[130]" id="bagian_akses[130]" value="editTukang" <?= isset($hakAkses) && in_array("editTukang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[130]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[131]" id="bagian_akses[131]" value="updateTukang" <?= isset($hakAkses) && in_array("updateTukang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[131]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[132]" id="bagian_akses[132]" value="deleteTukang" <?= isset($hakAkses) && in_array("deleteTukang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[132]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="marketing">Marketing</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[164]" id="bagian_akses[164]" value="marketing" <?= isset($hakAkses) && in_array("marketing", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[164]">Data Marketing</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[165]" id="bagian_akses[165]" value="addMarketing" <?= isset($hakAkses) && in_array("addMarketing", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[165]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[166]" id="bagian_akses[166]" value="insertMarketing" <?= isset($hakAkses) && in_array("insertMarketing", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[166]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[167]" id="bagian_akses[167]" value="editMarketing" <?= isset($hakAkses) && in_array("editMarketing", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[167]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[168]" id="bagian_akses[168]" value="updateMarketing" <?= isset($hakAkses) && in_array("updateMarketing", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[168]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[169]" id="bagian_akses[169]" value="deleteMarketing" <?= isset($hakAkses) && in_array("deleteMarketing", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[169]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="masterAbsensi">Master Absensi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[176]" id="bagian_akses[176]" value="masterAbsensi" <?= isset($hakAkses) && in_array("masterAbsensi", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[176]">Data</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[177]" id="bagian_akses[177]" value="addMasterAbsensi" <?= isset($hakAkses) && in_array("addMasterAbsensi", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[177]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[178]" id="bagian_akses[178]" value="insertMasterAbsensi" <?= isset($hakAkses) && in_array("insertMasterAbsensi", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[178]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[179]" id="bagian_akses[179]" value="editMasterAbsensi" <?= isset($hakAkses) && in_array("editMasterAbsensi", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[179]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[180]" id="bagian_akses[180]" value="updateMasterAbsensi" <?= isset($hakAkses) && in_array("updateMasterAbsensi", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[180]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[181]" id="bagian_akses[181]" value="deleteMasterAbsensi" <?= isset($hakAkses) && in_array("deleteMasterAbsensi", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[181]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="kehadiran">Kehadiran</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[182]" id="bagian_akses[182]" value="kehadiran" <?= isset($hakAkses) && in_array("kehadiran", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[182]">Data</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[183]" id="bagian_akses[183]" value="addKehadiran" <?= isset($hakAkses) && in_array("addKehadiran", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[183]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[184]" id="bagian_akses[184]" value="insertKehadiran" <?= isset($hakAkses) && in_array("insertKehadiran", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[184]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[185]" id="bagian_akses[185]" value="editKehadiran" <?= isset($hakAkses) && in_array("editKehadiran", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[185]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[186]" id="bagian_akses[186]" value="updateKehadiran" <?= isset($hakAkses) && in_array("updateKehadiran", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[186]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[187]" id="bagian_akses[187]" value="deleteKehadiran" <?= isset($hakAkses) && in_array("deleteKehadiran", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[187]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="biayalain">Biaya Lain</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[170]" id="bagian_akses[170]" value="biayalain" <?= isset($hakAkses) && in_array("biayalain", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[170]">Data Biaya Lain</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[171]" id="bagian_akses[171]" value="addBiayalain" <?= isset($hakAkses) && in_array("addBiayalain", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[171]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[172]" id="bagian_akses[172]" value="insertBiayalain" <?= isset($hakAkses) && in_array("insertBiayalain", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[172]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[173]" id="bagian_akses[173]" value="editBiayalain" <?= isset($hakAkses) && in_array("editBiayalain", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[173]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[174]" id="bagian_akses[174]" value="updateBiayalain" <?= isset($hakAkses) && in_array("updateBiayalain", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[174]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[175]" id="bagian_akses[175]" value="deleteBiayalain" <?= isset($hakAkses) && in_array("deleteBiayalain", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[175]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="estatem">Estate Management</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[158]" id="bagian_akses[158]" value="estatem" <?= isset($hakAkses) && in_array("estatem", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[158]">Data Estate Management</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[159]" id="bagian_akses[159]" value="addEstatem" <?= isset($hakAkses) && in_array("addEstatem", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[159]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[160]" id="bagian_akses[160]" value="insertEstatem" <?= isset($hakAkses) && in_array("insertEstatem", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[160]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[161]" id="bagian_akses[161]" value="editEstatem" <?= isset($hakAkses) && in_array("editEstatem", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[161]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[162]" id="bagian_akses[162]" value="updateEstatem" <?= isset($hakAkses) && in_array("updateEstatem", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[162]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[163]" id="bagian_akses[163]" value="deleteEstatem" <?= isset($hakAkses) && in_array("deleteEstatem", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[163]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="upah">Master Upah</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[133]" id="bagian_akses[133]" value="upah" <?= isset($hakAkses) && in_array("upah", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[133]">Data Upah</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[134]" id="bagian_akses[134]" value="addUpah" <?= isset($hakAkses) && in_array("addUpah", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[134]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[135]" id="bagian_akses[135]" value="insertUpah" <?= isset($hakAkses) && in_array("insertUpah", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[135]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[136]" id="bagian_akses[136]" value="editUpah" <?= isset($hakAkses) && in_array("editUpah", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[136]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[137]" id="bagian_akses[137]" value="updateUpah" <?= isset($hakAkses) && in_array("updateUpah", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[137]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[138]" id="bagian_akses[138]" value="deleteUpah" <?= isset($hakAkses) && in_array("deleteUpah", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[138]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="customer">Data KPR</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[61]" id="bagian_akses[61]" value="kpr" <?= isset($hakAkses) && in_array("kpr", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[61]">Data KPR</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[62]" id="bagian_akses[62]" value="addKpr" <?= isset($hakAkses) && in_array("addKpr", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[62]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[63]" id="bagian_akses[63]" value="insertKpr" <?= isset($hakAkses) && in_array("insertKpr", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[63]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[64]" id="bagian_akses[64]" value="editKpr" <?= isset($hakAkses) && in_array("editKpr", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[64]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[65]" id="bagian_akses[65]" value="updateKpr" <?= isset($hakAkses) && in_array("updateKpr", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[65]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[66]" id="bagian_akses[66]" value="deleteKpr" <?= isset($hakAkses) && in_array("deleteKpr", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[66]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="assets">Asset</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[67]" id="bagian_akses[67]" value="assets" <?= isset($hakAkses) && in_array("assets", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[67]">Data Asset</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[68]" id="bagian_akses[68]" value="addAsset" <?= isset($hakAkses) && in_array("addAsset", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[68]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[69]" id="bagian_akses[69]" value="insertAsset" <?= isset($hakAkses) && in_array("insertAsset", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[69]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[70]" id="bagian_akses[70]" value="editAsset" <?= isset($hakAkses) && in_array("editAsset", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[70]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[71]" id="bagian_akses[71]" value="updateAsset" <?= isset($hakAkses) && in_array("updateAsset", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[71]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[72]" id="bagian_akses[72]" value="deleteAsset" <?= isset($hakAkses) && in_array("deleteAsset", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[72]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="rap">RAP</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[73]" id="bagian_akses[73]" value="rap" <?= isset($hakAkses) && in_array("rap", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[73]">Data RAP</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[74]" id="bagian_akses[74]" value="addRap" <?= isset($hakAkses) && in_array("addRap", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[74]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[75]" id="bagian_akses[75]" value="insertRap" <?= isset($hakAkses) && in_array("insertRap", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[75]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[76]" id="bagian_akses[76]" value="editRap" <?= isset($hakAkses) && in_array("editRap", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[76]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[77]" id="bagian_akses[77]" value="updateRap" <?= isset($hakAkses) && in_array("updateRap", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[77]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[78]" id="bagian_akses[78]" value="deleteRap" <?= isset($hakAkses) && in_array("deleteRap", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[78]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="kaskecil">Kas Kecil</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[79]" id="bagian_akses[79]" value="kaskecil" <?= isset($hakAkses) && in_array("kaskecil", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[79]">Data Kas Kecil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[80]" id="bagian_akses[80]" value="addKaskecil" <?= isset($hakAkses) && in_array("addKaskecil", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[80]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[81]" id="bagian_akses[81]" value="insertKaskecil" <?= isset($hakAkses) && in_array("insertKaskecil", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[81]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[82]" id="bagian_akses[82]" value="editKaskecil" <?= isset($hakAkses) && in_array("editKaskecil", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[82]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[83]" id="bagian_akses[83]" value="updateKaskecil" <?= isset($hakAkses) && in_array("updateKaskecil", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[83]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[84]" id="bagian_akses[84]" value="deleteKaskecil" <?= isset($hakAkses) && in_array("deleteKaskecil", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[84]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="pembelian">Pembelian Barang</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[85]" id="bagian_akses[85]" value="pembelian" <?= isset($hakAkses) && in_array("pembelian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[85]">Data Pembelian</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[86]" id="bagian_akses[86]" value="addPembelian" <?= isset($hakAkses) && in_array("addPembelian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[86]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[87]" id="bagian_akses[87]" value="insertPembelian" <?= isset($hakAkses) && in_array("insertPembelian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[87]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[88]" id="bagian_akses[88]" value="editPembelian" <?= isset($hakAkses) && in_array("editPembelian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[88]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[89]" id="bagian_akses[89]" value="updatePembelian" <?= isset($hakAkses) && in_array("updatePembelian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[89]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[139]" id="bagian_akses[139]" value="approvalPembelian" <?= isset($hakAkses) && in_array("approvalPembelian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[139]">Approval</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[90]" id="bagian_akses[90]" value="deletePembelian" <?= isset($hakAkses) && in_array("deletePembelian", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[90]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ongkoskirim">Transaksi Ongkos Kirim</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[140]" id="bagian_akses[140]" value="ongkoskirim" <?= isset($hakAkses) && in_array("ongkoskirim", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[140]">Data Ongkos Kirim</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[141]" id="bagian_akses[141]" value="addOngkir" <?= isset($hakAkses) && in_array("addOngkir", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[141]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[142]" id="bagian_akses[142]" value="insertOngkir" <?= isset($hakAkses) && in_array("insertOngkir", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[142]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[143]" id="bagian_akses[143]" value="editOngkir" <?= isset($hakAkses) && in_array("editOngkir", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[143]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[144]" id="bagian_akses[144]" value="updateOngkir" <?= isset($hakAkses) && in_array("updateOngkir", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[144]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[145]" id="bagian_akses[145]" value="deleteOngkir" <?= isset($hakAkses) && in_array("deleteOngkir", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[145]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="operasional">Transaksi Operasional</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[146]" id="bagian_akses[146]" value="operasional" <?= isset($hakAkses) && in_array("operasional", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[146]">Data Operasional</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[147]" id="bagian_akses[147]" value="addOperasional" <?= isset($hakAkses) && in_array("addOperasional", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[147]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[148]" id="bagian_akses[148]" value="insertOperasional" <?= isset($hakAkses) && in_array("insertOperasional", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[148]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[149]" id="bagian_akses[149]" value="editOperasional" <?= isset($hakAkses) && in_array("editOperasional", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[149]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[150]" id="bagian_akses[150]" value="updateOperasional" <?= isset($hakAkses) && in_array("updateOperasional", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[150]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[151]" id="bagian_akses[151]" value="deleteOperasional" <?= isset($hakAkses) && in_array("deleteOperasional", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[151]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="kasbon">Transaksi Kas Bon / Utang</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[152]" id="bagian_akses[152]" value="kasbon" <?= isset($hakAkses) && in_array("kasbon", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[152]">Data Kas Bon</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[153]" id="bagian_akses[153]" value="addKasbon" <?= isset($hakAkses) && in_array("addKasbon", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[153]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[154]" id="bagian_akses[154]" value="insertKasbon" <?= isset($hakAkses) && in_array("insertKasbon", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[154]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[155]" id="bagian_akses[155]" value="editKasbon" <?= isset($hakAkses) && in_array("editKasbon", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[155]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[156]" id="bagian_akses[156]" value="updateKasbon" <?= isset($hakAkses) && in_array("updateKasbon", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[156]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[157]" id="bagian_akses[157]" value="deleteKasbon" <?= isset($hakAkses) && in_array("deleteKasbon", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[157]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="barangkeluar">Barang Keluar</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[91]" id="bagian_akses[91]" value="barangkeluar" <?= isset($hakAkses) && in_array("barangkeluar", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[91]">Data Barang Keluar</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[92]" id="bagian_akses[92]" value="addBarangKeluar" <?= isset($hakAkses) && in_array("addBarangKeluar", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[92]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[93]" id="bagian_akses[93]" value="insertBarangKeluar" <?= isset($hakAkses) && in_array("insertBarangKeluar", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[93]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[94]" id="bagian_akses[94]" value="editBarangKeluar" <?= isset($hakAkses) && in_array("editBarangKeluar", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[94]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[95]" id="bagian_akses[95]" value="updateBarangKeluar" <?= isset($hakAkses) && in_array("updateBarangKeluar", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[95]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[96]" id="bagian_akses[96]" value="deleteBarangKeluar" <?= isset($hakAkses) && in_array("deleteBarangKeluar", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[96]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ubk">Transaksi Upah</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[97]" id="bagian_akses[97]" value="ubk" <?= isset($hakAkses) && in_array("ubk", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[97]">Data Transaksi Upah</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[98]" id="bagian_akses[98]" value="addUbk" <?= isset($hakAkses) && in_array("addUbk", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[98]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[99]" id="bagian_akses[99]" value="insertUbk" <?= isset($hakAkses) && in_array("insertUbk", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[99]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[100]" id="bagian_akses[100]" value="editUbk" <?= isset($hakAkses) && in_array("editUbk", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[100]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[101]" id="bagian_akses[101]" value="updateUbk" <?= isset($hakAkses) && in_array("updateUbk", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[101]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[102]" id="bagian_akses[102]" value="deleteUbk" <?= isset($hakAkses) && in_array("deleteUbk", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[102]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ledger">General Ledger</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[103]" id="bagian_akses[103]" value="ledger" <?= isset($hakAkses) && in_array("ledger", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[103]">Data Ledger</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[104]" id="bagian_akses[104]" value="addLedger" <?= isset($hakAkses) && in_array("addLedger", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[104]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[105]" id="bagian_akses[105]" value="insertLedger" <?= isset($hakAkses) && in_array("insertLedger", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[105]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[106]" id="bagian_akses[106]" value="editLedger" <?= isset($hakAkses) && in_array("editLedger", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[106]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[107]" id="bagian_akses[107]" value="updateLedger" <?= isset($hakAkses) && in_array("updateLedger", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[107]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[108]" id="bagian_akses[108]" value="deleteLedger" <?= isset($hakAkses) && in_array("deleteLedger", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[108]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="penjualanunit">Penjualan Unit</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[109]" id="bagian_akses[109]" value="penjualanunit" <?= isset($hakAkses) && in_array("penjualanunit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[109]">Data Customer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[110]" id="bagian_akses[110]" value="addPenjualan" <?= isset($hakAkses) && in_array("addPenjualan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[110]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[111]" id="bagian_akses[111]" value="insertPenjualan" <?= isset($hakAkses) && in_array("insertPenjualan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[111]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[112]" id="bagian_akses[112]" value="editPenjualan" <?= isset($hakAkses) && in_array("editPenjualan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[112]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[113]" id="bagian_akses[113]" value="updatePenjualan" <?= isset($hakAkses) && in_array("updatePenjualan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[113]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[114]" id="bagian_akses[114]" value="deletePenjualan" <?= isset($hakAkses) && in_array("deletePenjualan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[114]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="laporanbarang">Laporan</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[115]" id="bagian_akses[115]" value="laporanbarang_harga" <?= isset($hakAkses) && in_array("laporanbarang_harga", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[115]">Tampilkan Harga</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[116]" id="bagian_akses[116]" value="rekapmaterial" <?= isset($hakAkses) && in_array("rekapmaterial", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[116]">Rekap Material</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[117]" id="bagian_akses[117]" value="laporanbarang" <?= isset($hakAkses) && in_array("laporanbarang", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[117]">Laporan Barang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[118]" id="bagian_akses[118]" value="hutangsuplier" <?= isset($hakAkses) && in_array("hutangsuplier", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[118]">Hutang Suplier</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[119]" id="bagian_akses[119]" value="laporanubk" <?= isset($hakAkses) && in_array("laporanubk", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[119]">Laporan UBK</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[120]" id="bagian_akses[120]" value="laporanprogresunit" <?= isset($hakAkses) && in_array("laporanprogresunit", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[120]">Laporan Progress</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="neracaSaldo">Laporan Akuntansi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[121]" id="bagian_akses[121]" value="neracaSaldo" <?= isset($hakAkses) && in_array("neracaSaldo", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[121]">Neraca Saldo</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[122]" id="bagian_akses[122]" value="bukubesar" <?= isset($hakAkses) && in_array("bukubesar", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[122]">Buku Besar</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[123]" id="bagian_akses[123]" value="labarugi" <?= isset($hakAkses) && in_array("labarugi", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[123]">Laba Rugi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[124]" id="bagian_akses[124]" value="laporanneraca" <?= isset($hakAkses) && in_array("laporanneraca", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[124]">Laporan Neraca</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="suplier">Pengaturan Aplikasi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[125]" id="bagian_akses[125]" value="pengaturan" <?= isset($hakAkses) && in_array("pengaturan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[125]">Pengaturan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[126]" id="bagian_akses[126]" value="updatePengaturan" <?= isset($hakAkses) && in_array("updatePengaturan", $hakAkses) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[126]">Update</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mt-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>