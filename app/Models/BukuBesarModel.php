<?php

namespace App\Models;

use CodeIgniter\Model;

class BukuBesarModel extends Model
{

    public function getSaldoKasKecil($idKk)
    {
        $kasKecilModel = new \App\Models\KasKecilModel();
        $kaskecil = $kasKecilModel->find($idKk);
        if ($kaskecil) {
            $idRek = $kaskecil['kk_debet'];
            if ($idRek) {
                $jurnalKasKecil = $kasKecilModel->where('kk_debet', $idRek)->findAll();

                $nominal = 0;
                $kembali = 0;
                foreach ($jurnalKasKecil as $row) {
                    $nominal += $row['kk_nominal'] ? str_replace(',', '.', $row['kk_nominal']) : 0;
                    $kembali += $row['kk_kembali'] ? str_replace(',', '.', $row['kk_kembali']) : 0;
                }

                $hbModel = new \App\Models\HutangsuplierBayarModel();
                $digunakan = 0;
                $bayarPembelian = $hbModel->where(['hb_kredit' => $idRek])->findAll();
                foreach ($bayarPembelian as $row) {
                    $bayar = $row['hb_bayar'] ? str_replace(',', '.', $row['hb_bayar']) : 0;
                    $digunakan += $bayar;
                }

                $saldo = ($nominal - $digunakan) - $kembali;
            } else {
                $kasKecilModel = new \App\Models\KasKecilModel();
                $pembelianModel = new \App\Models\PembelianModel();

                $kaskecil = $kasKecilModel->find($idRek);
                $nominal = $kaskecil['kk_nominal'] ? str_replace(',', '.', $kaskecil['kk_nominal']) : 0;
                $kembali = $kaskecil['kk_kembali'] ? str_replace(',', '.', $kaskecil['kk_kembali']) : 0;

                $pembelian = $pembelianModel->where('pb_kaskecil', $idRek)->findAll();
                $totalPembelian = 0;
                $totalOngkir = 0;
                foreach ($pembelian as $row) {
                    $totalPembelian += $row['pb_total'] ? str_replace(',', '.', $row['pb_total']) : 0;
                    $totalOngkir += $row['pb_ongkir'] ? str_replace(',', '.', $row['pb_ongkir']) : 0;
                }
                $digunakan = $totalPembelian + $totalOngkir;

                $saldo = ($nominal - $digunakan) - $kembali;
            }

            return [
                'diterima'      => $nominal,
                'digunakan'     => $digunakan,
                'dikembalikan'  => $kembali,
                'saldo'         => $saldo
            ];
        }
    }

    public function saldoAkhirBb($idRekening)
    {
        $rekeningModel = new \App\Models\KoderekeningModel();
        $bukubesarItem = $this->getBukuBesar($idRekening);
        $rekening = $rekeningModel->find($idRekening);

        if ($bukubesarItem) {
            $total = count($bukubesarItem);
            foreach ($bukubesarItem as $index => $row) {
                $rekSub = substr($rekening['rek_kode'], 0, 2);

                if ($rekSub == 11 || $rekSub == 12 || $rekSub == 51 || $rekSub == 60 || $rekSub == 61 || $rekSub == 62 || $rekSub == 63 || $rekSub == 72) {
                    $saldoDebet = 0;
                    for ($in = 0; $in <= $index; $in++) {
                        $saldoDebet += $bukubesarItem[$in]['debet'];
                    }

                    $saldoKredit = 0;
                    for ($in = 0; $in <= $index; $in++) {
                        $saldoKredit -= $bukubesarItem[$in]['kredit'];
                    }
                }

                if ($rekSub == 21 || $rekSub == 22 || $rekSub == 31 || $rekSub == 41 || $rekSub == 42 || $rekSub == 71) {
                    $saldoDebet = 0;
                    for ($in = 0; $in <= $index; $in++) {
                        $saldoDebet -= $bukubesarItem[$in]['debet'];
                    }

                    $saldoKredit = 0;
                    for ($in = 0; $in <= $index; $in++) {
                        $saldoKredit += $bukubesarItem[$in]['kredit'];
                    }
                }

                if (substr($rekening['rek_kode'], 0, 1) == 8) {
                    $saldoDebet = 0;
                    for ($in = 0; $in <= $index; $in++) {
                        $saldoDebet += $bukubesarItem[$in]['debet'];
                    }

                    $saldoKredit = 0;
                    for ($in = 0; $in <= $index; $in++) {
                        $saldoKredit -= $bukubesarItem[$in]['kredit'];
                    }
                }

                if (substr($rekening['rek_kode'], 0, 1) == 9) {
                    $saldoDebet = 0;
                    for ($in = 0; $in <= $index; $in++) {
                        $saldoDebet += $bukubesarItem[$in]['debet'];
                    }

                    $saldoKredit = 0;
                    for ($in = 0; $in <= $index; $in++) {
                        $saldoKredit -= $bukubesarItem[$in]['kredit'];
                    }
                }

                $saldo[] = (isset($saldoDebet) ? $saldoDebet : 0) + (isset($saldoKredit) ? $saldoKredit : 0);
            }
            return $saldo[$total - 1];
        } else {
            return 0;
        }
    }

