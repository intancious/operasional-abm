<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$request = \Config\Services::request();

$progresModel = new \App\Models\ProgresModel();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<?= $title_bar; ?>
        <a href="/dashboard/laporanprogresunit"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="row">
    <div class="col-lg">
        <div class="card shadow mb-4 bg-primary text-white">
            <div class="card-body">
                <form action="" method="get">
                    <div class="form-group">
                        <label for="unit">UNIT</label>
                        <select name="unit" id="unit" class="form-control form-control-sm <?= $validation->hasError('unit') ? 'is-invalid' : ''; ?> selectpicker" data-live-search="true">
                            <option value="" data-tokens="">:: SEMUA ::</option>
                            <?php
                            $unitModel = new \App\Models\UnitModel();
                            $units = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
                                ->select('unit.*, types.type_nama')->orderBy('unit.unit_nama', 'ASC')->findAll();
                            foreach ($units as $row) { ?>
                                <option value="<?= $row['unit_id']; ?>" data-tokens="<?= $row['unit_nama']; ?> (<?= $row['type_nama']; ?>)" <?= $request->getVar('unit') == $row['unit_id'] ? 'selected' : ''; ?>><?= strtoupper($row['unit_nama']); ?> (<?= $row['type_nama']; ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-light btn-sm mr-2"><i class="fas fa-sm fa-filter text-secondary mr-1"></i> FILTER</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($request->getVar('unit')) { ?>
    <div class="card shadow mb-4">
        <div class="card-body">
            <?= session()->get('pesan'); ?>
            <div class="table-responsive">
                <table class="table table-bordered small" id="customFixedTable2" style="width:100%">
                    <thead class="thead-light font-weight-bold">
                        <tr>
                            <th colspan="2">UNIT</th>
                            <th colspan="2">RAP</th>
                            <th colspan="2">REALISASI</th>
                            <th colspan="3"></th>
                        </tr>
                        <tr>
                            <th>TIPE</th>
                            <th>NAMA UNIT</th>
                            <th style="background-color: #eaecf4; font-weight: bold;">VOLUME</th>
                            <th style="background-color: #eaecf4; font-weight: bold;">NILAI</th>
                            <th>VOLUME</th>
                            <th>NILAI</th>

                            <th style="background-color: #eaecf4; font-weight: bold;">NILAI TANAH</th>
                            <th style="background-color: #eaecf4; font-weight: bold;">HPP</th>

                            <th>PROGRES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        if ($units) {
                            foreach ($units as $row) :
                                $progres = $progresModel->getProgresUnit($row['unit_id']);
                        ?>
                                <tr class="text-uppercase <?= $progres['totalVolumeKeluar'] > $progres['totalVolumeRap'] ? 'text-danger' : ''; ?>">
                                    <td style="vertical-align: middle;"><?= $row['type_nama']; ?></td>
                                    <td style="vertical-align: middle;"><?= $row['unit_nama']; ?></td>
                                    <td style="vertical-align: middle;"><?= number_format($progres['totalVolumeRap'], 2, ',', '.'); ?></td>
                                    <td style="vertical-align: middle;"><?= number_format($progres['totalNilaiRap'], 2, ',', '.'); ?></td>
                                    <td style="vertical-align: middle;"><?= number_format($progres['totalVolumeKeluar'], 2, ',', '.'); ?></td>
                                    <td style="vertical-align: middle;"><?= number_format($progres['totalNilaiKeluar'], 2, ',', '.'); ?></td>
                                    <td style="vertical-align: middle;"><?= number_format($progres['nilaiTanah'], 2, ',', '.'); ?></td>
                                    <td style="vertical-align: middle;"><?= number_format($progres['nilaiHpp'], 2, ',', '.'); ?></td>
                                    <td style="vertical-align: middle;">
                                        <a href="/dashboard/laporandetailprogres/<?= $row['unit_id']; ?>" class="text-decoration-none">
                                            <?= $progres['nilaiProsentase'] ? round($progres['nilaiProsentase'], 2) : 0; ?>%
                                            <div class="progress mt-1">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progres['nilaiProsentase'] ? round($progres['nilaiProsentase'], 2) : 0; ?>%" aria-valuenow="<?= $progres['nilaiProsentase'] ? round($progres['nilaiProsentase'], 2) : 0; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                if ($row['unit_tipe'] != 6) {
                                    $progresRumahInduk = $progresModel->getTransaksiUpah($row['unit_id'], 27);
                                ?>
                                    <tr class="text-uppercase <?= $progresRumahInduk['totalVolumeKeluar'] > $progresRumahInduk['totalVolumeRap'] ? 'text-danger' : ''; ?>">
                                        <td style="vertical-align: middle;">BORONGAN PEKERJAAN RUMAH INDUK</td>
                                        <td style="vertical-align: middle;"><?= $row['unit_nama']; ?> (<?= $row['type_nama']; ?>)</td>
                                        <td style="vertical-align: middle;"><?= number_format($progresRumahInduk['totalVolumeRap'], 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($progresRumahInduk['totalNilaiRap'], 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($progresRumahInduk['totalVolumeKeluar'], 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;"><?= number_format($progresRumahInduk['totalNilaiKeluar'], 2, ',', '.'); ?></td>
                                        <td style="vertical-align: middle;">-</td>
                                        <td style="vertical-align: middle;">-</td>
                                        <td style="vertical-align: middle;">
                                            <?= $progresRumahInduk['nilaiProsentase'] ? round($progresRumahInduk['nilaiProsentase'], 2) : 0; ?>%
                                            <div class="progress mt-1">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progresRumahInduk['nilaiProsentase'] ? round($progresRumahInduk['nilaiProsentase'], 2) : 0; ?>%" aria-valuenow="<?= $progresRumahInduk['nilaiProsentase'] ? round($progresRumahInduk['nilaiProsentase'], 2) : 0; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php endforeach;
                        } else { ?>
                            <tr>
                                <td colspan="9" class="text-center font-italic">Data belum tersedia.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } ?>
<?= $this->endSection(); ?>