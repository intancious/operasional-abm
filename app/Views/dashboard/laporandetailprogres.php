<?= $this->extend('dashboard/template'); ?>

<?= $this->section('content'); ?>
<?php
$sesiBagian = session()->get('usr_bagian');
$progresModel = new \App\Models\ProgresModel();
$bagianModel = new \App\Models\BagianModel();
$barangModel = new \App\Models\BarangModel();
$satuanModel = new \App\Models\SatuanModel();
$upahModel = new \App\Models\UpahModel();
$trxupahItemModel = new \App\Models\TrxupahItemModel();
$bagianAccess = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagianAccess['bagian_akses']);
$request = \Config\Services::request();
$rapModel = new \App\Models\RapsModel();
$raps = $rapModel->where('rap_tipe', $unit['unit_tipe'])
    ->join('types', 'types.type_id = raps.rap_tipe', 'left')
    ->select('raps.*, types.type_id, types.type_nama')
    ->orderBy('raps.rap_id', 'ASC')->findAll();

$kabarModel = new \App\Models\KabarModel();
$kabar = $kabarModel->orderBy('kabar_id', 'ASC')->findAll();
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h5 text-uppercase mb-0 text-gray-800">
        &nbsp;<a href="/dashboard/laporanprogresunit"><i class="fas fa-sm fa-arrow-alt-circle-left mr-2"></i></a>
        <?= $title_bar; ?>
        <a href="/dashboard/laporandetailprogres/<?= $unit['unit_id']; ?>"><i class="fas fa-sm fa-sync-alt fa-sm ml-2"></i></a>
    </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><?= $title_bar; ?></li>
    </ol>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?= session()->get('pesan'); ?>
        <div class="table-responsive">
            <table id="customTable" class="table table-striped table-bordered dt-responsive wrap small" style="width:100%">
                <thead class="thead-light font-weight-bold">
                    <tr class="bg-secondary text-white">
                        <th colspan="3" class="text-center" style="vertical-align: middle;"></th>
                        <th colspan="3" class="text-center" style="vertical-align: middle;">RAP</th>
                        <th colspan="3" class="text-center" style="vertical-align: middle;">REALISASI</th>
                        <th></th>
                    </tr>
                    <tr class="bg-dark text-white">
                        <th>#</th>
                        <th>BARANG / PEKERJAAN</th>
                        <th>SATUAN</th>

                        <th>VOLUME</th>
                        <th>HARGA</th>
                        <th>SUBTOTAL</th>

                        <th>VOLUME</th>
                        <th>HARGA</th>
                        <th>SUBTOTAL</th>

                        <th>PROGRES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grandTotalVolumeRap = 0;
                    $grandTotalVolumeKeluar = 0;
                    $grandTotalNilaiRap = 0;
                    $grandTotalNilaiKeluar = 0;
                    if ($raps) {
                        foreach ($kabar as $kb) { ?>
                            <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                                <td colspan="10"><?= strtoupper($kb['kabar_nama']); ?></td>
                            </tr>
                            <?php
                            $i = 1;
                            foreach ($raps as $row) :
                                if ($row['rap_barang']) {
                                    $brgupah = $barangModel->find($row['rap_barang']);
                                    $satuan = $brgupah ? $satuanModel->find($brgupah['barang_satuan']) : '-';
                                    $kategori = $brgupah ? $kabarModel->find($brgupah['barang_kategori']) : '-';
                                } else if ($row['rap_upah']) {
                                    $brgupah = $upahModel->find($row['rap_upah']);
                                    $satuan = $satuanModel->find($brgupah['up_satuan']);
                                    $kategori = $kabarModel->find($brgupah['up_kategori']);
                                }

                                if (isset($brgupah) && isset($satuan) && isset($kategori)) {
                                    if ($kategori['kabar_id'] == $kb['kabar_id']) {
                                        // rap
                                        $volumeRap = $row['rap_volume'] ? str_replace(',', '.', $row['rap_volume']) : 0;
                                        $nilaiRap = $row['rap_harga'] ? str_replace(',', '.', $row['rap_harga']) : 0;
                                        $subTotalRap = $volumeRap * $nilaiRap;
                                        $grandTotalVolumeRap += $volumeRap;
                                        $grandTotalNilaiRap += $subTotalRap;

                                        if ($row['rap_barang']) {
                                            // barang keluar
                                            $keluar = $progresModel->getProgresKeluar($unit['unit_id'], $row['rap_barang']);
                                            $volumeKeluar = $keluar['volumeKeluar'];
                                            $nilaiKeluar = $keluar['hargaKeluar'];
                                            $subTotalKeluar = $volumeKeluar * $nilaiKeluar;
                                        }

                                        if ($row['rap_upah']) {
                                            // upah
                                            $trxupah = $trxupahItemModel->where(['tui_unit' => $unit['unit_id'], 'tui_upah' => $row['rap_upah']])->findAll();
                                            $jumlah = 0;
                                            $nilai = 0;
                                            foreach ($trxupah as $trx) {
                                                $jumlah += $trx['tui_jumlah'] ? str_replace(',', '.', $trx['tui_jumlah']) : 0;
                                                $nilai += $trx['tui_nilai'] ? str_replace(',', '.', $trx['tui_nilai']) : 0;
                                            }
                                            $upah = $progresModel->getProgresUpah($unit['unit_id'], $row['rap_upah']);
                                            $volumeKeluar = ($jumlah / $volumeRap);
                                            $nilaiKeluar = $nilai;
                                            $subTotalKeluar = $upah['totalUpah'];
                                        }
                                        $grandTotalVolumeKeluar += $volumeKeluar;
                                        $grandTotalNilaiKeluar += $subTotalKeluar;

                                        $persen = $volumeRap > 0 ? ($volumeKeluar / $volumeRap) * 100 : 0;

                            ?>
                                        <tr class="text-uppercase <?= $volumeKeluar > $volumeRap ? 'text-danger' : ''; ?>">
                                            <td><?= $i++; ?></td>
                                            <td>
                                                <?= $row['rap_barang'] ? strtoupper($brgupah['barang_nama']) : ($row['rap_upah'] ? strtoupper($brgupah['up_nama']) : '-'); ?>
                                            </td>
                                            <td><?= isset($satuan) ? strtoupper($satuan['satuan_nama']) : '-'; ?></td>

                                            <td align="center"><?= number_format($volumeRap, 2, ',', '.'); ?></td>
                                            <td align="right"><?= number_format($nilaiRap, 2, ',', '.'); ?></td>
                                            <td align="right"><?= number_format($subTotalRap, 2, ',', '.'); ?></td>

                                            <td align="center"><?= number_format($volumeKeluar, 2, ',', '.'); ?></td>
                                            <td align="right"><?= number_format($nilaiKeluar, 2, ',', '.'); ?></td>
                                            <td align="right"><?= number_format($subTotalKeluar, 2, ',', '.'); ?></td>

                                            <td><?= round($persen, 2); ?>%</td>
                                        </tr>
                        <?php }
                                }
                            endforeach;
                        }
                        $nilaiTanah = $unit['unit_nilaitanah'] ? str_replace(',', '.', $unit['unit_nilaitanah']) : 0;
                        ?>
                        <tr>
                            <td colspan="10">&nbsp;</td>
                        </tr>
                        <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                            <td colspan="8">NILAI TANAH</td>
                            <td align="right"><?= number_format($nilaiTanah, 2, ',', '.'); ?></td>
                            <td></td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td colspan="10" class="text-center font-italic">Data belum tersedia.</td>
                        </tr>
                    <?php } ?>
                </tbody>
                <?php $totalPersen = $grandTotalVolumeRap > 0 ? ($grandTotalVolumeKeluar / $grandTotalVolumeRap) * 100 : 0; ?>
                <tfoot>
                    <tr>
                        <td colspan="10">&nbsp;</td>
                    </tr>
                    <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                        <td colspan="3" class="font-weight-bold">GRAND TOTAL</td>

                        <td align="center"><?= number_format($grandTotalVolumeRap, 2, ',', '.'); ?></td>
                        <td></td>
                        <td align="right"><?= number_format($grandTotalNilaiRap, 2, ',', '.'); ?></td>

                        <td align="center"><?= number_format($grandTotalVolumeKeluar, 2, ',', '.'); ?></td>
                        <td></td>
                        <td align="right"><?= number_format($grandTotalNilaiKeluar + $nilaiTanah, 2, ',', '.'); ?></td>

                        <td><?= round($totalPersen, 2); ?>%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>