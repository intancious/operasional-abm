<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$rekeningModel = new \App\Models\KoderekeningModel();
$kkModel = new \App\Models\KasKecilModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/estatem"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
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
        <form action="/dashboard/insertEstatem" method="post">
            <?= csrf_field(); ?>
            <?php
            $emModel = new \App\Models\EmModel();
            $valNumb = $emModel->countAllResults() + 1;
            $nomor = 'EM' . str_pad($valNumb, 4, "0", STR_PAD_LEFT);

            $checkIsExist = $emModel->where('em_nomor', $nomor)->first();
            if ($checkIsExist) {
                $lastRow = $emModel->orderBy('em_id', 'DESC')->first();
                $exNumb = str_replace('EM', '', $lastRow['em_nomor']);
                $newNumb = intval($exNumb) + 1;
                $fixNumber = 'EM' . str_pad($newNumb, 4, "0", STR_PAD_LEFT);
            } else {
                $fixNumber = $nomor;
            }
            ?>
            <div class="form-group">
                <label for="jenis">Jenis Transaksi</label>
                <select required name="jenis" id="jenis" class="form-control">
                    <option value="">:: PILIH ::</option>
                    <option value="1">MASUK</option>
                    <option value="2">KELUAR</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nomor">Nomor</label>
                        <input required type="text" name="nomor" id="nomor" class="form-control <?= $validation->hasError('nomor') ? 'is-invalid' : ''; ?>" value="<?= old('nomor') ? old('nomor') : $fixNumber; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nomor'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input required type="text" name="tanggal" id="tanggalSelect" class="form-control <?= $validation->hasError('tanggal') ? 'is-invalid' : ''; ?>" value="<?= old('tanggal') ? old('tanggal') : date('d-m-Y'); ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('tanggal'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input required type="text" name="nama" id="nama" class="form-control <?= $validation->hasError('nama') ? 'is-invalid' : ''; ?>" value="<?= old('nama') ? old('nama') : ''; ?>">
                        <div class="invalid-feedback">
                            <?= $validation->getError('nama'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="unit">Unit</label>
                        <select name="unit" id="unit" class="form-control <?= $validation->hasError('unit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            $unitModel = new \App\Models\UnitModel();
                            foreach ($unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
                                ->join('penjualan_unit', 'penjualan_unit.pu_unit = unit.unit_id', 'left')
                                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                                ->select('unit.*, unit.unit_nama, types.type_nama, customers.cust_nama')
                                ->orderBy('unit.unit_nama', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['unit_id']; ?>" <?= old('unit') == $row['unit_id'] ? 'selected' : ''; ?> data-tokens="<?= $row['unit_nama']; ?>"><?= strtoupper($row['unit_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('unit'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="nominal">Nominal</label>
                <input required type="text" name="nominal" id="nominal" class="form-control <?= $validation->hasError('nominal') ? 'is-invalid' : ''; ?>" value="<?= old('nominal') ? old('nominal') : 0; ?>">
                <div class="invalid-feedback">
                    <?= $validation->getError('nominal'); ?>
                </div>
            </div>
            <div class="form-group">
                <label for="keterangan">Uraian / Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control <?= $validation->hasError('keterangan') ? 'is-invalid' : ''; ?>" rows="3"></textarea>
                <div class="invalid-feedback">
                    <?= $validation->getError('keterangan'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="debet">Debet</label>
                        <select name="debet" id="debet" class="form-control <?= $validation->hasError('debet') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= old('debet') == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('debet'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kredit">Kredit</label>
                        <select name="kredit" id="kredit" class="form-control <?= $validation->hasError('kredit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= old('kredit') == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('kredit'); ?>
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