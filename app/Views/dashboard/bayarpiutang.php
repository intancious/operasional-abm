<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$rekeningModel = new \App\Models\KoderekeningModel();
$puModel = new \App\Models\PenjualanunitModel();
$penjualanunit = $puModel->where('penjualan_unit.pu_id', $tagihan['tp_pu'])
    ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
    ->join('unit', 'unit.unit_id = penjualan_unit.pu_unit', 'left')
    ->join('types', 'types.type_id = unit.unit_tipe', 'left')
    ->select('penjualan_unit.*, customers.cust_nama, unit.unit_nama, types.type_nama')->first();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;
        <a href="/dashboard/piutanglist"><i class="fas fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<?= session()->get('pesan'); ?>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="/dashboard/updatePiutang" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="idTp" id="idTp" value="<?= $tagihan['tp_id']; ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Atas Nama</label>
                        <input readonly type="text" name="nama" id="nama" class="form-control" value="<?= strtoupper($penjualanunit['cust_nama']); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="unit">Unit/Tipe</label>
                        <input readonly type="text" name="unit" id="unit" class="form-control" value="<?= $penjualanunit['unit_nama']; ?>  (<?= $penjualanunit['type_nama']; ?>)">
                    </div>
                </div>
            </div>
            <hr />
            <div class="form-group">
                <label for="jenis">Jenis Transaksi</label>
                <select name="jenis" id="jenis" class="form-control">
                    <option value="">:: PILIH ::</option>
                    <option value="1" <?= $tagihan['tp_jenis'] == 1 ? 'selected' : ''; ?>>BAYAR CUSTOMER</option>
                    <option value="2" <?= $tagihan['tp_jenis'] == 2 ? 'selected' : ''; ?>>BAYAR KPR</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nofaktur">Nomor</label>
                        <input required readonly type="text" name="nofaktur" id="nofaktur" class="form-control" value="<?= $tagihan['tp_nomor']; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="angsuran">Angsuran Ke</label>
                        <input readonly type="text" name="angsuran" id="angsuran" class="form-control" value="<?= $tagihan['tp_angsuran'] ? $tagihan['tp_angsuran'] : '-'; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tgljthtempo">Tgl. Jatuh Tempo</label>
                        <input type="text" name="tgljthtempo" id="tgljthtempo" class="form-control" value="<?= $tagihan['tp_jthtempo'] ? date('d-m-Y', strtotime($tagihan['tp_jthtempo'])) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nilai">Nominal</label>
                        <input type="text" name="nilai" id="tagnilai" class="form-control" value="<?= $tagihan['tp_nilai']; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tglbayar">Tgl. Bayar</label>
                        <input type="text" name="tglbayar" id="tglbayar" class="form-control" value="<?= $tagihan['tp_tglbayar'] ? date('d-m-Y', strtotime($tagihan['tp_tglbayar'])) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bayar">Bayar</label>
                        <input type="text" name="bayar" id="tagbayar" class="form-control" value="<?= $tagihan['tp_nominal']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="uraian">Uraian / Keterangan</label>
                <textarea name="uraian" id="uraian" rows="3" class="form-control"><?= $tagihan['tp_keterangan']; ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="debet">Debet</label>
                        <select name="debet" id="debet" class="form-control <?= $validation->hasError('debet') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: PILIH ::</option>
                            <?php
                            foreach ($rekeningModel->orderBy('rek_kode', 'ASC')->findAll() as $row) { ?>
                                <option value="<?= $row['rek_id']; ?>" <?= $tagihan['tp_debet'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
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
                                <option value="<?= $row['rek_id']; ?>" <?= $tagihan['tp_kredit'] == $row['rek_id'] ? 'selected' : ''; ?> data-tokens="(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?>">(<?= strtoupper($row['rek_kode']); ?>) <?= strtoupper($row['rek_nama']); ?></option>
                            <?php } ?>
                        </select>
                        <div class="invalid-feedback">
                            <?= $validation->getError('kredit'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">SIMPAN</button>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>