    public function getBukuBesar($idRek)
    {
        $kaskecilDebet = $this->getKaskecil($idRek, 'debet');
        $kaskecilKredit = $this->getKaskecil($idRek, 'kredit');

        $kaskecil2Debet = $this->getKaskecil2($idRek, 'debet');
        $kaskecil2Kredit = $this->getKaskecil2($idRek, 'kredit');

        $pembelian = $this->getPembelian($idRek, 'debet');
        $hutangsuplier = $this->getHutangSuplier($idRek, 'kredit');

        $barangKeluarDebet = $this->getBarangKeluar($idRek, 'debet');
        $barangKeluarKredit = $this->getBarangKeluar($idRek, 'kredit');

        $upahDebet = $this->getTrxupah($idRek, 'debet');
        $upahKredit = $this->getTrxupah($idRek, 'kredit');

        $getBayarTotalUpah = $this->getBayarTotalUpah($idRek);
        $getBayarTotalBon = $this->getBayarTotalBon($idRek);
        $getBayarSisaUpah = $this->getBayarSisaUpah($idRek);

        $bayarHsDebet = $this->getBayarHutangSuplier($idRek, 'debet');
        $bayarHsKredit = $this->getBayarHutangSuplier($idRek, 'kredit');

        $ongkirDebet = $this->getOngkosKirim($idRek, 'debet');
        $ongkirKredit = $this->getOngkosKirim($idRek, 'kredit');

        $operasionalDebet = $this->getOperasional($idRek, 'debet');
        $operasionalKredit = $this->getOperasional($idRek, 'kredit');

        $ledgerDebet = $this->getGeneralLedger($idRek, 'debet');
        $ledgerKredit = $this->getGeneralLedger($idRek, 'kredit');

        $bonLemburDebet = $this->getBonLembur($idRek, 'debet');
        $bonLemburKredit = $this->getBonLembur($idRek, 'kredit');

        $puHargaTrx = $this->puHargaTrx($idRek, 'kredit');
        $puSisaPiutang = $this->puSisaPiutang($idRek, 'debet');
        $puPiutangKpr = $this->puPiutangKpr($idRek, 'debet');

        $puTagBayarDebet = $this->puTagBayar($idRek, 'debet');
        $puTagBayarKredit = $this->puTagBayar($idRek, 'kredit');

        $puBiayaLainDebet = $this->puBiayaLain($idRek, 'debet');
        $puBiayaLainKredit = $this->puBiayaLain($idRek, 'kredit');

        $getEmDebet = $this->getEm($idRek, 'debet');
        $getEmKredit = $this->getEm($idRek, 'kredit');

        $merge = array_merge(
            $kaskecilDebet,
            $kaskecilKredit,

            $kaskecil2Debet,
            $kaskecil2Kredit,

            $pembelian,
            $hutangsuplier,

            $barangKeluarDebet,
            $barangKeluarKredit,

            $upahDebet,
            $upahKredit,

            $getBayarTotalUpah,
            $getBayarTotalBon,
            $getBayarSisaUpah,

            $bayarHsDebet,
            $bayarHsKredit,

            $ongkirDebet,
            $ongkirKredit,

            $operasionalDebet,
            $operasionalKredit,

            $ledgerDebet,
            $ledgerKredit,

            $bonLemburDebet,
            $bonLemburKredit,

            $puHargaTrx,
            $puSisaPiutang,
            $puPiutangKpr,
            $puTagBayarDebet,
            $puTagBayarKredit,
            $puBiayaLainDebet,
            $puBiayaLainKredit,

            $getEmDebet,
            $getEmKredit,
        );

        if ($merge) {
            foreach ($merge as $key => $part) {
                $sort[$key] = strtotime($part['tanggal']);
            }
            array_multisort($sort, SORT_ASC, $merge);

            $request = \Config\Services::request();
            $startDate = $request->getVar('startDate');
            $endDate = $request->getVar('endDate');
            if ($startDate && $endDate) {
                $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
                $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
                foreach ($merge as $item) {
                    if ($item['tanggal'] >= $start && $item['tanggal'] <= $end) {
                        $newarray[] = $item;
                    }
                }
            } else {
                $newarray = $merge;
            }

            return isset($newarray) ? $newarray : [];
        } else {
            return NULL;
        }
    }

