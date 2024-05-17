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
        <form action="/dashboard/updateBagian" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="id" id="id" value="<?= $bagian['bagian_id']; ?>">
            <div class="form-group">
                <label for="nama">Nama Bagian</label>
                <input required type="text" name="nama" id="nama" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" value="<?= old('nama') ? old('nama') : $bagian['bagian_nama']; ?>">
                <div class="invalid-feedback">
                    <?= $validation->getError('nama'); ?>
                </div>
            </div>

            <p class="mt-4 mb-3 font-weight-bold">Hak Akses</p>
            <?php
            $bagianArr = explode(',', $bagian['bagian_akses']);
            ?>
            <div class="row" style="max-height: 500px; overflow-y: auto;">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user">Data User</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[1]" id="bagian_akses[1]" value="users" <?= in_array("users", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[1]">Data User</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[2]" id="bagian_akses[2]" value="addUser" <?= in_array("addUser", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[2]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[3]" id="bagian_akses[3]" value="insertUser" <?= in_array("insertUser", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[3]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[4]" id="bagian_akses[4]" value="editUser" <?= in_array("editUser", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[4]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[5]" id="bagian_akses[5]" value="updateUser" <?= in_array("updateUser", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[5]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[6]" id="bagian_akses[6]" value="deleteUser" <?= in_array("deleteUser", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[6]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="bagian">Data Bagian</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[7]" id="bagian_akses[7]" value="bagian" <?= in_array("bagian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[7]">Data Bagian</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[8]" id="bagian_akses[8]" value="addBagian" <?= in_array("addBagian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[8]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[9]" id="bagian_akses[9]" value="insertBagian" <?= in_array("insertBagian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[9]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[10]" id="bagian_akses[10]" value="editBagian" <?= in_array("editBagian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[10]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[11]" id="bagian_akses[11]" value="updateBagian" <?= in_array("updateBagian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[11]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[12]" id="bagian_akses[12]" value="deleteBagian" <?= in_array("deleteBagian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[12]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="rekening">Data Rekening</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[13]" id="bagian_akses[13]" value="rekening" <?= in_array("rekening", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[13]">Data Rekening</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[14]" id="bagian_akses[14]" value="addRekening" <?= in_array("addRekening", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[14]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[15]" id="bagian_akses[15]" value="insertRekening" <?= in_array("insertRekening", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[15]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[16]" id="bagian_akses[16]" value="editRekening" <?= in_array("editRekening", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[16]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[17]" id="bagian_akses[17]" value="updateRekening" <?= in_array("updateRekening", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[17]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[18]" id="bagian_akses[18]" value="deleteRekening" <?= in_array("deleteRekening", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[18]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="satuan">Data Satuan</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[19]" id="bagian_akses[19]" value="satuan" <?= in_array("satuan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[19]">Data Satuan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[20]" id="bagian_akses[20]" value="addSatuan" <?= in_array("addSatuan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[20]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[21]" id="bagian_akses[21]" value="insertSatuan" <?= in_array("insertSatuan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[21]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[22]" id="bagian_akses[22]" value="editSatuan" <?= in_array("editSatuan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[22]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[23]" id="bagian_akses[23]" value="updateSatuan" <?= in_array("updateSatuan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[23]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[24]" id="bagian_akses[24]" value="deleteSatuan" <?= in_array("deleteSatuan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[24]">Delete</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="suplier">Data Kategori Barang</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[25]" id="bagian_akses[25]" value="kategoriBarang" <?= in_array("kategoriBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[25]">Data Kategori Barang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[26]" id="bagian_akses[26]" value="addKategoriBarang" <?= in_array("addKategoriBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[26]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[27]" id="bagian_akses[27]" value="insertKategoriBarang" <?= in_array("insertKategoriBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[27]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[28]" id="bagian_akses[28]" value="editKategoriBarang" <?= in_array("editKategoriBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[28]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[29]" id="bagian_akses[29]" value="updateKategoriBarang" <?= in_array("updateKategoriBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[29]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[30]" id="bagian_akses[30]" value="deleteKategoriBarang" <?= in_array("deleteKategoriBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[30]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="barang">Data Barang</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[31]" id="bagian_akses[31]" value="barang" <?= in_array("barang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[31]">Data Barang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[32]" id="bagian_akses[32]" value="addBarang" <?= in_array("addBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[32]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[33]" id="bagian_akses[33]" value="insertBarang" <?= in_array("insertBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[33]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[34]" id="bagian_akses[34]" value="editBarang" <?= in_array("editBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[34]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[35]" id="bagian_akses[35]" value="updateBarang" <?= in_array("updateBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[35]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[36]" id="bagian_akses[36]" value="deleteBarang" <?= in_array("deleteBarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[36]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tipeUnit">Tipe Unit</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[37]" id="bagian_akses[37]" value="tipeUnit" <?= in_array("tipeUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[37]">Data Tipe Unit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[38]" id="bagian_akses[38]" value="addTipeUnit" <?= in_array("addTipeUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[38]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[39]" id="bagian_akses[39]" value="insertTipeUnit" <?= in_array("insertTipeUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[39]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[40]" id="bagian_akses[40]" value="editTipeUnit" <?= in_array("editTipeUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[40]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[41]" id="bagian_akses[41]" value="updateTipeUnit" <?= in_array("updateTipeUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[41]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[42]" id="bagian_akses[42]" value="deleteTipeUnit" <?= in_array("deleteTipeUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[42]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="unit">Data Unit</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[43]" id="bagian_akses[43]" value="unit" <?= in_array("unit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[43]">Data Unit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[44]" id="bagian_akses[44]" value="addUnit" <?= in_array("addUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[44]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[45]" id="bagian_akses[45]" value="insertUnit" <?= in_array("insertUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[45]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[46]" id="bagian_akses[46]" value="editUnit" <?= in_array("editUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[46]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[47]" id="bagian_akses[47]" value="updateUnit" <?= in_array("updateUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[47]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[48]" id="bagian_akses[48]" value="deleteUnit" <?= in_array("deleteUnit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[48]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="suplier">Data Suplier</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[49]" id="bagian_akses[49]" value="suplier" <?= in_array("suplier", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[49]">Data Suplier</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[50]" id="bagian_akses[50]" value="addSuplier" <?= in_array("addSuplier", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[50]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[51]" id="bagian_akses[51]" value="insertSuplier" <?= in_array("insertSuplier", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[51]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[52]" id="bagian_akses[52]" value="editSuplier" <?= in_array("editSuplier", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[52]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[53]" id="bagian_akses[53]" value="updateSuplier" <?= in_array("updateSuplier", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[53]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[54]" id="bagian_akses[54]" value="deleteSuplier" <?= in_array("deleteSuplier", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[54]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="customer">Data Customer</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[55]" id="bagian_akses[55]" value="customer" <?= in_array("customer", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[55]">Data Customer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[56]" id="bagian_akses[56]" value="addCustomer" <?= in_array("addCustomer", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[56]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[57]" id="bagian_akses[57]" value="insertCustomer" <?= in_array("insertCustomer", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[57]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[58]" id="bagian_akses[58]" value="editCustomer" <?= in_array("editCustomer", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[58]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[59]" id="bagian_akses[59]" value="updateCustomer" <?= in_array("updateCustomer", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[59]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[60]" id="bagian_akses[60]" value="deleteCustomer" <?= in_array("deleteCustomer", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[60]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tukang">Data Tukang</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[127]" id="bagian_akses[127]" value="tukang" <?= in_array("tukang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[127]">Data Tukang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[128]" id="bagian_akses[128]" value="addTukang" <?= in_array("addTukang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[128]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[129]" id="bagian_akses[129]" value="insertTukang" <?= in_array("insertTukang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[129]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[130]" id="bagian_akses[130]" value="editTukang" <?= in_array("editTukang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[130]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[131]" id="bagian_akses[131]" value="updateTukang" <?= in_array("updateTukang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[131]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[132]" id="bagian_akses[132]" value="deleteTukang" <?= in_array("deleteTukang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[132]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="marketing">Marketing</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[164]" id="bagian_akses[164]" value="marketing" <?= in_array("marketing", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[164]">Data Marketing</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[165]" id="bagian_akses[165]" value="addMarketing" <?= in_array("addMarketing", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[165]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[166]" id="bagian_akses[166]" value="insertMarketing" <?= in_array("insertMarketing", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[166]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[167]" id="bagian_akses[167]" value="editMarketing" <?= in_array("editMarketing", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[167]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[168]" id="bagian_akses[168]" value="updateMarketing" <?= in_array("updateMarketing", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[168]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[169]" id="bagian_akses[169]" value="deleteMarketing" <?= in_array("deleteMarketing", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[169]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="masterAbsensi">Master Absensi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[176]" id="bagian_akses[176]" value="masterAbsensi" <?= in_array("masterAbsensi", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[176]">Data</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[177]" id="bagian_akses[177]" value="addMasterAbsensi" <?= in_array("addMasterAbsensi", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[177]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[178]" id="bagian_akses[178]" value="insertMasterAbsensi" <?= in_array("insertMasterAbsensi", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[178]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[179]" id="bagian_akses[179]" value="editMasterAbsensi" <?= in_array("editMasterAbsensi", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[179]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[180]" id="bagian_akses[180]" value="updateMasterAbsensi" <?= in_array("updateMasterAbsensi", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[180]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[181]" id="bagian_akses[181]" value="deleteMasterAbsensi" <?= in_array("deleteMasterAbsensi", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[181]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="kehadiran">Kehadiran</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[182]" id="bagian_akses[182]" value="kehadiran" <?= in_array("kehadiran", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[182]">Data</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[183]" id="bagian_akses[183]" value="addKehadiran" <?= in_array("addKehadiran", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[183]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[184]" id="bagian_akses[184]" value="insertKehadiran" <?= in_array("insertKehadiran", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[184]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[185]" id="bagian_akses[185]" value="editKehadiran" <?= in_array("editKehadiran", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[185]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[186]" id="bagian_akses[186]" value="updateKehadiran" <?= in_array("updateKehadiran", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[186]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[187]" id="bagian_akses[187]" value="deleteKehadiran" <?= in_array("deleteKehadiran", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[187]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="biayalain">Biaya Lain</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[170]" id="bagian_akses[170]" value="biayalain" <?= in_array("biayalain", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[170]">Data Biaya Lain</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[171]" id="bagian_akses[171]" value="addBiayalain" <?= in_array("addBiayalain", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[171]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[172]" id="bagian_akses[172]" value="insertBiayalain" <?= in_array("insertBiayalain", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[172]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[173]" id="bagian_akses[173]" value="editBiayalain" <?= in_array("editBiayalain", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[173]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[174]" id="bagian_akses[174]" value="updateBiayalain" <?= in_array("updateBiayalain", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[174]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[175]" id="bagian_akses[175]" value="deleteBiayalain" <?= in_array("deleteBiayalain", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[175]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="estatem">Estate Management</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[158]" id="bagian_akses[158]" value="estatem" <?= in_array("estatem", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[158]">Data Estate Management</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[159]" id="bagian_akses[159]" value="addEstatem" <?= in_array("addEstatem", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[159]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[160]" id="bagian_akses[160]" value="insertEstatem" <?= in_array("insertEstatem", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[160]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[161]" id="bagian_akses[161]" value="editEstatem" <?= in_array("editEstatem", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[161]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[162]" id="bagian_akses[162]" value="updateEstatem" <?= in_array("updateEstatem", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[162]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[163]" id="bagian_akses[163]" value="deleteEstatem" <?= in_array("deleteEstatem", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[163]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="upah">Master Upah</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[133]" id="bagian_akses[133]" value="upah" <?= in_array("upah", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[133]">Data Upah</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[134]" id="bagian_akses[134]" value="addUpah" <?= in_array("addUpah", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[134]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[135]" id="bagian_akses[135]" value="insertUpah" <?= in_array("insertUpah", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[135]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[136]" id="bagian_akses[136]" value="editUpah" <?= in_array("editUpah", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[136]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[137]" id="bagian_akses[137]" value="updateUpah" <?= in_array("updateUpah", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[137]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[138]" id="bagian_akses[138]" value="deleteUpah" <?= in_array("deleteUpah", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[138]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="customer">Data KPR</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[61]" id="bagian_akses[61]" value="kpr" <?= in_array("kpr", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[61]">Data KPR</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[62]" id="bagian_akses[62]" value="addKpr" <?= in_array("addKpr", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[62]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[63]" id="bagian_akses[63]" value="insertKpr" <?= in_array("insertKpr", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[63]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[64]" id="bagian_akses[64]" value="editKpr" <?= in_array("editKpr", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[64]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[65]" id="bagian_akses[65]" value="updateKpr" <?= in_array("updateKpr", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[65]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[66]" id="bagian_akses[66]" value="deleteKpr" <?= in_array("deleteKpr", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[66]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="assets">Asset</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[67]" id="bagian_akses[67]" value="assets" <?= in_array("assets", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[67]">Data Asset</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[68]" id="bagian_akses[68]" value="addAsset" <?= in_array("addAsset", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[68]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[69]" id="bagian_akses[69]" value="insertAsset" <?= in_array("insertAsset", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[69]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[70]" id="bagian_akses[70]" value="editAsset" <?= in_array("editAsset", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[70]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[71]" id="bagian_akses[71]" value="updateAsset" <?= in_array("updateAsset", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[71]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[72]" id="bagian_akses[72]" value="deleteAsset" <?= in_array("deleteAsset", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[72]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="rap">RAP</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[73]" id="bagian_akses[73]" value="rap" <?= in_array("rap", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[73]">Data RAP</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[74]" id="bagian_akses[74]" value="addRap" <?= in_array("addRap", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[74]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[75]" id="bagian_akses[75]" value="insertRap" <?= in_array("insertRap", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[75]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[76]" id="bagian_akses[76]" value="editRap" <?= in_array("editRap", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[76]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[77]" id="bagian_akses[77]" value="updateRap" <?= in_array("updateRap", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[77]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[78]" id="bagian_akses[78]" value="deleteRap" <?= in_array("deleteRap", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[78]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="kaskecil">Kas Kecil</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[79]" id="bagian_akses[79]" value="kaskecil" <?= in_array("kaskecil", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[79]">Data Kas Kecil</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[80]" id="bagian_akses[80]" value="addKaskecil" <?= in_array("addKaskecil", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[80]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[81]" id="bagian_akses[81]" value="insertKaskecil" <?= in_array("insertKaskecil", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[81]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[82]" id="bagian_akses[82]" value="editKaskecil" <?= in_array("editKaskecil", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[82]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[83]" id="bagian_akses[83]" value="updateKaskecil" <?= in_array("updateKaskecil", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[83]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[84]" id="bagian_akses[84]" value="deleteKaskecil" <?= in_array("deleteKaskecil", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[84]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="pembelian">Pembelian Barang</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[85]" id="bagian_akses[85]" value="pembelian" <?= in_array("pembelian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[85]">Data Pembelian</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[86]" id="bagian_akses[86]" value="addPembelian" <?= in_array("addPembelian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[86]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[87]" id="bagian_akses[87]" value="insertPembelian" <?= in_array("insertPembelian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[87]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[88]" id="bagian_akses[88]" value="editPembelian" <?= in_array("editPembelian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[88]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[89]" id="bagian_akses[89]" value="updatePembelian" <?= in_array("updatePembelian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[89]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[139]" id="bagian_akses[139]" value="approvalPembelian" <?= in_array("approvalPembelian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[139]">Approval</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[90]" id="bagian_akses[90]" value="deletePembelian" <?= in_array("deletePembelian", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[90]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ongkoskirim">Transaksi Ongkos Kirim</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[140]" id="bagian_akses[140]" value="ongkoskirim" <?= in_array("ongkoskirim", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[140]">Data Ongkos Kirim</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[141]" id="bagian_akses[141]" value="addOngkir" <?= in_array("addOngkir", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[141]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[142]" id="bagian_akses[142]" value="insertOngkir" <?= in_array("insertOngkir", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[142]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[143]" id="bagian_akses[143]" value="editOngkir" <?= in_array("editOngkir", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[143]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[144]" id="bagian_akses[144]" value="updateOngkir" <?= in_array("updateOngkir", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[144]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[145]" id="bagian_akses[145]" value="deleteOngkir" <?= in_array("deleteOngkir", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[145]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="operasional">Transaksi Operasional</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[146]" id="bagian_akses[146]" value="operasional" <?= in_array("operasional", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[146]">Data Operasional</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[147]" id="bagian_akses[147]" value="addOperasional" <?= in_array("addOperasional", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[147]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[148]" id="bagian_akses[148]" value="insertOperasional" <?= in_array("insertOperasional", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[148]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[149]" id="bagian_akses[149]" value="editOperasional" <?= in_array("editOperasional", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[149]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[150]" id="bagian_akses[150]" value="updateOperasional" <?= in_array("updateOperasional", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[150]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[151]" id="bagian_akses[151]" value="deleteOperasional" <?= in_array("deleteOperasional", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[151]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="kasbon">Transaksi Kas Bon / Utang</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[152]" id="bagian_akses[152]" value="kasbon" <?= in_array("kasbon", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[152]">Data Kas Bon</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[153]" id="bagian_akses[153]" value="addKasbon" <?= in_array("addKasbon", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[153]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[154]" id="bagian_akses[154]" value="insertKasbon" <?= in_array("insertKasbon", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[154]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[155]" id="bagian_akses[155]" value="editKasbon" <?= in_array("editKasbon", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[155]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[156]" id="bagian_akses[156]" value="updateKasbon" <?= in_array("updateKasbon", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[156]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[157]" id="bagian_akses[157]" value="deleteKasbon" <?= in_array("deleteKasbon", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[157]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="barangkeluar">Barang Keluar</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[91]" id="bagian_akses[91]" value="barangkeluar" <?= in_array("barangkeluar", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[91]">Data Barang Keluar</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[92]" id="bagian_akses[92]" value="addBarangKeluar" <?= in_array("addBarangKeluar", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[92]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[93]" id="bagian_akses[93]" value="insertBarangKeluar" <?= in_array("insertBarangKeluar", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[93]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[94]" id="bagian_akses[94]" value="editBarangKeluar" <?= in_array("editBarangKeluar", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[94]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[95]" id="bagian_akses[95]" value="updateBarangKeluar" <?= in_array("updateBarangKeluar", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[95]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[96]" id="bagian_akses[96]" value="deleteBarangKeluar" <?= in_array("deleteBarangKeluar", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[96]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ubk">Transaksi Upah</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[97]" id="bagian_akses[97]" value="ubk" <?= in_array("ubk", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[97]">Data Transaksi Upah</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[98]" id="bagian_akses[98]" value="addUbk" <?= in_array("addUbk", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[98]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[99]" id="bagian_akses[99]" value="insertUbk" <?= in_array("insertUbk", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[99]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[100]" id="bagian_akses[100]" value="editUbk" <?= in_array("editUbk", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[100]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[101]" id="bagian_akses[101]" value="updateUbk" <?= in_array("updateUbk", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[101]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[102]" id="bagian_akses[102]" value="deleteUbk" <?= in_array("deleteUbk", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[102]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ledger">General Ledger</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[103]" id="bagian_akses[103]" value="ledger" <?= in_array("ledger", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[103]">Data Ledger</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[104]" id="bagian_akses[104]" value="addLedger" <?= in_array("addLedger", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[104]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[105]" id="bagian_akses[105]" value="insertLedger" <?= in_array("insertLedger", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[105]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[106]" id="bagian_akses[106]" value="editLedger" <?= in_array("editLedger", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[106]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[107]" id="bagian_akses[107]" value="updateLedger" <?= in_array("updateLedger", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[107]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[108]" id="bagian_akses[108]" value="deleteLedger" <?= in_array("deleteLedger", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[108]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="penjualanunit">Penjualan Unit</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[109]" id="bagian_akses[109]" value="penjualanunit" <?= in_array("penjualanunit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[109]">Data Customer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[110]" id="bagian_akses[110]" value="addPenjualan" <?= in_array("addPenjualan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[110]">Add</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[111]" id="bagian_akses[111]" value="insertPenjualan" <?= in_array("insertPenjualan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[111]">Insert</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[112]" id="bagian_akses[112]" value="editPenjualan" <?= in_array("editPenjualan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[112]">Edit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[113]" id="bagian_akses[113]" value="updatePenjualan" <?= in_array("updatePenjualan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[113]">Update</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[114]" id="bagian_akses[114]" value="deletePenjualan" <?= in_array("deletePenjualan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[114]">Delete</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="laporanbarang">Laporan</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[115]" id="bagian_akses[115]" value="laporanbarang_harga" <?= in_array("laporanbarang_harga", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[115]">Tampilkan Harga</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[116]" id="bagian_akses[116]" value="rekapmaterial" <?= in_array("rekapmaterial", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[116]">Rekap Material</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[117]" id="bagian_akses[117]" value="laporanbarang" <?= in_array("laporanbarang", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[117]">Laporan Barang</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[118]" id="bagian_akses[118]" value="hutangsuplier" <?= in_array("hutangsuplier", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[118]">Hutang Suplier</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[119]" id="bagian_akses[119]" value="laporanubk" <?= in_array("laporanubk", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[119]">Laporan UBK</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[120]" id="bagian_akses[120]" value="laporanprogresunit" <?= in_array("laporanprogresunit", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[120]">Laporan Progress</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="neracaSaldo">Laporan Akuntansi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[121]" id="bagian_akses[121]" value="neracaSaldo" <?= in_array("neracaSaldo", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[121]">Neraca Saldo</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[122]" id="bagian_akses[122]" value="bukubesar" <?= in_array("bukubesar", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[122]">Buku Besar</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[123]" id="bagian_akses[123]" value="labarugi" <?= in_array("labarugi", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[123]">Laba Rugi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[124]" id="bagian_akses[124]" value="laporanneraca" <?= in_array("laporanneraca", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[124]">Laporan Neraca</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="suplier">Pengaturan Aplikasi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[125]" id="bagian_akses[125]" value="pengaturan" <?= in_array("pengaturan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[125]">Pengaturan</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bagian_akses[126]" id="bagian_akses[126]" value="updatePengaturan" <?= in_array("updatePengaturan", $bagianArr) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="bagian_akses[126]">Update</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>