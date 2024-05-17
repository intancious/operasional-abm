<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgresModel extends Model
{

    public function getProgresUnit($idUnit)
    {
        $unitModel = new \App\Models\UnitModel();
        $rapModel = new \App\Models\RapsModel();
        $trxupahItemModel = new \App\Models\TrxupahItemModel();

        $unit = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->where('unit.unit_id', $idUnit)
            ->select('unit.*, types.type_nama')->first();
        $raps = $rapModel->where('rap_tipe', $unit['unit_tipe'])->findAll();

        $grandTotalVolumeRap = 0;
        $grandTotalVolumeKeluar = 0;
        $grandTotalNilaiRap = 0;
        $grandTotalNilaiKeluar = 0;
        foreach ($raps as $row) {
            $volumeRap = $row['rap_volume'] ? str_replace(',', '.', $row['rap_volume']) : 0;
            $nilaiRap = $row['rap_harga'] ? str_replace(',', '.', $row['rap_harga']) : 0;

            $subTotalRap = $volumeRap * $nilaiRap;
            $grandTotalVolumeRap += $volumeRap;
            $grandTotalNilaiRap += $subTotalRap;

            if ($row['rap_barang']) {
                // barang keluar
                $keluar = $this->getProgresKeluar($unit['unit_id'], $row['rap_barang']);
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

                $upah = $this->getProgresUpah($unit['unit_id'], $row['rap_upah']);
                $volumeKeluar = ($jumlah / $volumeRap);
                $nilaiKeluar = $nilai;
                $subTotalKeluar = $upah['totalUpah'];
            }
            $grandTotalVolumeKeluar += $volumeKeluar;
            $grandTotalNilaiKeluar += $subTotalKeluar;
        }

        $nilaiTanah = $unit['unit_nilaitanah'] ? str_replace(',', '.', $unit['unit_nilaitanah']) : 0;
        return [
            'totalVolumeRap'    => $grandTotalVolumeRap,
            'totalNilaiRap'     => $grandTotalNilaiRap,
            'totalVolumeKeluar' => $grandTotalVolumeKeluar,
            'totalNilaiKeluar'  => $grandTotalNilaiKeluar,
            'nilaiTanah'        => $nilaiTanah,
            'nilaiHpp'          => $grandTotalNilaiKeluar + $nilaiTanah,
            'nilaiProsentase'   => $grandTotalVolumeRap > 0 ? ($grandTotalVolumeKeluar / $grandTotalVolumeRap) * 100 : 0
        ];
    }

    public function getProgresKeluar($idUnit, $idBarang)
    {
        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
        // $barangModel = new \App\Models\BarangModel();

        $barangKeluar = $barangKeluarModel->where('bk_unit', $idUnit)->findAll();
        $totalVolumeKeluar = 0;
        $totalNilaiKeluar = 0;
        foreach ($barangKeluar as $row) {
            $items = $barangKeluarItemModel->where(['bki_barangkeluar' => $row['bk_id'], 'bki_barang' => $idBarang])->findAll();
            $volumeKeluar = 0;
            $nilaiKeluar = 0;
            foreach ($items as $item) {
                $qty = $item['bki_qty'] ? str_replace(',', '.', $item['bki_qty']) : 0;
                $harga = $item['bki_harga'] ? str_replace(',', '.', $item['bki_harga']) : 0;
                $volumeKeluar += $qty;
                $nilaiKeluar += $qty * $harga;
            }
            $totalVolumeKeluar += $volumeKeluar;
            $totalNilaiKeluar += $nilaiKeluar;
        }

        // $harga = $barangModel->rataRataHarga($idBarang);
        $harga = $totalNilaiKeluar > 0 ? $totalNilaiKeluar / $totalVolumeKeluar : 0;
        return [
            'volumeKeluar'  => $totalVolumeKeluar,
            'hargaKeluar'   => $totalNilaiKeluar > 0 ? $harga : 0,
            'totalNilai'    => $totalNilaiKeluar > 0 ? $totalNilaiKeluar : 0,
        ];
    }

    public function getProgresUpah($idUnit, $idUpah)
    {
        $trxupahItemModel = new \App\Models\TrxupahItemModel();
        $trxupahBayarModel = new \App\Models\TrxupahBayarModel();

        $trxupahitem = $trxupahItemModel->where(['tui_unit' => $idUnit, 'tui_upah' => $idUpah])->findAll();
        $totalUpah = 0;
        $idtrxupah = [];
        foreach ($trxupahitem as $row) {
            $jumlah = $row['tui_jumlah'] ? str_replace(',', '.', $row['tui_jumlah']) : 0;
            $nilai = $row['tui_nilai'] ? str_replace(',', '.', $row['tui_nilai']) : 0;
            $totalUpah += $jumlah * $nilai;

            $idtrxupah[] = $row['tui_trxupah'];
        }

        $totalBayar = 0;
        foreach (array_unique($idtrxupah) as $idtrxupah) {
            $bayarupah = $trxupahBayarModel->where('tub_trxupah', $idtrxupah)->findAll();
            $subtotal = 0;
            foreach ($bayarupah as $row) {
                $subtotal += $row['tub_bayar'] ? str_replace(',', '.', $row['tub_bayar']) : 0;
            }
            $totalBayar += $subtotal;
        }

        return [
            'totalUpah'  => $totalUpah - $totalBayar
        ];
    }

    public function getTransaksiUpah($idUnit, $idUpah)
    {
        $unitModel = new \App\Models\UnitModel();
        $rapModel = new \App\Models\RapsModel();
        $trxupahItemModel = new \App\Models\TrxupahItemModel();
        $unit = $unitModel->find($idUnit);

        $raps = $rapModel->where(['raps.rap_tipe' => $unit['unit_tipe'], 'raps.rap_upah' => $idUpah])
            ->join('types', 'types.type_id = raps.rap_tipe', 'left')
            ->select('raps.*, types.type_id, types.type_nama')
            ->orderBy('raps.rap_id', 'ASC')->findAll();

        $grandTotalVolumeRap = 0;
        $grandTotalNilaiRap = 0;
        $grandTotalVolumeKeluar = 0;
        $grandTotalNilaiKeluar = 0;
        foreach ($raps as $row) :
            if ($unit['unit_tipe'] != 6) {
                // rap
                $volumeRap = $row['rap_volume'] ? str_replace(',', '.', $row['rap_volume']) : 0;
                $nilaiRap = $row['rap_harga'] ? str_replace(',', '.', $row['rap_harga']) : 0;
                $subTotalRap = $volumeRap * $nilaiRap;
                $grandTotalVolumeRap += $volumeRap;
                $grandTotalNilaiRap += $subTotalRap;

                // upah
                $trxupah = $trxupahItemModel->where(['tui_unit' => $idUnit, 'tui_upah' => $idUpah])->findAll();
                $jumlah = 0;
                $nilai = 0;
                foreach ($trxupah as $trx) {
                    $jumlah += $trx['tui_jumlah'] ? str_replace(',', '.', $trx['tui_jumlah']) : 0;
                    $nilai += $trx['tui_nilai'] ? str_replace(',', '.', $trx['tui_nilai']) : 0;
                }
                $upah = $this->getProgresUpah($idUnit, $idUpah);
                $volumeKeluar = ($jumlah / $volumeRap);
                // $nilaiKeluar = $nilai;
                $subTotalKeluar = $upah['totalUpah'];

                $grandTotalVolumeKeluar += $volumeKeluar;
                $grandTotalNilaiKeluar += $subTotalKeluar;
            }
        endforeach;

        return [
            'totalVolumeRap'    => $grandTotalVolumeRap,
            'totalNilaiRap'     => $grandTotalNilaiRap,
            'totalVolumeKeluar' => $grandTotalVolumeKeluar,
            'totalNilaiKeluar'  => $grandTotalNilaiKeluar,
            'nilaiProsentase'   => $grandTotalVolumeKeluar > 0 ? ($grandTotalVolumeKeluar / $grandTotalVolumeRap) * 100 : 0
        ];
    }
}