    public function getKaskecil($idRek, $jenis)
    {
        $kasKecilModel = new \App\Models\KasKecilModel();
        if ($jenis == 'debet') {
            $data = $kasKecilModel->where('kk_debet', $idRek)
                ->orderBy('kk_tanggal', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $kasKecilModel->where('kk_kredit', $idRek)
                ->orderBy('kk_tanggal', 'ASC')
                ->findAll();
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $jenisKas = $kasKecilModel->getJenisKas($row['kk_jenis']);
                if ($row['kk_uraian']) {
                    $uraian = 'KAS KECIL ' . $jenisKas['name'] . ' NOMOR ' . $row['kk_nomor'] . ' - ' . strtoupper($row['kk_uraian']);
                } else {
                    $uraian = 'KAS KECIL ' . $jenisKas['name'] . ' NOMOR ' . $row['kk_nomor'];
                }
                $total = $row['kk_nominal'] ? str_replace(',', '.', $row['kk_nominal']) : 0;
                $results[] = [
                    'tanggal'   => $row['kk_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['kk_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $total : 0,
                    'kredit'    => $jenis == 'kredit' ? $total : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getEm($idRek, $jenis)
    {
        $emModel = new \App\Models\EmModel();
        if ($jenis == 'debet') {
            $data = $emModel->where('em_debet', $idRek)
                ->orderBy('em_tanggal', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $emModel->where('em_kredit', $idRek)
                ->orderBy('em_tanggal', 'ASC')
                ->findAll();
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $jenisKas = $row['em_jenis'] == 1 ? 'Masuk' : ($row['em_jenis'] == 2 ? 'Keluar' : '');
                if ($row['em_keterangan']) {
                    $uraian = 'ESTATE MANAGEMENT ' . $jenisKas . ' NOMOR ' . $row['em_nomor'] . ' - ' . strtoupper($row['em_keterangan']);
                } else {
                    $uraian = 'KAS KECIL ' . $jenisKas . ' NOMOR ' . $row['em_nomor'];
                }
                $total = $row['em_nominal'] ? str_replace(',', '.', $row['em_nominal']) : 0;
                $results[] = [
                    'tanggal'   => $row['em_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['em_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $total : 0,
                    'kredit'    => $jenis == 'kredit' ? $total : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getKaskecil2($idRek, $jenis)
    {
        $kasKecilModel = new \App\Models\KasKecilModel();
        if ($jenis == 'kredit') {
            $data = $kasKecilModel->where('kk_debet', $idRek)
                ->orderBy('kk_tanggal', 'ASC')
                ->findAll();
        }
        if ($jenis == 'debet') {
            $data = $kasKecilModel->where('kk_kredit', $idRek)
                ->orderBy('kk_tanggal', 'ASC')
                ->findAll();
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['kk_status'] == 2) {
                    $jenisKas = $kasKecilModel->getJenisKas($row['kk_jenis']);
                    if ($row['kk_uraian']) {
                        $uraian = 'PENGEMBALIAN KAS KECIL ' . $jenisKas['name'] . ' NOMOR ' . $row['kk_nomor'] . ' - ' . strtoupper($row['kk_uraian']);
                    } else {
                        $uraian = 'PENGEMBALIAN KAS KECIL ' . $jenisKas['name'] . ' NOMOR ' . $row['kk_nomor'];
                    }
                    $total = $row['kk_nominal'] ? str_replace(',', '.', $row['kk_nominal']) : 0;
                    $results[] = [
                        'tanggal'   => $row['kk_tanggal'],
                        'rek'       => $idRek,
                        'nomor'     => $row['kk_nomor'],
                        'uraian'    => $uraian,
                        'debet'     => $jenis == 'debet' ? $total : 0,
                        'kredit'    => $jenis == 'kredit' ? $total : 0
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function getPembelian($idRek, $jenis)
    {
        $pembelianItemModel = new \App\Models\PembelianItemModel();

        if ($jenis == 'debet') {
            $data = $pembelianItemModel->where('pembelian_item.pi_debet', $idRek)
                ->join('pembelian', 'pembelian.pb_id = pembelian_item.pi_pembelian', 'left')
                ->select('pembelian_item.*, pembelian.pb_tanggal, pembelian.pb_nomor, pembelian.pb_keterangan')
                ->orderBy('pembelian_item.created_at', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $pembelianItemModel->where('pembelian_item.pi_kredit', $idRek)
                ->join('pembelian', 'pembelian.pb_id = pembelian_item.pi_pembelian', 'left')
                ->select('pembelian_item.*, pembelian.pb_tanggal, pembelian.pb_nomor, pembelian.pb_keterangan')
                ->orderBy('pembelian_item.created_at', 'ASC')
                ->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['pb_keterangan']) {
                    $uraian = 'PEMBELIAN MATERIAL ' . $row['pb_nomor'] . ' - ' . strtoupper($row['pb_keterangan']);
                } else {
                    $uraian = 'PEMBELIAN MATERIAL ' . $row['pb_nomor'];
                }
                $total = $row['pi_total'] ? str_replace(',', '.', $row['pi_total']) : 0;
                $results[] = [
                    'tanggal'   => $row['pb_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['pb_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $total : 0,
                    'kredit'    => $jenis == 'kredit' ? $total : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getBarangKeluar($idRek, $jenis)
    {
        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
        $barangModel = new \App\Models\BarangModel();

        if ($jenis == 'debet') {
            $data = $barangKeluarModel->where('bk_debet', $idRek)
                ->orderBy('bk_tanggal', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $barangKeluarModel->where('bk_kredit', $idRek)
                ->orderBy('bk_tanggal', 'ASC')
                ->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['bk_keterangan']) {
                    $uraian = 'MATERIAL KELUAR ' . $row['bk_nomor'] . ' - ' . strtoupper($row['bk_keterangan']);
                } else {
                    $uraian = 'MATERIAL KELUAR ' . $row['bk_nomor'];
                }

                $total = 0;
                $itemKeluar = $barangKeluarItemModel->where(['bki_barangkeluar' => $row['bk_id']])->findAll();
                foreach ($itemKeluar as $item) {
                    $qty = $item['bki_qty'] ? str_replace(',', '.', $item['bki_qty']) : 0;
                    // $harga = $barangModel->rataRataHarga($item['bki_barang']);
                    $harga =  $item['bki_harga'] ? str_replace(',', '.', $item['bki_harga']) : 0;
                    $total += $qty * $harga;
                }

                $results[] = [
                    'tanggal'   => $row['bk_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['bk_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $total : 0,
                    'kredit'    => $jenis == 'kredit' ? $total : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getTrxupah($idRek, $jenis)
    {
        $trxupahItemModel = new \App\Models\TrxupahItemModel();

        if ($jenis == 'debet') {
            $data = $trxupahItemModel->where('trxupah_item.tui_debet', $idRek)
                ->join('trxupah', 'trxupah.tu_id = trxupah_item.tui_trxupah', 'left')
                ->join('tukang', 'tukang.tk_id = trxupah.tu_tukang', 'left')
                ->join('upah', 'upah.up_id = trxupah_item.tui_upah', 'left')
                ->select('trxupah_item.*, trxupah.tu_nomor, trxupah.tu_keterangan, tukang.tk_nama, upah.up_nama')
                ->orderBy('trxupah_item.tui_tanggal', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $trxupahItemModel->where('trxupah_item.tui_kredit', $idRek)
                ->join('trxupah', 'trxupah.tu_id = trxupah_item.tui_trxupah', 'left')
                ->join('tukang', 'tukang.tk_id = trxupah.tu_tukang', 'left')
                ->join('upah', 'upah.up_id = trxupah_item.tui_upah', 'left')
                ->select('trxupah_item.*, trxupah.tu_nomor, trxupah.tu_keterangan, tukang.tk_nama, upah.up_nama')
                ->orderBy('trxupah_item.tui_tanggal', 'ASC')
                ->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['tu_keterangan']) {
                    $uraian = 'UPAH ' . $row['tk_nama'] . ' - ' . $row['up_nama'] . ' - ' . strtoupper($row['tu_keterangan']);
                } else {
                    $uraian = 'UPAH ' . $row['tk_nama'] . ' - ' . $row['up_nama'];
                }

                $total = $row['tui_total'] ? str_replace(',', '.', $row['tui_total']) : 0;

                $results[] = [
                    'tanggal'   => $row['tui_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['tu_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $total : 0,
                    'kredit'    => $jenis == 'kredit' ? $total : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getBayarTotalUpah($idRek) // debet
    {
        $trxupahBayarModel = new \App\Models\TrxupahBayarModel();
        $data = $trxupahBayarModel->where('trxupah_bayar.tub_tupahdebet', $idRek)
            ->join('trxupah', 'trxupah.tu_id = trxupah_bayar.tub_trxupah', 'left')
            ->join('tukang', 'tukang.tk_id = trxupah.tu_tukang', 'left')
            ->select('trxupah_bayar.*, trxupah.tu_nomor, tukang.tk_nama')
            ->orderBy('trxupah_bayar.tub_tanggal', 'ASC')
            ->findAll();

        foreach ($data as $row) {
            if ($row['tub_keterangan']) {
                $uraian = 'TOTAL UPAH ' . strtoupper($row['tk_nama']) . ' NOMOR ' . $row['tu_nomor'] . ' - ' . strtoupper($row['tub_keterangan']);
            } else {
                $uraian = 'TOTAL UPAH ' . strtoupper($row['tk_nama']) . ' NOMOR ' . $row['tu_nomor'];
            }

            $total =  $row['tub_tupah'] ? str_replace(',', '.', $row['tub_tupah']) : 0;
            $results[] = [
                'tanggal'   => $row['tub_tanggal'],
                'rek'       => $idRek,
                'nomor'     => $row['tub_nomor'],
                'uraian'    => $uraian,
                'debet'     => $total,
                'kredit'    => 0
            ];
        }

        return isset($results) ? $results : [];
    }

    public function getBayarTotalBon($idRek) // kredit
    {
        $trxupahBayarModel = new \App\Models\TrxupahBayarModel();
        $data = $trxupahBayarModel->where('trxupah_bayar.tub_tbonkredit', $idRek)
            ->join('trxupah', 'trxupah.tu_id = trxupah_bayar.tub_trxupah', 'left')
            ->join('tukang', 'tukang.tk_id = trxupah.tu_tukang', 'left')
            ->select('trxupah_bayar.*, trxupah.tu_nomor, tukang.tk_nama')
            ->orderBy('trxupah_bayar.tub_tanggal', 'ASC')
            ->findAll();

        foreach ($data as $row) {
            if ($row['tub_keterangan']) {
                $uraian = 'BAYAR BON ' . strtoupper($row['tk_nama']) . ' NOMOR ' . $row['tu_nomor'] . ' - ' . strtoupper($row['tub_keterangan']);
            } else {
                $uraian = 'BAYAR BON ' . strtoupper($row['tk_nama']) . ' NOMOR ' . $row['tu_nomor'];
            }

            $total =  $row['tub_tbon'] ? str_replace(',', '.', $row['tub_tbon']) : 0;
            $results[] = [
                'tanggal'   => $row['tub_tanggal'],
                'rek'       => $idRek,
                'nomor'     => $row['tub_nomor'],
                'uraian'    => $uraian,
                'debet'     => 0,
                'kredit'    => $total
            ];
        }

        return isset($results) ? $results : [];
    }

    public function getBayarSisaUpah($idRek) // kredit
    {
        $trxupahBayarModel = new \App\Models\TrxupahBayarModel();
        $data = $trxupahBayarModel->where('trxupah_bayar.tub_tsupahkredit', $idRek)
            ->join('trxupah', 'trxupah.tu_id = trxupah_bayar.tub_trxupah', 'left')
            ->join('tukang', 'tukang.tk_id = trxupah.tu_tukang', 'left')
            ->select('trxupah_bayar.*, trxupah.tu_nomor, tukang.tk_nama')
            ->orderBy('trxupah_bayar.tub_tanggal', 'ASC')
            ->findAll();

        foreach ($data as $row) {
            if ($row['tub_keterangan']) {
                $uraian = 'BAYAR UPAH ' . strtoupper($row['tk_nama']) . ' NOMOR ' . $row['tu_nomor'] . ' - ' . strtoupper($row['tub_keterangan']);
            } else {
                $uraian = 'BAYAR UPAH ' . strtoupper($row['tk_nama']) . ' NOMOR ' . $row['tu_nomor'];
            }

            $total =  $row['tub_tsupah'] ? str_replace(',', '.', $row['tub_tsupah']) : 0;
            $results[] = [
                'tanggal'   => $row['tub_tanggal'],
                'rek'       => $idRek,
                'nomor'     => $row['tub_nomor'],
                'uraian'    => $uraian,
                'debet'     => 0,
                'kredit'    => $total
            ];
        }

        return isset($results) ? $results : [];
    }

    public function getHutangSuplier($idRek, $jenis)
    {
        $hutangsuplierModel = new \App\Models\HutangsuplierModel();

        if ($jenis == 'debet') {
            $data = $hutangsuplierModel
                ->where('hutangsuplier.hs_debet', $idRek)
                ->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left')
                ->select('hutangsuplier.*, suplier.suplier_id, suplier.suplier_nama')
                ->orderBy('hutangsuplier.hs_tanggal', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $hutangsuplierModel
                ->where('hutangsuplier.hs_kredit', $idRek)
                ->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left')
                ->select('hutangsuplier.*, suplier.suplier_id, suplier.suplier_nama')
                ->orderBy('hutangsuplier.hs_tanggal', 'ASC')
                ->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $uraian = 'HUTANG SUPLIER ' . strtoupper($row['suplier_nama']) . ' NOMOR ' . $row['hs_nomor'];
                $total =  $row['hs_total'] ? str_replace(',', '.', $row['hs_total']) : 0;
                $results[] = [
                    'tanggal'   => $row['hs_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['hs_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $total : 0,
                    'kredit'    => $jenis == 'kredit' ? $total : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getBayarHutangSuplier($idRek, $jenis)
    {
        $hutangsuplierBayarModel = new \App\Models\HutangsuplierBayarModel();

        if ($jenis == 'debet') {
            $data = $hutangsuplierBayarModel->where('hutangsuplier_bayar.hb_debet', $idRek)
                ->join('hutangsuplier', 'hutangsuplier.hs_id = hutangsuplier_bayar.hb_hutangsuplier', 'left')
                ->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left')
                ->select('hutangsuplier_bayar.*, hutangsuplier.hs_nomor, suplier.suplier_nama')
                ->orderBy('hutangsuplier_bayar.hb_tanggal', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $hutangsuplierBayarModel->where('hutangsuplier_bayar.hb_kredit', $idRek)
                ->join('hutangsuplier', 'hutangsuplier.hs_id = hutangsuplier_bayar.hb_hutangsuplier', 'left')
                ->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left')
                ->select('hutangsuplier_bayar.*, hutangsuplier.hs_nomor, suplier.suplier_nama')
                ->orderBy('hutangsuplier_bayar.hb_tanggal', 'ASC')
                ->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['hb_keterangan']) {
                    $uraian = 'BAYAR HUTANG SUPLIER ' . strtoupper($row['suplier_nama']) . ' NOMOR ' . $row['hs_nomor'] . ' - ' . strtoupper($row['hb_keterangan']);
                } else {
                    $uraian = 'BAYAR HUTANG SUPLIER ' . strtoupper($row['suplier_nama']) . ' NOMOR ' . $row['hs_nomor'];
                }
                $total =  $row['hb_bayar'] ? str_replace(',', '.', $row['hb_bayar']) : 0;
                $results[] = [
                    'tanggal'   => $row['hb_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['hb_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $total : 0,
                    'kredit'    => $jenis == 'kredit' ? $total : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getOngkosKirim($idRek, $jenis)
    {
        $ongkirPembelianModel = new \App\Models\OngkirPembelianModel();

        if ($jenis == 'debet') {
            $data = $ongkirPembelianModel->where('ongkir_pembelian.op_debet', $idRek)
                ->join('hutangsuplier', 'hutangsuplier.hs_id = ongkir_pembelian.op_hutangsuplier', 'left')
                ->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left')
                ->select('ongkir_pembelian.*, hutangsuplier.hs_nomor, suplier.suplier_nama')
                ->orderBy('ongkir_pembelian.op_tanggal', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $ongkirPembelianModel->where('ongkir_pembelian.op_kredit', $idRek)
                ->join('hutangsuplier', 'hutangsuplier.hs_id = ongkir_pembelian.op_hutangsuplier', 'left')
                ->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left')
                ->select('ongkir_pembelian.*, hutangsuplier.hs_nomor, suplier.suplier_nama')
                ->orderBy('ongkir_pembelian.op_tanggal', 'ASC')
                ->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['op_keterangan']) {
                    $uraian = 'BAYAR ONGKOS KIRIM SUPLIER ' . strtoupper($row['suplier_nama']) . ' - ' . strtoupper($row['op_keterangan']);
                } else {
                    $uraian = 'BAYAR ONGKOS KIRIM SUPLIER ' . strtoupper($row['suplier_nama']);
                }
                $total =  $row['op_bayar'] ? str_replace(',', '.', $row['op_bayar']) : 0;
                $results[] = [
                    'tanggal'   => $row['op_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['op_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $total : 0,
                    'kredit'    => $jenis == 'kredit' ? $total : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getOperasional($idRek, $jenis)
    {
        $operasionalModel = new \App\Models\OperasionalModel();
        $kasKecilModel = new \App\Models\KasKecilModel();

        if ($jenis == 'debet') {
            $data = $operasionalModel->where('tl_debet', $idRek)
                ->orderBy('tl_tanggal', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $operasionalModel->where('tl_kredit', $idRek)
                ->orderBy('tl_tanggal', 'ASC')
                ->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['tl_jenis'] == 1) {
                    $jenisTrx = $kasKecilModel->getJenisKas(2);
                }
                if ($row['tl_jenis'] == 2) {
                    $jenisTrx = $kasKecilModel->getJenisKas(3);
                }
                if ($row['tl_keterangan']) {
                    $uraian = $jenisTrx['name'] . ' NOMOR ' . $row['tl_nomor'] . ' - ' . strtoupper($row['tl_keterangan']);
                } else {
                    $uraian = $jenisTrx['name'] . ' NOMOR ' . $row['tl_nomor'];
                }
                $total =  $row['tl_nominal'] ? str_replace(',', '.', $row['tl_nominal']) : 0;
                $results[] = [
                    'tanggal'   => $row['tl_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['tl_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $total : 0,
                    'kredit'    => $jenis == 'kredit' ? $total : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getGeneralLedger($idRek, $jenis)
    {
        $ledgerModel = new \App\Models\LedgerModel();

        if ($jenis == 'debet') {
            $data = $ledgerModel
                ->where('gl_debet', $idRek)
                ->orderBy('created_at', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $ledgerModel
                ->where('gl_kredit', $idRek)
                ->orderBy('created_at', 'ASC')
                ->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                $results[] = [
                    'tanggal'   => $row['created_at'],
                    'rek'       => $idRek,
                    'nomor'     => $row['gl_nomor'],
                    'uraian'    => $row['gl_uraian'],
                    'debet'     => $jenis == 'debet' ? $row['gl_nominalDebet'] : 0,
                    'kredit'    => $jenis == 'kredit' ? $row['gl_nominalKredit'] : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getBonLembur($idRek, $jenis)
    {
        $trxupahLainModel = new \App\Models\TrxupahLainModel();

        if ($jenis == 'debet') {
            $data = $trxupahLainModel
                ->where('trxupah_lain.tul_debet', $idRek)
                ->join('trxupah', 'trxupah.tu_id = trxupah_lain.tul_trxupah', 'left')
                ->join('tukang', 'tukang.tk_id = trxupah.tu_tukang', 'left')
                ->select('trxupah_lain.*, trxupah.tu_nomor, tukang.tk_nama')
                ->orderBy('trxupah_lain.tul_tanggal', 'ASC')
                ->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $trxupahLainModel
                ->where('trxupah_lain.tul_kredit', $idRek)
                ->join('trxupah', 'trxupah.tu_id = trxupah_lain.tul_trxupah', 'left')
                ->join('tukang', 'tukang.tk_id = trxupah.tu_tukang', 'left')
                ->select('trxupah_lain.*, trxupah.tu_nomor, tukang.tk_nama')
                ->orderBy('trxupah_lain.tul_tanggal', 'ASC')
                ->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['tul_jenis'] == 1) {
                    $jenistrx = 'KAS BON';
                } else {
                    $jenistrx = 'UPAH LEMBUR';
                }

                $total = $row['tul_nominal'] ? str_replace(',', '.', $row['tul_nominal']) : 0;
                $results[] = [
                    'tanggal'   => $row['tul_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['tu_nomor'],
                    'uraian'    => $jenistrx . ' ' . strtoupper($row['tk_nama']) . ($row['tul_keterangan'] ? ' - ' . $row['tul_keterangan'] : ''),
                    'debet'     => $jenis == 'debet' ? $total : 0,
                    'kredit'    => $jenis == 'kredit' ? $total : 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function getPuHargaTrx($idRek, $jenis)
    {
        $puModel = new \App\Models\PenjualanunitModel();

        if ($jenis == 'kredit') {
            $data = $puModel
                ->where([
                    'penjualan_unit.pu_hargaKredit' => $idRek,
                    'penjualan_unit.pu_status' => 1
                ])
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->select('penjualan_unit.*, customers.cust_nama')
                ->orderBy('penjualan_unit.created_at', 'ASC')->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if (($row['pu_harga'] ? $row['pu_harga'] : 0) > 0) {
                    $results[] = [
                        'tanggal'   => $row['created_at'],
                        'rek'       => $idRek,
                        'nomor'     => $row['pu_nomor'],
                        'uraian'    => 'PENJUALAN UNIT ' . $row['pu_nomor'] . ' A/N ' . $row['cust_nama'],
                        'debet'     => 0,
                        'kredit'    => ($row['pu_harga'] ? $row['pu_harga'] : 0) ? ($row['pu_harga'] ? $row['pu_harga'] : 0) : 0,
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function getPuBayar($idRek, $jenis)
    {
        $puModel = new \App\Models\PenjualanunitModel();

        if ($jenis == 'debet') {
            $data = $puModel
                ->where([
                    'penjualan_unit.pu_bayarDebet' => $idRek,
                    'penjualan_unit.pu_status' => 1
                ])
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->select('penjualan_unit.*, customers.cust_nama')
                ->orderBy('penjualan_unit.created_at', 'ASC')->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['pu_bayar'] > 0) {
                    $results[] = [
                        'tanggal'   => $row['created_at'],
                        'rek'       => $idRek,
                        'nomor'     => $row['pu_nomor'],
                        'uraian'    => 'UANG MUKA PENJUALAN UNIT ' . $row['pu_nomor'] . ' A/N ' . $row['cust_nama'],
                        'debet'     => $row['pu_bayar'] ? $row['pu_bayar'] : 0,
                        'kredit'    => 0,
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function getPuKpr($idRek, $jenis)
    {
        $puModel = new \App\Models\PenjualanunitModel();

        if ($jenis == 'debet') {
            $data = $puModel
                ->where([
                    'penjualan_unit.pu_kprDebet' => $idRek,
                    'penjualan_unit.pu_status' => 1
                ])
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->select('penjualan_unit.*, customers.cust_nama')
                ->orderBy('penjualan_unit.created_at', 'ASC')->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['pu_nominalKpr'] > 0) {
                    $results[] = [
                        'tanggal'   => $row['created_at'],
                        'rek'       => $idRek,
                        'nomor'     => $row['pu_nomor'],
                        'uraian'    => 'PENJUALAN UNIT KPR ' . $row['pu_nomor'] . ' A/N ' . $row['cust_nama'],
                        'debet'     => $row['pu_nominalKpr'] ? $row['pu_nominalKpr'] : 0,
                        'kredit'    => 0,
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function getPcBayar($idRek, $jenis)
    {
        $pcModel = new \App\Models\PiutangpenjualanModel();

        if ($jenis == 'debet') {
            $data = $pcModel
                ->join('penjualan_unit', 'penjualan_unit.pu_nomor = piutang_penjualan.pc_penjualan', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->where([
                    'piutang_penjualan.pc_bayarDebet' => $idRek,
                    'penjualan_unit.pu_status' => 1
                ])
                ->select('piutang_penjualan.*, customers.cust_nama')
                ->orderBy('piutang_penjualan.created_at', 'ASC')->findAll();
        }

        if ($jenis == 'kredit') {
            $data = $pcModel
                ->join('penjualan_unit', 'penjualan_unit.pu_nomor = piutang_penjualan.pc_penjualan', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->where([
                    'piutang_penjualan.pc_bayarKredit' => $idRek,
                    'penjualan_unit.pu_status' => 1
                ])
                ->select('piutang_penjualan.*, customers.cust_nama')
                ->orderBy('piutang_penjualan.created_at', 'ASC')->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['pc_bayar']) {
                    $results[] = [
                        'tanggal'   => $row['created_at'],
                        'rek'       => $idRek,
                        'nomor'     => $row['pc_notrx'],
                        'uraian'    => 'PIUTANG PENJUALAN UNIT ' . $row['pc_notrx'] . ' A/N ' . $row['cust_nama'],
                        'debet'     => $jenis == 'debet' ? ($row['pc_bayar'] ? abs($row['pc_bayar']) : 0) : 0,
                        'kredit'    => $jenis == 'kredit' ? ($row['pc_bayar'] ? abs($row['pc_bayar']) : 0) : 0,
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function getPcBayarKpr($idRek, $jenis)
    {
        $pcModel = new \App\Models\PiutangpenjualanModel();

        if ($jenis == 'debet') {
            $data = $pcModel
                ->join('penjualan_unit', 'penjualan_unit.pu_nomor = piutang_penjualan.pc_penjualan', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->where([
                    'piutang_penjualan.pc_bayarKprDebet' => $idRek,
                    'penjualan_unit.pu_status' => 1
                ])
                ->select('piutang_penjualan.*, customers.cust_nama')
                ->orderBy('piutang_penjualan.created_at', 'ASC')->findAll();
        }

        if ($jenis == 'kredit') {
            $data = $pcModel
                ->join('penjualan_unit', 'penjualan_unit.pu_nomor = piutang_penjualan.pc_penjualan', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->where([
                    'piutang_penjualan.pc_bayarKprKredit' => $idRek,
                    'penjualan_unit.pu_status' => 1
                ])
                ->select('piutang_penjualan.*, customers.cust_nama')
                ->orderBy('piutang_penjualan.created_at', 'ASC')->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['pc_bayarKpr'] > 0) {
                    $results[] = [
                        'tanggal'   => $row['created_at'],
                        'rek'       => $idRek,
                        'nomor'     => $row['pc_notrx'],
                        'uraian'    => 'PIUTANG PENJUALAN UNIT ' . $row['pc_notrx'] . ' A/N ' . $row['cust_nama'],
                        'debet'     => $jenis == 'debet' ? ($row['pc_bayarKpr'] ? $row['pc_bayarKpr'] : 0) : 0,
                        'kredit'    => $jenis == 'kredit' ? ($row['pc_bayarKpr'] ? $row['pc_bayarKpr'] : 0) : 0,
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function getPcBayarLebihan($idRek, $jenis)
    {
        $pcModel = new \App\Models\PiutangpenjualanModel();

        if ($jenis == 'debet') {
            $data = $pcModel
                ->join('penjualan_unit', 'penjualan_unit.pu_nomor = piutang_penjualan.pc_penjualan', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->where([
                    'piutang_penjualan.pc_debetLebih' => $idRek,
                    'penjualan_unit.pu_status' => 1
                ])
                ->select('piutang_penjualan.*, customers.cust_nama')
                ->orderBy('piutang_penjualan.created_at', 'ASC')->findAll();
        }

        if ($jenis == 'kredit') {
            $data = $pcModel
                ->join('penjualan_unit', 'penjualan_unit.pu_nomor = piutang_penjualan.pc_penjualan', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->where([
                    'piutang_penjualan.pc_kreditLebih' => $idRek,
                    'penjualan_unit.pu_status' => 1
                ])
                ->select('piutang_penjualan.*, customers.cust_nama')
                ->orderBy('piutang_penjualan.created_at', 'ASC')->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['pc_bayarLebih'] > 0) {
                    $results[] = [
                        'tanggal'   => $row['created_at'],
                        'rek'       => $idRek,
                        'nomor'     => $row['pc_notrx'],
                        'uraian'    => 'PENGEMBALIAN KELEBIHAN BAYAR PIUTANG PENJUALAN UNIT ' . $row['pc_notrx'] . ' A/N ' . $row['cust_nama'],
                        'debet'     => $jenis == 'debet' ? ($row['pc_bayarLebih'] ? $row['pc_bayarLebih'] : 0) : 0,
                        'kredit'    => $jenis == 'kredit' ? ($row['pc_bayarLebih'] ? $row['pc_bayarLebih'] : 0) : 0,
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function getPcSisa($idRek, $jenis)
    {
        $pcModel = new \App\Models\PiutangpenjualanModel();

        if ($jenis == 'debet') {
            $data = $pcModel
                ->join('penjualan_unit', 'penjualan_unit.pu_nomor = piutang_penjualan.pc_penjualan', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->where([
                    'piutang_penjualan.pc_sisaDebet' => $idRek,
                    'penjualan_unit.pu_status' => 1
                ])
                ->select('piutang_penjualan.*, customers.cust_nama')
                ->orderBy('piutang_penjualan.created_at', 'ASC')->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['pc_sisa']) {
                    $results[] = [
                        'tanggal'   => $row['created_at'],
                        'rek'       => $idRek,
                        'nomor'     => $row['pc_notrx'],
                        'uraian'    => 'PIUTANG PENJUALAN UNIT ' . $row['pc_notrx'] . ' A/N ' . $row['cust_nama'],
                        'debet'     => intval($row['pc_sisa']) > 0 ? abs($row['pc_sisa']) : 0,
                        'kredit'    => intval($row['pc_sisa']) < 0 ? abs($row['pc_sisa']) : 0,
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function getBp($idRek, $jenis)
    {
        $bpModel = new \App\Models\BiayapenjualanModel();

        if ($jenis == 'debet') {
            $data = $bpModel
                ->where('bp_debet', $idRek)->orderBy('created_at', 'ASC')->findAll();
        }

        if ($jenis == 'kredit') {
            $data = $bpModel
                ->where('bp_kredit', $idRek)->orderBy('created_at', 'ASC')->findAll();
        }

        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['bp_nominal'] > 0) {
                    $results[] = [
                        'tanggal'   => $row['created_at'],
                        'rek'       => $idRek,
                        'nomor'     => $row['bp_penjualan'],
                        'uraian'    => $row['bp_uraian'],
                        'debet'     => $jenis == 'debet' ? ($row['bp_nominal'] ? $row['bp_nominal'] : 0) : 0,
                        'kredit'    => $jenis == 'kredit' ? ($row['bp_nominal'] ? $row['bp_nominal'] : 0) : 0,
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function puHargaTrx($idRek, $jenis)
    {
        $puModel = new \App\Models\PenjualanunitModel();
        if ($jenis == 'kredit') {
            $data = $puModel->where(['penjualan_unit.pu_hargaKredit' => $idRek, 'pu_status' => 1]) // , 'pu_status' => 1
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->join('unit', 'unit.unit_id = penjualan_unit.pu_unit', 'left')
                ->select('penjualan_unit.*, customers.cust_nama, unit.unit_nama')
                ->orderBy('penjualan_unit.pu_tglTrx', 'ASC')->findAll();
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['pu_keterangan']) {
                    $uraian = 'PENJUALAN UNIT ' . $row['unit_nama'] . ' A/N ' . $row['cust_nama'] . ' - ' . strtoupper($row['pu_keterangan']);
                } else {
                    $uraian = 'PENJUALAN UNIT ' . $row['unit_nama'] . ' A/N ' . $row['cust_nama'];
                }
                $nominal = $row['pu_harga'] ? str_replace(',', '.', $row['pu_harga']) : 0;
                if ($nominal > 0) {
                    $results[] = [
                        'tanggal'   => $row['pu_tglTrx'],
                        'rek'       => $idRek,
                        'nomor'     => $row['pu_nomor'],
                        'uraian'    => $uraian,
                        'debet'     => 0,
                        'kredit'    => $jenis == 'kredit' ? $nominal : 0
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function puSisaPiutang($idRek, $jenis)
    {
        $puModel = new \App\Models\PenjualanunitModel();
        if ($jenis == 'debet') {
            $data = $puModel->where(['penjualan_unit.pu_sisaDebet' => $idRek, 'pu_status' => 1]) // , 'pu_status' => 1
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->join('unit', 'unit.unit_id = penjualan_unit.pu_unit', 'left')
                ->select('penjualan_unit.*, customers.cust_nama, unit.unit_nama')
                ->orderBy('penjualan_unit.pu_tglTrx', 'ASC')->findAll();
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['pu_keterangan']) {
                    $uraian = 'PIUTANG PENJUALAN UNIT ' . $row['unit_nama'] . ' A/N ' . $row['cust_nama'] . ' - ' . strtoupper($row['pu_keterangan']);
                } else {
                    $uraian = 'PIUTANG PENJUALAN UNIT ' . $row['unit_nama'] . ' A/N ' . $row['cust_nama'];
                }
                $nominal = $row['pu_sisa'] ? str_replace(',', '.', $row['pu_sisa']) : 0;
                $results[] = [
                    'tanggal'   => $row['pu_tglTrx'],
                    'rek'       => $idRek,
                    'nomor'     => $row['pu_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $nominal : 0,
                    'kredit'    => 0
                ];
            }
        }

        return isset($results) ? $results : [];
    }

    public function puPiutangKpr($idRek, $jenis)
    {
        $puModel = new \App\Models\PenjualanunitModel();
        if ($jenis == 'debet') {
            $data = $puModel->where(['penjualan_unit.pu_debetKpr' => $idRek, 'pu_status' => 1]) // , 'pu_status' => 1
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->join('unit', 'unit.unit_id = penjualan_unit.pu_unit', 'left')
                ->select('penjualan_unit.*, customers.cust_nama, unit.unit_nama')
                ->orderBy('penjualan_unit.pu_tglTrx', 'ASC')->findAll();
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['pu_keterangan']) {
                    $uraian = 'PENJUALAN UNIT KPR ' . $row['unit_nama'] . ' A/N ' . $row['cust_nama'] . ' - ' . strtoupper($row['pu_keterangan']);
                } else {
                    $uraian = 'PENJUALAN UNIT KPR ' . $row['unit_nama'] . ' A/N ' . $row['cust_nama'];
                }
                $nominal = $row['pu_nilaiAccKpr'] ? str_replace(',', '.', $row['pu_nilaiAccKpr']) : 0;
                if ($nominal > 0) {
                    $results[] = [
                        'tanggal'   => $row['pu_tglTrx'],
                        'rek'       => $idRek,
                        'nomor'     => $row['pu_nomor'],
                        'uraian'    => $uraian,
                        'debet'     => $jenis == 'debet' ? $nominal : 0,
                        'kredit'    => 0
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function puTagBayar($idRek, $jenis)
    {
        $tagModel = new \App\Models\TagihanPuModel();
        if ($jenis == 'debet') {
            $data = $tagModel->where('tagihanpu.tp_debet', $idRek)
                ->join('penjualan_unit', 'penjualan_unit.pu_id = tagihanpu.tp_pu', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->join('unit', 'unit.unit_id = penjualan_unit.pu_unit', 'left')
                ->select('tagihanpu.*, penjualan_unit.pu_nomor, customers.cust_nama, unit.unit_nama')
                ->orderBy('tagihanpu.tp_tglbayar', 'ASC')->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $tagModel->where('tagihanpu.tp_kredit', $idRek)
                ->join('penjualan_unit', 'penjualan_unit.pu_id = tagihanpu.tp_pu', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->join('unit', 'unit.unit_id = penjualan_unit.pu_unit', 'left')
                ->select('tagihanpu.*, penjualan_unit.pu_nomor, customers.cust_nama, unit.unit_nama')
                ->orderBy('tagihanpu.tp_tglbayar', 'ASC')->findAll();
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['tp_keterangan']) {
                    $uraian = 'PIUTANG PENJUALAN UNIT ' . $row['pu_nomor'] . ' A/N ' . $row['cust_nama'] . ' - ' . strtoupper($row['tp_keterangan']);
                } else {
                    $uraian = 'PIUTANG PENJUALAN UNIT ' . $row['pu_nomor'] . ' A/N ' . $row['cust_nama'];
                }
                $nominal = $row['tp_nilai'] ? str_replace(',', '.', $row['tp_nilai']) : 0;
                if ($nominal > 0) {
                    $results[] = [
                        'tanggal'   => $row['tp_tglbayar'],
                        'rek'       => $idRek,
                        'nomor'     => $row['tp_nomor'],
                        'uraian'    => $uraian,
                        'debet'     => $jenis == 'debet' ? $nominal : 0,
                        'kredit'    => $jenis == 'kredit' ? $nominal : 0,
                    ];
                }
            }
        }

        return isset($results) ? $results : [];
    }

    public function puBiayaLain($idRek, $jenis)
    {
        $bpModel = new \App\Models\BiayapenjualanModel();
        if ($jenis == 'debet') {
            $data = $bpModel->where('biaya_penjualan.bp_debet', $idRek)
                ->join('biayalain', 'biayalain.bl_id = biaya_penjualan.bp_biayalain', 'left')
                ->join('penjualan_unit', 'penjualan_unit.pu_id = biaya_penjualan.bp_penjualan', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->join('unit', 'unit.unit_id = penjualan_unit.pu_unit', 'left')
                ->select('biaya_penjualan.*, penjualan_unit.pu_nomor, biayalain.bl_nama, customers.cust_nama, unit.unit_nama')
                ->orderBy('biaya_penjualan.bp_tanggal', 'ASC')->findAll();
        }
        if ($jenis == 'kredit') {
            $data = $bpModel->where('biaya_penjualan.bp_kredit', $idRek)
                ->join('biayalain', 'biayalain.bl_id = biaya_penjualan.bp_biayalain', 'left')
                ->join('penjualan_unit', 'penjualan_unit.pu_id = biaya_penjualan.bp_penjualan', 'left')
                ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
                ->join('unit', 'unit.unit_id = penjualan_unit.pu_unit', 'left')
                ->select('biaya_penjualan.*, penjualan_unit.pu_nomor, biayalain.bl_nama, customers.cust_nama, unit.unit_nama')
                ->orderBy('biaya_penjualan.bp_tanggal', 'ASC')->findAll();
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($row['bp_uraian']) {
                    $uraian = $row['bl_nama'] . ' - PENJUALAN UNIT ' . $row['unit_nama'] . ' A/N ' . $row['cust_nama'] . ' - ' . strtoupper($row['bp_uraian']);
                } else {
                    $uraian = $row['bl_nama'] . ' - PENJUALAN UNIT ' . $row['unit_nama'] . ' A/N ' . $row['cust_nama'];
                }
                $nominal = $row['bp_nominal'] ? str_replace(',', '.', $row['bp_nominal']) : 0;
                $results[] = [
                    'tanggal'   => $row['bp_tanggal'],
                    'rek'       => $idRek,
                    'nomor'     => $row['pu_nomor'],
                    'uraian'    => $uraian,
                    'debet'     => $jenis == 'debet' ? $nominal : 0,
                    'kredit'    => $jenis == 'kredit' ? $nominal : 0,
                ];
            }
        }

        return isset($results) ? $results : [];
    }
}
