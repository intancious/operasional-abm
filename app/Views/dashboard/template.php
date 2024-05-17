<?php
$request = \Config\Services::request();
$settingModel = new \App\Models\SettingModel();
$setting = $settingModel->find(1);

$userModel = new \App\Models\UserModel();
$session = $userModel->find(session()->get('usr_id'));
$sesiBagian = session()->get('usr_bagian');
$bagianModel = new \App\Models\BagianModel();
$bagian = $bagianModel->find($sesiBagian);
$akses = explode(',', $bagian['bagian_akses']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title_bar; ?></title>
    <link href="/assets/img/<?= $setting['setting_favicon']; ?>" rel="icon">
    <link href="/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/ruang-admin.css" rel="stylesheet">

    <!-- cdn -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" integrity="sha512-ARJR74swou2y0Q2V9k0GbzQ/5vJ2RBSoCWokg4zkfM29Fb3vZEQyv0iWBMW/yvKgyHSR/7D64pFMmU8nYmbRkg==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.0/css/dataTables.bootstrap4.min.css">
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .modalLoader {
            height: 100%;
            width: 100%;
            background: rgba(255, 255, 255, .8) url('https://i.stack.imgur.com/FhHRx.gif') 50% 50% no-repeat;
            opacity: 0.80;
            -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=80);
            filter: alpha(opacity=80)
        }
    </style>
    <?php if (session()->get('logged_in')) { ?>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQ2OuVJG-uwrIIrUs0kpw_VTcCGRl0OtU&callback=" type="text/javascript"></script>
    <?php } ?>
</head>

<body id="page-top">
    <?php if (session()->get('logged_in')) { ?>
        <div id="wrapper">
            <ul class="navbar-nav sidebar sidebar-light accordion font-weight-bold" id="accordionSidebar">
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/dashboard">
                    <!-- <div class="sidebar-brand-icon">
                        <img src="/assets/img/<?= $setting['setting_logo2']; ?>">
                    </div> -->
                    <div class="sidebar-brand-text mx-3 text-uppercase small font-weight-bold"><?= $setting['setting_nama']; ?></div>
                </a>
                <hr class="sidebar-divider my-0">
                <li class="nav-item <?= $request->uri->getSegment(2) == '' || $request->uri->getSegment(2) == NULL ? 'active' : ''; ?>">
                    <a class="nav-link" href="/dashboard">
                        <i class="fas fa-sm fa-fw fa-home"></i>
                        <span>Dashboard</span></a>
                </li>
                <?php if (
                    in_array("rap", $akses) ||
                    in_array("masterAbsensi", $akses) ||
                    in_array("users", $akses) ||
                    in_array("bagian", $akses) ||
                    in_array("satuan", $akses) ||
                    in_array("kategoriBarang", $akses) ||
                    in_array("barang", $akses) ||
                    in_array("tukang", $akses) ||
                    in_array("upah", $akses) ||
                    in_array("assets", $akses) ||
                    in_array("unit", $akses) ||
                    in_array("tipeUnit", $akses) ||
                    in_array("suplier", $akses) ||
                    in_array("customer", $akses) ||
                    in_array("kpr", $akses) ||
                    in_array("kaskecil", $akses) ||
                    in_array("marketing", $akses) ||
                    in_array("biayalain", $akses) ||
                    in_array("rekening", $akses)
                ) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'rap' ||
                                            $request->uri->getSegment(2) == 'users' ||
                                            $request->uri->getSegment(2) == 'jamkerja' ||
                                            $request->uri->getSegment(2) == 'punishment' ||
                                            $request->uri->getSegment(2) == 'bagian' ||
                                            $request->uri->getSegment(2) == 'satuan' ||
                                            $request->uri->getSegment(2) == 'kategoriBarang' ||
                                            $request->uri->getSegment(2) == 'barang' ||
                                            $request->uri->getSegment(2) == 'tukang' ||
                                            $request->uri->getSegment(2) == 'upah' ||
                                            $request->uri->getSegment(2) == 'assets' ||
                                            $request->uri->getSegment(2) == 'unit' ||
                                            $request->uri->getSegment(2) == 'tipeUnit' ||
                                            $request->uri->getSegment(2) == 'suplier' ||
                                            $request->uri->getSegment(2) == 'customer' ||
                                            $request->uri->getSegment(2) == 'kpr' ||
                                            $request->uri->getSegment(2) == 'kaskecil' ||
                                            $request->uri->getSegment(2) == 'biayalain' ||
                                            $request->uri->getSegment(2) == 'marketing' ||
                                            $request->uri->getSegment(2) == 'rekening' ? 'active' : ''; ?>">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="true" aria-controls="collapseMaster">
                            <i class="fas fa-sm fa-fw fa-database"></i>
                            <span>Master Data</span>
                        </a>
                        <div id="collapseMaster" class="collapse <?= $request->uri->getSegment(2) == 'rap' ||
                                                                        $request->uri->getSegment(2) == 'users' ||
                                                                        $request->uri->getSegment(2) == 'jamkerja' ||
                                                                        $request->uri->getSegment(2) == 'punishment' ||
                                                                        $request->uri->getSegment(2) == 'bagian' ||
                                                                        $request->uri->getSegment(2) == 'satuan' ||
                                                                        $request->uri->getSegment(2) == 'kategoriBarang' ||
                                                                        $request->uri->getSegment(2) == 'barang' ||
                                                                        $request->uri->getSegment(2) == 'tukang' ||
                                                                        $request->uri->getSegment(2) == 'upah' ||
                                                                        $request->uri->getSegment(2) == 'assets' ||
                                                                        $request->uri->getSegment(2) == 'unit' ||
                                                                        $request->uri->getSegment(2) == 'tipeUnit' ||
                                                                        $request->uri->getSegment(2) == 'suplier' ||
                                                                        $request->uri->getSegment(2) == 'customer' ||
                                                                        $request->uri->getSegment(2) == 'kpr' ||
                                                                        $request->uri->getSegment(2) == 'kaskecil' ||
                                                                        $request->uri->getSegment(2) == 'biayalain' ||
                                                                        $request->uri->getSegment(2) == 'marketing' ||
                                                                        $request->uri->getSegment(2) == 'rekening' ? 'show' : ''; ?>" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <?php if (in_array("users", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/users">User</a>
                                <?php } ?>
                                <?php if (in_array("masterAbsensi", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/jamkerja">Jam Kerja</a>
                                <?php } ?>
                                <?php if (in_array("masterAbsensi", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/punishment">Punishment</a>
                                <?php } ?>
                                <?php if (in_array("bagian", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/bagian">Bagian</a>
                                <?php } ?>
                                <?php if (in_array("rekening", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/rekening">Rekening</a>
                                <?php } ?>
                                <?php if (in_array("kaskecil", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/kaskecil">Kas Kecil</a>
                                <?php } ?>
                                <?php if (in_array("kategoriBarang", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/kategoriBarang">Kategori Barang</a>
                                <?php } ?>
                                <?php if (in_array("satuan", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/satuan">Satuan Barang</a>
                                <?php } ?>
                                <?php if (in_array("barang", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/barang">Material Gudang</a>
                                <?php } ?>
                                <?php if (in_array("tukang", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/tukang">Tukang</a>
                                <?php } ?>
                                <?php if (in_array("upah", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/upah">Data Upah</a>
                                <?php } ?>
                                <?php if (in_array("jenisAsset", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/jenisAsset">Jenis Asset</a>
                                <?php } ?>
                                <?php if (in_array("unit", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/unit">Unit</a>
                                <?php } ?>
                                <?php if (in_array("tipeUnit", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/tipeUnit">Tipe Unit</a>
                                <?php } ?>
                                <?php if (in_array("rap", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/rap">RAP</a>
                                <?php } ?>
                                <?php if (in_array("suplier", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/suplier">Suplier</a>
                                <?php } ?>
                                <?php if (in_array("customer", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/customer">Customer</a>
                                <?php } ?>
                                <?php if (in_array("marketing", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/marketing">Marketing</a>
                                <?php } ?>
                                <?php if (in_array("kpr", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/kpr">KPR</a>
                                <?php } ?>
                                <?php if (in_array("biayalain", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/biayalain">Biaya Lain</a>
                                <?php } ?>

                                <?php if (in_array("masterAbsensi", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/whatsapp">Status & Scan WA</a>
                                <?php } ?>
                                <?php if (in_array("masterAbsensi", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/waHistory">History WA</a>
                                <?php } ?>
                            </div>
                        </div>
                    </li>
                <?php } ?>

                <?php if (
                    in_array("kehadiran", $akses)
                ) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'kehadiran' ||
                                            $request->uri->getSegment(2) == 'checkin' ||
                                            $request->uri->getSegment(2) == 'checkout' ||
                                            $request->uri->getSegment(2) == 'absen' ? 'active' : ''; ?>">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAbsensi" aria-expanded="true" aria-controls="collapseAbsensi">
                            <i class="fas fa-sm fa-fw fa-list-alt"></i>
                            <span>Kehadiran</span>
                        </a>
                        <div id="collapseAbsensi" class="collapse <?= $request->uri->getSegment(2) == 'kehadiran' ||
                                                                        $request->uri->getSegment(2) == 'checkin' ||
                                                                        $request->uri->getSegment(2) == 'checkout' ||
                                                                        $request->uri->getSegment(2) == 'absen' ? 'show' : ''; ?>" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <a class="collapse-item" href="/dashboard/kehadiran">Rekap Kehadiran</a>
                                <a class="collapse-item" href="/dashboard/checkin">Check-In</a>
                                <a class="collapse-item" href="/dashboard/checkout">Check-Out</a>
                                <a class="collapse-item" href="/dashboard/absen">Absen</a>
                            </div>
                        </div>
                    </li>
                <?php } ?>

                <?php if (
                    in_array("pembelian", $akses) ||
                    in_array("addPembelian", $akses) ||
                    in_array("editPembelian", $akses)
                ) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'pembelian' ||
                                            $request->uri->getSegment(2) == 'addPembelian' ||
                                            $request->uri->getSegment(2) == 'editPembelian' ||
                                            $request->uri->getSegment(2) == 'rewards' ? 'active' : ''; ?>">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePembelian" aria-expanded="true" aria-controls="collapsePembelian">
                            <i class="fas fa-sm fa-fw fa-boxes"></i>
                            <span>Pengajuan Barang</span>
                        </a>
                        <div id="collapsePembelian" class="collapse <?= $request->uri->getSegment(2) == 'pembelian' ||
                                                                        $request->uri->getSegment(2) == 'addPembelian' ||
                                                                        $request->uri->getSegment(2) == 'editPembelian' ||
                                                                        $request->uri->getSegment(2) == 'rewards' ? 'show' : ''; ?>" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <a class="collapse-item" href="/dashboard/pembelian">Pengajuan Barang</a>
                                <a class="collapse-item" href="/dashboard/rewards">Reward Pengajuan</a>
                            </div>
                        </div>
                    </li>
                <?php } ?>

                <?php if (in_array("barangkeluar", $akses)) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'barangkeluar' ? 'active' : ''; ?>">
                        <a class="nav-link" href="/dashboard/barangkeluar">
                            <i class="fas fa-sm fa-fw fa-truck-loading"></i>
                            <span>Barang Keluar</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if (in_array("ubk", $akses)) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'ubk' ? 'active' : ''; ?>">
                        <a class="nav-link" href="/dashboard/ubk">
                            <i class="fas fa-sm fa-fw fa-people-carry"></i>
                            <span>Transaksi Upah</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if (
                    in_array("ongkoskirim", $akses) ||
                    in_array("addOngkir", $akses) ||
                    in_array("editOngkir", $akses) ||
                    in_array("operasional", $akses) ||
                    in_array("addOperasional", $akses) ||
                    in_array("editOperasional", $akses) ||
                    in_array("kasbon", $akses) ||
                    in_array("addKasbon", $akses) ||
                    in_array("editKasbon", $akses)
                ) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'ongkoskirim' ||
                                            $request->uri->getSegment(2) == 'addOngkir' ||
                                            $request->uri->getSegment(2) == 'editOngkir' ||
                                            $request->uri->getSegment(2) == 'operasional' ||
                                            $request->uri->getSegment(2) == 'addOperasional' ||
                                            $request->uri->getSegment(2) == 'editOperasional' ||
                                            $request->uri->getSegment(2) == 'kasbon' ||
                                            $request->uri->getSegment(2) == 'addKasbon' ||
                                            $request->uri->getSegment(2) == 'editKasbon' ? 'active' : ''; ?>">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTransaksi" aria-expanded="true" aria-controls="collapseTransaksi">
                            <i class="fas fa-sm fa-fw fa-random"></i>
                            <span>Transaksi Lain</span>
                        </a>
                        <div id="collapseTransaksi" class="collapse <?= $request->uri->getSegment(2) == 'ongkoskirim' ||
                                                                        $request->uri->getSegment(2) == 'addOngkir' ||
                                                                        $request->uri->getSegment(2) == 'editOngkir' ||
                                                                        $request->uri->getSegment(2) == 'operasional' ||
                                                                        $request->uri->getSegment(2) == 'addOperasional' ||
                                                                        $request->uri->getSegment(2) == 'editOperasional' ||
                                                                        $request->uri->getSegment(2) == 'kasbon' ||
                                                                        $request->uri->getSegment(2) == 'addKasbon' ||
                                                                        $request->uri->getSegment(2) == 'editKasbon' ? 'show' : ''; ?>" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <?php if (in_array("ongkoskirim", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/ongkoskirim">Bayar Ongkos Kirim</a>
                                <?php } ?>
                                <?php if (in_array("operasional", $akses)) { ?>
                                    <a class="collapse-item" href="/dashboard/operasional">Transaksi Operasional</a>
                                <?php } ?>
                            </div>
                        </div>
                    </li>
                <?php } ?>

                <?php if (in_array("hutangsuplier", $akses)) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'hutangsuplier' ||
                                            $request->uri->getSegment(2) == 'jatuhtempo' ? 'active' : ''; ?>">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseHs" aria-expanded="true" aria-controls="collapseHs">
                            <i class="fas fa-sm fa-fw fa-file-invoice"></i>
                            <span>Hutang Suplier</span>
                        </a>
                        <div id="collapseHs" class="collapse <?= $request->uri->getSegment(2) == 'hutangsuplier' ||
                                                                    $request->uri->getSegment(2) == 'jatuhtempo' ? 'show' : ''; ?>" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <a class="collapse-item" href="/dashboard/hutangsuplier">Data Hutang Suplier</a>
                                <a class="collapse-item" href="/dashboard/jatuhtempo">Jatuh Tempo Terdekat</a>
                            </div>
                        </div>
                    </li>
                <?php } ?>

                <?php if (in_array("penjualanunit", $akses)) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'penjualanunit' ? 'active' : ''; ?>">
                        <a class="nav-link" href="/dashboard/penjualanunit">
                            <i class="fas fa-sm fa-fw fa-store-alt"></i>
                            <span>Penjualan Unit</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'piutanglist' ? 'active' : ''; ?>">
                        <a class="nav-link" href="/dashboard/piutanglist">
                            <i class="fas fa-sm fa-fw fa-file-invoice"></i>
                            <span>Piutang Penjualan</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if (in_array("ledger", $akses)) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'ledger' ? 'active' : ''; ?>">
                        <a class="nav-link" href="/dashboard/ledger">
                            <i class="fas fa-sm fa-fw fa-clipboard-list"></i>
                            <span>General Ledger</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if (in_array("estatem", $akses)) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'estatem' ? 'active' : ''; ?>">
                        <a class="nav-link" href="/dashboard/estatem">
                            <i class="fas fa-sm fa-fw fa-wallet"></i>
                            <span>Estate Management</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if (
                    in_array("rekapmaterial", $akses) ||
                    in_array("laporanbarang", $akses) ||
                    in_array("laporanprogresunit", $akses)
                ) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'rekapmaterial' ||
                                            $request->uri->getSegment(2) == 'laporanbarang' ||
                                            $request->uri->getSegment(2) == 'laporanprogresunit' ||
                                            $request->uri->getSegment(2) == 'laporandetailprogres' ? 'active' : ''; ?>">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan" aria-expanded="true" aria-controls="collapseLaporan">
                            <i class="fas fa-sm fa-fw fa-chart-bar"></i>
                            <span>Laporan</span>
                        </a>
                        <div id="collapseLaporan" class="collapse <?= $request->uri->getSegment(2) == 'rekapmaterial' ||
                                                                        $request->uri->getSegment(2) == 'laporanbarang' ||
                                                                        $request->uri->getSegment(2) == 'laporanprogresunit' ||
                                                                        $request->uri->getSegment(2) == 'laporandetailprogres' ? 'show' : ''; ?>" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <a class="collapse-item" href="/dashboard/rekapmaterial">Rekap Material</a>
                                <a class="collapse-item" href="/dashboard/laporanbarang">Persediaan Material</a>
                                <a class="collapse-item" href="/dashboard/laporanprogresunit">Progres Unit</a>
                            </div>
                        </div>
                    </li>
                <?php } ?>

                <?php if (in_array("bukubesar", $akses)) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'bukubesar' ? 'active' : ''; ?>">
                        <a class="nav-link" href="/dashboard/bukubesar">
                            <i class="fas fa-sm fa-fw fa-book-open"></i>
                            <span>Buku Besar</span>
                        </a>
                    </li>
                <?php } ?>

                <?php if (in_array("neracaSaldo", $akses)) { ?>
                    <li class="nav-item <?= $request->uri->getSegment(2) == 'neracaSaldo' ? 'active' : ''; ?>">
                        <a class="nav-link" href="/dashboard/neracaSaldo">
                            <i class="fas fa-sm fa-fw fa-balance-scale"></i>
                            <span>Neraca Saldo</span>
                        </a>
                    </li>
                <?php } ?>

                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0);" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sm fa-fw fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </a>
                </li>
            </ul>

            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
                        <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img class="img-profile rounded-circle" src="<?= $session['usr_photo'] ? '/assets/img/' . $session['usr_photo'] : '/assets/img/boy.png'; ?>" style="max-width: 60px">
                                    <span class="ml-2 d-none d-lg-inline text-white small"><?= $session['usr_nama']; ?></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="/dashboard/profile">
                                        <i class="fas fa-sm fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Profil
                                    </a>
                                    <?php if (in_array("pengaturan", $akses)) { ?>
                                        <a class="dropdown-item" href="/dashboard/pengaturan">
                                            <i class="fas fa-sm fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Pengaturan
                                        </a>
                                    <?php } ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#logoutModal">
                                        <i class="fas fa-sm fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Keluar
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>

                    <div class="container-fluid" id="container-wrapper">
                    <?php } ?>

                    <?= $this->renderSection('content'); ?>

                    <?php if (session()->get('logged_in')) { ?>
                        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelLogout" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-uppercase" id="exampleModalLabelLogout">Keluar dari Aplikasi</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Yakin ingin melanjutkan?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Tidak</button>
                                        <a href="/auth/logout" class="btn btn-primary">Ya</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>
                                &copy; <?= date('Y'); ?> - <?= strtoupper($setting['setting_nama']); ?>
                            </span>
                        </div>
                    </div>
                </footer>

            </div>
        </div>

        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-sm fa-angle-up"></i>
        </a>
    <?php } ?>

    <script src="/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/assets/js/ruang-admin.min.js"></script>
    <script src="/assets/vendor/chart.js/Chart.min.js"></script>

    <!-- cdn -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js" integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <script src="https://cdn.datatables.net/1.11.0/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.11.0/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
    <!-- <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jq-3.6.0/dt-1.11.4/fh-3.2.1/datatables.min.js"></script> -->

    <script src="https://unpkg.com/cart-localstorage@1.1.4/dist/cart-localstorage.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.plugins.min.js"></script>

    <script>
        $('.pagin').on('change', function() {
            window.location = $(this).val();
        });

        $(document).ready(function() {
            $('#customTable').DataTable({
                "lengthChange": false,
                "paging": false,
                "bInfo": false
            });

            $('#customFixedTable').DataTable({
                "lengthChange": false,
                "paging": false,
                "bInfo": false,
                "scrollY": "728px",
                "scrollX": true,
                "scrollCollapse": true,
                "fixedColumns": true
            });

            $('#customFixedTable2').DataTable({
                "bSort": false,
                "lengthChange": false,
                "paging": false,
                "bInfo": false,
                "scrollY": "728px",
                "scrollX": true,
                "scrollCollapse": true,
                "fixedColumns": true
            });

            $('#customTable2').DataTable({
                "fixedHeader": true,
                "lengthChange": false,
                "paging": false,
                "bInfo": false
            });
        });

        $('#tanggalSelect').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });

        $('#tanggalNup').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });

        $('#tanggalPicker').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });

        $('#tanggalTrx').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });

        $('#tanggalUpah').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });

        $('#tanggalLain').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });

        $('#tanggalTrxEdit').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });

        $('#startDate').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });

        $('#endDate').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });

        $('#tglPengajuanKpr').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });
        $('#tglAccKpr').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });
        $('#tglRealisasiKpr').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });
        $('#tagJthTempo').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });
        $('#tgljthtempo').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });
        $('#tglbayar').datepicker({
            uiLibrary: 'bootstrap4',
            // iconsLibrary: 'fontawesome',
            format: 'dd-mm-yyyy'
        });

        $('#tagbayar').val(formatRupiah($('#tagbayar').val()));
        $('#tagbayar').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#tagnilai').val(formatRupiah($('#tagnilai').val()));
        $('#tagnilai').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#tagNominal').val(formatRupiah($('#tagNominal').val()));
        $('#tagNominal').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#hargariil').val(formatRupiah($('#hargariil').val()));
        $('#hargariil').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#ttlharga').val(formatRupiah($('#ttlharga').val()));
        $('#ttlharga').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#tanahLebih').val(formatRupiah($('#tanahLebih').val()));
        $('#tanahLebih').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nnup').val(formatRupiah($('#nnup').val()));
        $('#nnup').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#mutu').val(formatRupiah($('#mutu').val()));
        $('#mutu').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#sbum').val(formatRupiah($('#sbum').val()));
        $('#sbum').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#ajbn').val(formatRupiah($('#ajbn').val()));
        $('#ajbn').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#pph').val(formatRupiah($('#pph').val()));
        $('#pph').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#bphtb').val(formatRupiah($('#bphtb').val()));
        $('#bphtb').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#realisasi').val(formatRupiah($('#realisasi').val()));
        $('#realisasi').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#shm').val(formatRupiah($('#shm').val()));
        $('#shm').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#kanopi').val(formatRupiah($('#kanopi').val()));
        $('#kanopi').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#tandon').val(formatRupiah($('#tandon').val()));
        $('#tandon').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#pompair').val(formatRupiah($('#pompair').val()));
        $('#pompair').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#teralis').val(formatRupiah($('#teralis').val()));
        $('#teralis').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#tembok').val(formatRupiah($('#tembok').val()));
        $('#tembok').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#pondasi').val(formatRupiah($('#pondasi').val()));
        $('#pondasi').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#pijb').val(formatRupiah($('#pijb').val()));
        $('#pijb').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#ppn').val(formatRupiah($('#ppn').val()));
        $('#ppn').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });
        $('#fee').val(formatRupiah($('#fee').val()));
        $('#fee').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nilaiPengajuanKpr').val(formatRupiah($('#nilaiPengajuanKpr').val()));
        $('#nilaiPengajuanKpr').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nilaiAccKpr').val(formatRupiah($('#nilaiAccKpr').val()));
        $('#nilaiAccKpr').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nominaldebet').val(formatRupiah($('#nominaldebet').val()));
        $('#nominaldebet').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nominal_lain').val(formatRupiah($('#nominal_lain').val()));
        $('#nominal_lain').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#sisaBayar').val(formatRupiah($('#sisaBayar').val()));
        $('#sisaBayar').keyup(function() {
            $('#sisaBayar').val(formatRupiah($(this).val()));
        });

        $('#bayarCustomer').val(formatRupiah($('#bayarCustomer').val()));
        $('#bayarCustomer').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#piutangCustomer').val(formatRupiah($('#piutangCustomer').val()));
        $('#piutangCustomer').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nominalKpr').val(formatRupiah($('#nominalKpr').val()));
        $('#nominalKpr').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#sisaPiutangKpr').val(formatRupiah($('#sisaPiutangKpr').val()));
        $('#sisaPiutangKpr').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#piutangKpr').val(formatRupiah($('#piutangKpr').val()));
        $('#piutangKpr').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#bayarKpr').val(formatRupiah($('#bayarKpr').val()));
        $('#bayarKpr').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#piutang_kpr').val(formatRupiah($('#piutang_kpr').val()));
        $('#piutang_kpr').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#hargaOut').val(formatRupiah($('#hargaOut').val()));
        $('#hargaOut').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#potongan').val(formatRupiah($('#potongan').val()));
        $(document).on('keyup', '#potongan', function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nilaitanah').val(formatRupiah($('#nilaitanah').val()));
        $('#nilaitanah').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nominalkredit').val(formatRupiah($('#nominalkredit').val()));
        $('#nominalkredit').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#harga').val(formatRupiah($('#harga').val()));
        $('#harga').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#qtyP').val(formatRupiah($('#qtyP').val()));
        $('#qtyP').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#qtyUpah').val(formatRupiah($('#qtyUpah').val()));
        $('#qtyUpah').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nilaiUbk').val(formatRupiah($('#nilaiUbk').val()));
        $('#nilaiUbk').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nilaiUbk').val(formatRupiah($('#nilaiUbk').val()));
        $('#nilaiUbk').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#total').val(formatRupiah($('#total').val()));
        $('#total').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#qtyDatang').val(formatRupiah($('#qtyDatang').val()));
        $('#qtyDatang').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#hargaBarang').val(formatRupiah($('#hargaBarang').val()));
        $('#hargaBarang').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nominal').val(formatRupiah($('#nominal').val()));
        $('#nominal').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#hargaStok').val(formatRupiah($('#hargaStok').val()));
        $('#hargaStok').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#jumlah').val(formatRupiah($('#jumlah').val()));
        $('#jumlah').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#kembali').val(formatRupiah($('#kembali').val()));
        $('#kembali').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#ongkir').val(formatRupiah($('#ongkir').val()));
        $('#ongkir').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#grandTotalAkhir').val(formatRupiah($('#grandTotalAkhir').val()));
        $('#grandTotalAkhir').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#grandTotalAkhir2').val(formatRupiah($('#grandTotalAkhir2').val()));
        $('#grandTotalAkhir2').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nilai').val(formatRupiah($('#nilai').val()));
        $('#nilai').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#ubkDibayar').val(formatRupiah($('#ubkDibayar').val()));
        $('#ubkDibayar').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#ubkSisa').val(formatRupiah($('#ubkSisa').val()));
        $('#ubkSisa').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#bayar').val(formatRupiah($('#bayar').val()));
        $('#bayar').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#bayarEdit').val(formatRupiah($('#bayarEdit').val()));
        $('#bayarEdit').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#newBayar').val(formatRupiah($('#newBayar').val()));
        $('#newBayar').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('#nominalLain').val(formatRupiah($('#nominalLain').val()));
        $('#nominalLain').keyup(function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('.addTimeWork').on('click', function() {
            $('#timeworkModalLabel').html('Tambah Data');
            $('.modal form').attr('action', '/dashboard/insertTime');
            $('.modal-footer button[type=submit]').html('Simpan');
            $('#id').val('');
            $('#mulai').val('');
            $('#selesai').val('');
        });

        $(document).on('click', '.absenKantor', function() {
            const id = $(this).data('id');
            const tgl = $(this).data('tanggal');
            $.ajax({
                url: '/dashboard/getDetailCheckInOut/' + id + '/' + tgl,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    if (response[0].ci_telat == 'yes') {
                        $('#telat').show();
                        $('#terlambat').val('>= ' + response[0].pun_waktu + ' Menit');
                        $('#punishment').val('Rp ' + formatRupiah(response[0].pun_potongan));
                    } else {
                        $('#telat').hide();
                    }
                    $('#detailAbsenKantorLabel').html(response[0].usr_nama);
                    $('#tanggalKantor').val(response[0].created_at);
                    $('#keteranganKantor').val(response[0].ci_deskripsi);
                    $('#tanggalOutKantor').val(response[1] ? response[1].created_at : '');
                    $('#keteranganOutKantor').val(response[1] ? response[1].ci_deskripsi : '');
                }
            });
        });

        $(document).on('click', '.editTimeWork', function() {
            const id = $(this).data('id');
            $('#timeworkModalLabel').html('Perbarui Data');
            $('.modal form').attr('action', '/dashboard/updateTime');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/jamKerjaJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.jk_id);
                    $('#mulai').val(response.jk_mulai);
                    $('#selesai').val(response.jk_selesai);
                }
            });
        });

        $('.addPunishment').on('click', function() {
            $('#punishmentModalLabel').html('Tambah Data');
            $('.modal form').attr('action', '/dashboard/insertPunishment');
            $('.modal-footer button[type=submit]').html('Simpan');
            $('#id').val('');
            $("#user option[value='']").prop("selected", true);
            $('#user').selectpicker('refresh');
            $('#punishment').val('');
            $('#waktu').val('');
            $('#potongan').val('');
            $('#keterangan').val('');
        });

        $(document).on('click', '.editPunishment', function() {
            const id = $(this).data('id');
            $('#punishmentModalLabel').html('Perbarui Data');
            $('.modal form').attr('action', '/dashboard/updatePunishment');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/punishmentJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.pun_id);
                    $("#user option[value='" + response.pun_user + "']").prop("selected", true);
                    $('#user').selectpicker('refresh');
                    $('#punishment').val(response.pun_nama);
                    $('#waktu').val(response.pun_waktu);
                    $('#potongan').val(formatRupiah(response.pun_potongan));
                    $('#keterangan').val(response.pun_deskripsi);
                }
            });
        });

        if ($('#transaksi').val()) {
            if ($('#transaksi').val() == '1') {
                $("#kredit option[value='654']").prop("selected", true);
                $('#kredit').selectpicker('refresh');
            } else
            if ($('#transaksi').val() == '2') {
                $("#kredit option[value='7']").prop("selected", true);
                $('#kredit').selectpicker('refresh');
            } else {
                $("#kredit option[value='']").prop("selected", true);
                $('#kredit').selectpicker('refresh');
            }
        }

        $('.addBiayalain').on('click', function() {
            $('#biayaLainModalLabel').html('Biaya Lain Baru');
            $('.modal form').attr('action', '/dashboard/insertBiayalain');
            $('.modal-footer button[type=submit]').html('Simpan');
            $('#id').val('');
            $('#nama').val('');
        });

        $(document).on('click', '.editBiayalain', function() {
            const id = $(this).data('id');
            $('#biayaLainModalLabel').html('Perbarui Biaya Lain');
            $('.modal form').attr('action', '/dashboard/updateBiayalain');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/biayalainJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.bl_id);
                    $('#nama').val(response.bl_nama);
                }
            });
        });

        $('.addMarketing').on('click', function() {
            $('#marketingModalLabel').html('Marketing Baru');
            $('.modal form').attr('action', '/dashboard/insertMarketing');
            $('.modal-footer button[type=submit]').html('Simpan');
            $('#id').val('');
            $('#nama').val('');
            $('#telp').val('');
            $('#alamat').val('');
        });

        $(document).on('click', '.editMarketing', function() {
            const id = $(this).data('id');
            $('#marketingModalLabel').html('Perbarui Marketing');
            $('.modal form').attr('action', '/dashboard/updateMarketing');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/marketingJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.m_id);
                    $('#nama').val(response.m_nama);
                    $('#telp').val(response.m_telp);
                    $('#alamat').val(response.m_alamat);
                }
            });
        });

        $(document).on('change', '#transaksi', function() {
            const id = $(this).val();
            if (id == '1') {
                $("#kredit option[value='654']").prop("selected", true);
                $('#kredit').selectpicker('refresh');
            } else
            if (id == '2') {
                $("#kredit option[value='7']").prop("selected", true);
                $('#kredit').selectpicker('refresh');
            } else {
                $("#kredit option[value='']").prop("selected", true);
                $('#kredit').selectpicker('refresh');
            }
        });

        if ($('#kaskecil').val()) {
            $.ajax({
                url: '/dashboard/kasKecilJson/' + $('#kaskecil').val(),
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#saldoKas').val(formatRupiah(response.saldo));
                }
            });
        }

        if ($('#jenisTrx').val() === '1') {
            $('.jenisHutang').hide();
            $('.jenisTunai').show();

            $(document).on('change', '#kaskecil', function() {
                const kaskecil = $(this).val();
                $.ajax({
                    url: '/dashboard/kasKecilJson/' + kaskecil,
                    method: 'get',
                    dataType: 'json',
                    success: function(response) {
                        $('#saldoKas').val(formatRupiah(response.saldo));
                    }
                });
            });
            $('#tempo').val(0);
        } else {
            $('.jenisTunai').hide();
            $('.jenisHutang').show();
            $("#kaskecil option[value='']").prop("selected", true);
            $('#kaskecil').selectpicker('refresh');
            $('#saldoKas').val(0);
        }

        $(document).on('change', '#jenisTrx', function() {
            const status = $(this).val();
            if (status === '1') {
                $('.jenisHutang').hide();
                $('.jenisTunai').show();

                $(document).on('change', '#kaskecil', function() {
                    const kaskecil = $(this).val();
                    $.ajax({
                        url: '/dashboard/kasKecilJson/' + kaskecil,
                        method: 'get',
                        dataType: 'json',
                        success: function(response) {
                            $('#saldoKas').val(formatRupiah(response.saldo));
                        }
                    });
                });
                $('#tempo').val(0);
            } else {
                $('.jenisTunai').hide();
                $('.jenisHutang').show();
                $("#kaskecil option[value='']").prop("selected", true);
                $('#kaskecil').selectpicker('refresh');
                $('#saldoKas').val(0);
            }
        });

        if ($('#supplier').val()) {
            $.ajax({
                url: '/dashboard/suplierJson/' + $('#supplier').val(),
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#namaSuplier').html(response.suplier_nama);
                    $('#alamatSuplier').html(response.suplier_alamat);
                    $('#telpSuplier').html(response.suplier_telp);
                }
            });
        }
        $(document).on('change', '#supplier', function() {
            const id = $(this).val();
            $.ajax({
                url: '/dashboard/suplierJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#namaSuplier').html(response.suplier_nama);
                    $('#alamatSuplier').html(response.suplier_alamat);
                    $('#telpSuplier').html(response.suplier_telp);
                }
            });
        });

        <?php if ($request->uri->getSegment(2) == 'rekapmaterial') { ?>
            // $('#showUnit').hide();
            if ($('#jenis').val() == 'keluar') {
                $('#showUnit').show();
            } else {
                $('#showUnit').hide();
            }
            $(document).on('change', '#jenis', function() {
                const id = $(this).val();
                if (id == 'keluar') {
                    $('#showUnit').show();
                } else {
                    $('#showUnit').hide();
                }
            });
        <?php } ?>

        <?php if ($request->uri->getSegment(2) == 'addUbk' || $request->uri->getSegment(2) == 'editUbk') { ?>
            $(document).on('change', '#unitUpah', function() {
                const id = $(this).val();
                if (id) {
                    $.ajax({
                        url: '/dashboard/getRekeningUnit/' + id,
                        method: 'get',
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            $("#debet option[value=" + response.rek_id + "]").prop("selected", true);
                            $('#debet').selectpicker('refresh');
                            $("#kredit option[value='801']").prop("selected", true);
                            $('#kredit').selectpicker('refresh');
                        }
                    });
                } else {
                    $("#debet option[value='']").prop("selected", true);
                    $('#debet').selectpicker('refresh');
                    $("#kredit option[value='']").prop("selected", true);
                    $('#kredit').selectpicker('refresh');
                }
            });
        <?php } ?>

        <?php if ($request->uri->getSegment(2) == 'addBarangKeluar' || $request->uri->getSegment(2) == 'editBarangKeluar') { ?>
            $(document).on('change', '#unit', function() {
                const id = $(this).val();
                if (id) {
                    $.ajax({
                        url: '/dashboard/getRekeningUnit/' + id,
                        method: 'get',
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            $("#debet option[value=" + response.rek_id + "]").prop("selected", true);
                            $('#debet').selectpicker('refresh');
                            $("#kredit option[value='35']").prop("selected", true);
                            $('#kredit').selectpicker('refresh');
                        }
                    });
                } else {
                    $("#debet option[value='']").prop("selected", true);
                    $('#debet').selectpicker('refresh');
                    $("#kredit option[value='']").prop("selected", true);
                    $('#kredit').selectpicker('refresh');
                }
            });
        <?php } ?>

        <?php if ($request->uri->getSegment(2) == 'editUbk') { ?>
            $(document).on('change', '#jenistrx', function() {
                const id = $(this).val();
                if (id === '1') {
                    $('#showKaskecil').show();
                    $("#debetLain option[value='694']").prop("selected", true);
                    $('#debetLain').selectpicker('refresh');
                    $("#kreditLain option[value='656']").prop("selected", true);
                    $('#kreditLain').selectpicker('refresh');
                } else if (id === '2') {
                    $("#debetLain option[value='']").prop("selected", true);
                    $('#debetLain').selectpicker('refresh');
                    $("#kreditLain option[value='801']").prop("selected", true);
                    $('#kreditLain').selectpicker('refresh');
                } else {
                    $('#showKaskecil').hide();
                    $("#debetLain option[value='']").prop("selected", true);
                    $('#debetLain').selectpicker('refresh');
                    $("#kreditLain option[value='']").prop("selected", true);
                    $('#kreditLain').selectpicker('refresh');
                }
            });
        <?php } ?>

        <?php if ($request->uri->getSegment(2) == 'addKaskecil') { ?>
            $(document).on('change', '#jenis', function() {
                const id = $(this).val();
                if (id === '1') {
                    $("#debet option[value='655']").prop("selected", true);
                    $('#debet').selectpicker('refresh');
                    $("#kredit option[value='21']").prop("selected", true);
                    $('#kredit').selectpicker('refresh');
                } else if (id === '2') {
                    $("#debet option[value='654']").prop("selected", true);
                    $('#debet').selectpicker('refresh');
                    $("#kredit option[value='21']").prop("selected", true);
                    $('#kredit').selectpicker('refresh');
                } else if (id === '3') {
                    $("#debet option[value='7']").prop("selected", true);
                    $('#debet').selectpicker('refresh');
                    $("#kredit option[value='21']").prop("selected", true);
                    $('#kredit').selectpicker('refresh');
                } else if (id === '4') {
                    $("#debet option[value='656']").prop("selected", true);
                    $('#debet').selectpicker('refresh');
                    $("#kredit option[value='21']").prop("selected", true);
                    $('#kredit').selectpicker('refresh');
                } else {
                    $("#debet option[value='']").prop("selected", true);
                    $('#debet').selectpicker('refresh');
                    $("#kredit option[value='']").prop("selected", true);
                    $('#kredit').selectpicker('refresh');
                }
            });
        <?php } ?>

        $('#showKpr').hide();
        if ($("#jenisSelect").val() == 'kpr') {
            $('#showKpr').show();
        } else {
            $('#showKpr').hide();
        }

        $("#jenisSelect").change(function() {
            if ($(this).val() == 'kpr') {
                $('#showKpr').show();

                $('#hargariil').val(0);
                $('#nnup').val(0);
                $('#tanahLebih').val(0);
                $('#mutu').val(0);
                $('#sbum').val(0);
                $('#ttlharga').val(0);
                $('#ajbn').val(0);
                $('#pph').val(0);
                $('#bphtb').val(0);
                $('#realisasi').val(0);
                $('#shm').val(0);
                $('#kanopi').val(0);
                $('#tandon').val(0);
                $('#pompair').val(0);
                $('#teralis').val(0);
                $('#tembok').val(0);
                $('#pondasi').val(0);
                $('#pijb').val(0);
                $('#ppn').val(0);
                $('#fee').val(0);
                $('#unitselect').val('').selectpicker("refresh");

                $('#harga').val(0);
                $('#kredit_harga').val('').selectpicker("refresh");
                $('#bayar').val(0);
                $('#debet_bayar').val('').selectpicker("refresh");
                $('#kpr').val('').selectpicker("refresh");
                $('#tglPengajuanKpr').val('');
                $('#nilaiPengajuanKpr').val(0);
                $('#tglAccKpr').val('');
                $('#nilaiAccKpr').val(0);
                $('#tglRealisasiKpr').val('');
                $('#sisaBayar').val(0);
                $('#debet_sisa').val('').selectpicker("refresh");
            } else {
                $('#showKpr').hide();

                $('#hargariil').val(0);
                $('#nnup').val(0);
                $('#tanahLebih').val(0);
                $('#mutu').val(0);
                $('#sbum').val(0);
                $('#ttlharga').val(0);
                $('#ajbn').val(0);
                $('#pph').val(0);
                $('#bphtb').val(0);
                $('#realisasi').val(0);
                $('#shm').val(0);
                $('#kanopi').val(0);
                $('#tandon').val(0);
                $('#pompair').val(0);
                $('#teralis').val(0);
                $('#tembok').val(0);
                $('#pondasi').val(0);
                $('#pijb').val(0);
                $('#ppn').val(0);
                $('#fee').val(0);
                $('#unitselect').val('').selectpicker("refresh");

                $('#harga').val(0);
                $('#kredit_harga').val('').selectpicker("refresh");
                $('#bayar').val(0);
                $('#debet_bayar').val('').selectpicker("refresh");
                $('#kpr').val('').selectpicker("refresh");
                $('#tglPengajuanKpr').val('');
                $('#nilaiPengajuanKpr').val(0);
                $('#tglAccKpr').val('');
                $('#nilaiAccKpr').val(0);
                $('#tglRealisasiKpr').val('');
                $('#sisaBayar').val(0);
                $('#debet_sisa').val('').selectpicker("refresh");
            }
        });

        // if ($('#tukang').val()) {
        //     $.ajax({
        //         url: '/dashboard/tukangJson/' + $('#tukang').val(),
        //         method: 'get',
        //         dataType: 'json',
        //         success: function(response) {
        //             $('#namaTukang').html(response.tk_nama);
        //             $('#alamatTukang').html(response.tk_alamat);
        //         }
        //     });
        // }

        // $(document).on('change', '#tukang', function() {
        //     const id = $(this).val();
        //     $.ajax({
        //         url: '/dashboard/tukangJson/' + id,
        //         method: 'get',
        //         dataType: 'json',
        //         success: function(response) {
        //             $('#namaTukang').html(response.tk_nama);
        //             $('#alamatTukang').html(response.tk_alamat);
        //         }
        //     });
        // });

        // if ($('#tukang').val()) {
        //     $.ajax({
        //         url: '/dashboard/kasbonTukangJson/' + $('#tukang').val(),
        //         method: 'get',
        //         dataType: 'json',
        //         success: function(response) {
        //             $('#kasbon').children().remove().end();

        //             const option = $('<option>').val('').text(':: PILIH ::');
        //             $('#kasbon').append(option.attr('selected', true));
        //             $('#kasbon').selectpicker('refresh');

        //             for (var i = 0; i < response.length; i++) {
        //                 $('#kasbon').append($('<option>').val(response[i].bu_id).text(`Rp ` + formatRupiah(response[i].bu_nominal) + ` - ` + response[i].bu_keterangan.toUpperCase()));
        //             }
        //             $('#kasbon').selectpicker('refresh');
        //         }
        //     });
        // }

        $(document).on('change', '#tukang', function() {
            const id = $(this).val();
            $.ajax({
                url: '/dashboard/kasbonTukangJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#kasbon').children().remove().end();

                    const option = $('<option>').val('').text(':: PILIH ::');
                    $('#kasbon').append(option.attr('selected', true));
                    $('#kasbon').selectpicker('refresh');

                    for (var i = 0; i < response.length; i++) {
                        $('#kasbon').append($('<option>').val(response[i].bu_id).text(response[i].bu_nomor + ` - ` + response[i].bu_keterangan.toUpperCase()));
                    }
                    $('#kasbon').selectpicker('refresh');
                }
            });
        });

        $(document).on('change', '#ubk', function() {
            const id = $('#unitUpah').val();
            const ubk = $(this).val();

            $.ajax({
                url: '/dashboard/upahJson/' + ubk,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    const nilai = response.up_nilai;
                    $('#nilaiUbk').val(formatRupiah(nilai));
                }
            });
        });

        $(document).on('keyup', '#qtyUpah', function() {
            const qty = $(this).val().match(/[0-9,]+/g).join([]);
            const quantity = qty.replace(',', '.');

            const nilai = $('#nilaiUbk').val().match(/[0-9,]+/g).join([]);
            const nilaiUbk = nilai.replace(',', '.');

            const total = parseFloat(quantity) * parseFloat(nilaiUbk);
            $('#total').val(formatRupiah(total.toFixed(2).toString().replace(".", ",")));
        });

        $(document).on('keyup', '#nilaiUbk', function() {
            const qty = $('#qtyUpah').val().match(/[0-9,]+/g).join([]);
            const quantity = qty.replace(',', '.');

            const nilai = $(this).val().match(/[0-9,]+/g).join([]);
            const nilaiUbk = nilai.replace(',', '.');

            const total = parseFloat(quantity) * parseFloat(nilaiUbk);
            $('#total').val(formatRupiah(total.toFixed(2).toString().replace(".", ",")));
        });

        $(document).on('change', '#barang', function() {
            const id = $(this).val();
            $.ajax({
                url: '/dashboard/barangJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#namaBarang').html(response.barang.barang_nama);
                    $('#kategoriSatuanBarang').html(response.barang.kabar_nama + ' / ' + response.barang.satuan_nama);
                    $('#qtyP').val(0);
                    if ($('#stokSaatIni')) {
                        $('#stokSaatIni').html(response.stok);
                    }

                    $.ajax({
                        url: '/dashboard/unitJson/' + $('#unit').val(),
                        method: 'get',
                        dataType: 'json',
                        success: function(unit) {
                            const tipeId = unit.unit_tipe;
                            $.ajax({
                                url: '/dashboard/rapBarangJson/' + tipeId + '/' + id,
                                method: 'get',
                                dataType: 'json',
                                success: function(rap) {
                                    $('#volumeRap').html(formatRupiah(rap.rap_volume));
                                }
                            });
                        }
                    });

                    $('#harga').val(formatRupiah(response.harga.toFixed(2).toString().replace(".", ",")));
                    $('#total').val(0);
                }
            });
        });

        $('.addSatuan').on('click', function() {
            $('#satuanModalLabel').html('Satuan Baru');
            $('.modal form').attr('action', '/dashboard/insertSatuan');
            $('.modal-footer button[type=submit]').html('Simpan');
            $('#id').val('');
            $('#nama').val('');
        });

        $(document).on('click', '.editSatuan', function() {
            const id = $(this).data('id');
            $('#satuanModalLabel').html('Perbarui Satuan');
            $('.modal form').attr('action', '/dashboard/updateSatuan');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/satuanJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.satuan_id);
                    $('#nama').val(response.satuan_nama);
                }
            });
        });

        $(document).on('click', '.editStatusKk', function() {
            const id = $(this).data('id');
            $('#statusKasKecilLabel').html('Perbarui Status');
            $('.modal form').attr('action', '/dashboard/updateStatusKk');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/kasKecilJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.kaskecil.kk_id);
                    $('#diterima').val(formatRupiah(response.diterima));
                    $('#digunakan').val(formatRupiah(response.digunakan));
                    $('#sisa').val(formatRupiah(response.saldo));
                    $('#kembali').val(formatRupiah(response.saldo ? response.saldo : (response.kaskecil.kk_kembali ? response.kaskecil.kk_kembali : 0)));
                    $("#statusKasKecil option[value=" + response.kaskecil.kk_status + "]").prop("selected", true);

                    if (response.kaskecil.kk_jenis === '1') {
                        const kkDebet = response.kaskecil.kk_kembaliDebet ? response.kaskecil.kk_kembaliDebet : '21';
                        const kkKredit = response.kaskecil.kk_kembaliKredit ? response.kaskecil.kk_kembaliKredit : '655';

                        $("#debet option[value=" + kkDebet + "]").prop("selected", true);
                        $('#debet').selectpicker('refresh');
                        $("#kredit option[value=" + kkKredit + "]").prop("selected", true);
                        $('#kredit').selectpicker('refresh');
                    } else if (response.kaskecil.kk_jenis === '2') {
                        const kkDebet = response.kaskecil.kk_kembaliDebet ? response.kaskecil.kk_kembaliDebet : '21';
                        const kkKredit = response.kaskecil.kk_kembaliKredit ? response.kaskecil.kk_kembaliKredit : '654';

                        $("#debet option[value=" + kkDebet + "]").prop("selected", true);
                        $('#debet').selectpicker('refresh');
                        $("#kredit option[value=" + kkKredit + "]").prop("selected", true);
                        $('#kredit').selectpicker('refresh');
                    } else if (response.kaskecil.kk_jenis === '3') {
                        const kkDebet = response.kaskecil.kk_kembaliDebet ? response.kaskecil.kk_kembaliDebet : '21';
                        const kkKredit = response.kaskecil.kk_kembaliKredit ? response.kaskecil.kk_kembaliKredit : '7';

                        $("#debet option[value=" + kkDebet + "]").prop("selected", true);
                        $('#debet').selectpicker('refresh');
                        $("#kredit option[value=" + kkKredit + "]").prop("selected", true);
                        $('#kredit').selectpicker('refresh');
                    } else if (response.kaskecil.kk_jenis === '4') {
                        const kkDebet = response.kaskecil.kk_kembaliDebet ? response.kaskecil.kk_kembaliDebet : '21';
                        const kkKredit = response.kaskecil.kk_kembaliKredit ? response.kaskecil.kk_kembaliKredit : '656';

                        $("#debet option[value=" + kkDebet + "]").prop("selected", true);
                        $('#debet').selectpicker('refresh');
                        $("#kredit option[value=" + kkKredit + "]").prop("selected", true);
                        $('#kredit').selectpicker('refresh');
                    } else {
                        $("#debet option[value='']").prop("selected", true);
                        $('#debet').selectpicker('refresh');
                        $("#kredit option[value='']").prop("selected", true);
                        $('#kredit').selectpicker('refresh');
                    }
                }
            });
        });

        $('.addKabar').on('click', function() {
            $('#kabarModalLabel').html('Kategori Baru');
            $('.modal form').attr('action', '/dashboard/insertKategoriBarang');
            $('.modal-footer button[type=submit]').html('Simpan');
            $('#id').val('');
            $('#nama').val('');
        });

        $(document).on('click', '.editKabar', function() {
            const id = $(this).data('id');
            $('#kabarModalLabel').html('Perbarui Kategori');
            $('.modal form').attr('action', '/dashboard/updateKategoriBarang');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/kabarJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.kabar_id);
                    $('#nama').val(response.kabar_nama);
                }
            });
        });

        $('.addTukang').on('click', function() {
            $('#tukangModalLabel').html('Tukang Baru');
            $('.modal form').attr('action', '/dashboard/insertTukang');
            $('.modal-footer button[type=submit]').html('Simpan');
            $('#id').val('');
            $('#nama').val('');
            $('#alamat').val('');
        });

        $(document).on('click', '.editTukang', function() {
            const id = $(this).data('id');
            $('#tukangModalLabel').html('Perbarui Data');
            $('.modal form').attr('action', '/dashboard/updateTukang');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/tukangJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.tk_id);
                    $('#nama').val(response.tk_nama);
                    $('#alamat').val(response.tk_alamat);
                }
            });
        });

        $('.addSabar').on('click', function() {
            $('#sabarModalLabel').html('Status Baru');
            $('.modal form').attr('action', '/dashboard/insertStatusbarang');
            $('.modal-footer button[type=submit]').html('Simpan');
            $('#id').val('');
            $('#nama').val('');
        });

        $(document).on('click', '.editSabar', function() {
            const id = $(this).data('id');
            $('#sabarModalLabel').html('Perbarui Status');
            $('.modal form').attr('action', '/dashboard/updateStatusbarang');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/sabarJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.sb_id);
                    $('#nama').val(response.sb_nama);
                }
            });
        });

        $('.addTipeUnit').on('click', function() {
            $('#tipeUnitModalLabel').html('Tipe Unit Baru');
            $('.modal form').attr('action', '/dashboard/insertTipeUnit');
            $('.modal-footer button[type=submit]').html('Simpan');
            $('#id').val('');
            $('#nama').val('');
        });

        $(document).on('click', '.editTipeUnit', function() {
            const id = $(this).data('id');
            $('#tipeUnitModalLabel').html('Perbarui Tipe Unit');
            $('.modal form').attr('action', '/dashboard/updateTipeUnit');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/tipeUnitJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.type_id);
                    $('#nama').val(response.type_nama);
                }
            });
        });

        $('.addKpr').on('click', function() {
            $('#kprModalLabel').html('KPR Baru');
            $('.modal form').attr('action', '/dashboard/insertKpr');
            $('.modal-footer button[type=submit]').html('Simpan');
            $('#id').val('');
            $('#nama').val('');
            $('#keterangan').val('');
        });

        $(document).on('click', '.editKpr', function() {
            const id = $(this).data('id');
            $('#kprModalLabel').html('Perbarui KPR');
            $('.modal form').attr('action', '/dashboard/updateKpr');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/kprJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#id').val(response.kpr_id);
                    $('#nama').val(response.kpr_nama);
                    $('#keterangan').val(response.kpr_keterangan);
                }
            });
        });

        $(document).on('click', '.editPaymentHutang', function() {
            const id = $(this).data('id');
            $.ajax({
                url: '/dashboard/bayarHsJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#idHb').val(response.hb_id);
                    $('#idHs').val(response.hb_hutangsuplier);
                    $('#tanggalTrxEdit').val(response.hb_tanggal);
                    $('#bayarEdit').val(formatRupiah(response.hb_bayar));
                    $('#keteranganEdit').val(response.hb_keterangan);
                    $('#debetEdit option[value=' + response.hb_debet + ']').attr('selected', true);
                    $('#debetEdit').selectpicker('refresh');
                    $('#kreditEdit option[value=' + response.hb_kredit + ']').attr('selected', true);
                    $('#kreditEdit').selectpicker('refresh');
                }
            });
        });

        $(document).on('click', '.updatePembelianStatusRek', function() {
            const idPbStatus = $('#idPbStatus').val();
            const status = $('#status').val();
            const tanggal = $('#tanggalTrx').val();
            const faktur = $('#faktur').val();
            const jenisTrx = $('#jenisTrx').val();
            const kaskecil = $('#kaskecil').val();
            const supplier = $('#supplier').val();
            const tempo = $('#tempo').val();
            const catatan = $('#catatan').val();
            const debet = $('#debet').val();
            const kredit = $('#kredit').val();

            const ongkir = $('#ongkir').val();
            const debetOngkir = $('#debetOngkir').val();
            const kreditOngkir = $('#kreditOngkir').val();
            $.ajax({
                url: '/dashboard/updatePembelianStatusRek',
                method: 'post',
                data: {
                    idPbStatus,
                    status,
                    tanggal,
                    faktur,
                    jenisTrx,
                    kaskecil,
                    supplier,
                    tempo,
                    catatan,
                    debet,
                    kredit,

                    ongkir,
                    debetOngkir,
                    kreditOngkir
                },
                dataType: 'json',
                success: function(response) {
                    setInterval('refreshPage()', 1000);
                }
            });
        });

        $(document).on('click', '.updateKeluarStatusRek', function() {
            const id = $('#id').val();
            const tanggal = $('#tanggalTrx').val();
            const faktur = $('#faktur').val();
            const unit = $('#unit').val();
            const catatan = $('#catatan').val();
            const debet = $('#debet').val();
            const kredit = $('#kredit').val();
            $.ajax({
                url: '/dashboard/updateKeluarStatusRek',
                method: 'post',
                data: {
                    id,
                    tanggal,
                    faktur,
                    unit,
                    catatan,
                    debet,
                    kredit
                },
                dataType: 'json',
                success: function(response) {
                    setInterval('refreshPage()', 1000);
                }
            });
        });

        $(document).on('click', '.editPaymentUpah', function() {
            const id = $(this).data('id');
            $.ajax({
                url: '/dashboard/bayarUpahJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#idUpah').val(response.tub_trxupah);
                    $('#idTrx').val(response.tub_id);
                    $('#tanggalTrxEdit').val(response.tub_tanggal);
                    $('#bayarEdit').val(formatRupiah(response.tub_bayar));
                    $('#keteranganEdit').val(response.tub_keterangan);
                    $('#debetEdit option[value=' + response.tub_debet + ']').attr('selected', true);
                    $('#debetEdit').selectpicker('refresh');
                    $('#kreditEdit option[value=' + response.tub_kredit + ']').attr('selected', true);
                    $('#kreditEdit').selectpicker('refresh');
                }
            });
        });

        function formatRupiah(angka) {
            if (angka) {
                var number_string = angka.toString().match(/[0-9,]+/g).join([]).toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                if (parseInt(angka) < 0) {
                    return '-' + rupiah;
                } else {
                    return rupiah;
                }
            } else {
                return '';
            }
        }

        $(function() {
            loadRap();
            $(document).on('click', '.formAddRap', function() {
                const nama = $('#nama').val();
                const tipe = $('#tipe').val();
                const barang = $('#barang').val();
                const ubk = $('#ubk').val();
                const volume = $('#volume').val();
                const harga = $('#harga').val().match(/[0-9]+/g).join([]);
                const keterangan = $('#keterangan').val();

                if (barang) {
                    $.ajax({
                        url: '/dashboard/barangJson/' + barang,
                        type: 'get',
                        dataType: 'json',
                        success: function(response) {
                            $.ajax({
                                url: '/dashboard/tipeUnitJson/' + tipe,
                                method: 'get',
                                dataType: 'json',
                                success: function(restype) {
                                    const dataBrg = {
                                        id: parseInt(response.barang.barang_id),
                                        name: response.barang.barang_nama,
                                        price: harga,
                                        jenis: 'brg',

                                        typeId: tipe,
                                        rapTipe: restype.type_nama,
                                        rapSatuan: response.barang.satuan_nama,
                                        rapKeterangan: keterangan
                                    };
                                    cartLS.add(dataBrg, parseInt(volume));
                                    $('#barang option[value=""]').attr('selected', true);
                                    $('#barang').selectpicker('refresh');
                                    $('#ubk option[value=""]').attr('selected', true);
                                    $('#ubk').selectpicker('refresh');
                                    $('#volume').val('');
                                    $('#harga').val('');
                                    $('#keterangan').val();
                                    loadRap();
                                }
                            });
                        }
                    });
                }

                if (ubk) {
                    $.ajax({
                        url: '/dashboard/upahJson/' + ubk,
                        type: 'get',
                        dataType: 'json',
                        success: function(response) {
                            $.ajax({
                                url: '/dashboard/tipeUnitJson/' + tipe,
                                method: 'get',
                                dataType: 'json',
                                success: function(restype) {
                                    const dataBrg = {
                                        id: parseInt(response.up_id),
                                        name: response.up_nama,
                                        price: harga,
                                        jenis: 'ubk',

                                        typeId: tipe,
                                        rapTipe: restype.type_nama,
                                        rapSatuan: response.satuan_nama,
                                        rapKeterangan: keterangan
                                    };
                                    cartLS.add(dataBrg, parseInt(volume));
                                    $('#barang option[value=""]').attr('selected', true);
                                    $('#barang').selectpicker('refresh');
                                    $('#ubk option[value=""]').attr('selected', true);
                                    $('#ubk').selectpicker('refresh');
                                    $('#volume').val('');
                                    $('#harga').val('');
                                    $('#keterangan').val();
                                    loadRap();
                                }
                            });
                        }
                    });
                }
            });
        });

        $(document).on('click', '.removeItem', function() {
            const id = $(this).data('id');
            if (cartLS.exists(id)) {
                cartLS.remove(id);
                loadCartBiayaLain();
            }
        });

        $(document).on('click', '.removeItemRap', function() {
            const id = $(this).data('id');
            if (cartLS.exists(id)) {
                cartLS.remove(id);
                loadRap();
            }
        });

        $(document).on('click', '.clearItemRap', function() {
            cartLS.destroy();
            loadRap();
        });

        $(document).on('click', '.removeItemUpahLain', function() {
            const id = $(this).data('id');
            if (cartLS.exists(id)) {
                cartLS.remove(id);
                loadCartUpahLain();
            }
        });

        $(document).on('click', '.clearItemUpahLain', function() {
            cartLS.destroy();
            loadCartUpahLain();
        });


        function insertRap() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                $.ajax({
                    url: '/dashboard/insertRap',
                    method: 'post',
                    data: {
                        data: JSON.stringify(cartLS.list())
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            cartLS.destroy();
                            loadRap();
                            $("#toastAlert").toast({
                                delay: 2000
                            });
                            $("#toastAlert").toast('show');
                            $("#infoToast").html('Sukses!');
                            $(".toast-body>#message").html('Data berhasil disimpan.');
                        } else {
                            $("#toastAlert").toast({
                                delay: 2000
                            });
                            $("#toastAlert").toast('show');
                            $("#infoToast").html('Oppss!');
                            $(".toast-body>#message").html('Data barang belum ditambahkan atau sudah tersedia di RAP.');
                        }
                    },
                });
            }
        }

        // rap localStorage
        function loadRap() {
            const rapList = cartLS.list();
            if (rapList.length > 0) {
                $('#rapList').html('');
                for (var i = 0; i < rapList.length; i++) {
                    $('#rapList').append(`
                        <tr>
                            <td style="vertical-align:middle;">` + (parseInt(i) + 1) + `</td>
                            <td style="vertical-align:middle;">` + rapList[i].rapTipe + `</td>
                            <td style="vertical-align:middle;" class="text-uppercase">` + rapList[i].name + `</td>
                            <td style="vertical-align:middle;" class="text-uppercase">` + rapList[i].rapSatuan + `</td>
                            <td style="vertical-align:middle;">
                                <input type="number" class="form-control form-control-sm" id="quantity` + i + `" value="` + rapList[i].quantity + `" data-id="` + rapList[i].id + `">
                            </td>
                            <td style="vertical-align:middle;">
                                <input type="text" class="form-control form-control-sm" id="price` + i + `" value="` + formatRupiah(rapList[i].price) + `" data-id="` + rapList[i].id + `">
                            </td>
                            <td style="vertical-align:middle;">` + formatRupiah(rapList[i].price * rapList[i].quantity) + `</td>
                            <td style="vertical-align:middle;"><textarea class="form-control form-control-sm" rows="1" id="keteranganRap` + i + `" data-id="` + rapList[i].id + `">` + rapList[i].rapKeterangan + `</textarea></td>
                            <td style="vertical-align:middle;"><button class="btn btn-danger btn-sm removeItemRap" type="button" data-id="` + rapList[i].id + `"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    `);
                }
            }

            if (rapList.length <= 0) {
                $('#rapList').html('');
                $('#rapList').append(`
                    <tr>
                        <td colspan="9" class="text-center font-italic">Data belum tersedia.</td>
                    </tr>
                `);
            }

            let typingTimer;
            let doneTypingInterval = 1000;

            for (let i = 0; i < rapList.length; i++) {
                let keteranganRap = document.getElementById('keteranganRap' + i);
                let rapQuantity = document.getElementById('quantity' + i);
                let rapPrice = document.getElementById('price' + i);

                keteranganRap.addEventListener('keyup', () => {
                    clearTimeout(typingTimer);
                    if (keteranganRap.value) {
                        typingTimer = setTimeout(doneTypingCatatan, doneTypingInterval);
                    }
                });

                rapPrice.addEventListener('keyup', () => {
                    clearTimeout(typingTimer);
                    if (rapPrice.value) {
                        typingTimer = setTimeout(doneTypingPrice, doneTypingInterval);
                    }
                });

                rapQuantity.addEventListener('keyup', () => {
                    clearTimeout(typingTimer);
                    if (rapQuantity.value) {
                        typingTimer = setTimeout(doneTypingQuantity, doneTypingInterval);
                    }
                });

                function doneTypingQuantity() {
                    const id = $('#quantity' + i).data('id');
                    const qty = $('#quantity' + i).val();
                    cartLS.update(parseInt(id), 'quantity', parseFloat(qty));
                    loadRap();
                }

                function doneTypingPrice() {
                    const id = $('#price' + i).data('id');
                    const price = $('#price' + i).val().match(/[0-9]+/g).join([]);
                    cartLS.update(parseInt(id), 'price', parseInt(price));
                    loadRap();
                }

                function doneTypingCatatan() {
                    const id = $('#keteranganRap' + i).data('id');
                    const keterangan = $('#keteranganRap' + i).val();
                    cartLS.update(parseInt(id), 'rapKeterangan', keterangan);
                    loadRap();
                }
            }
        }

        <?php if ($request->uri->getSegment(2) == 'addPembelian') { ?>
            cartLS.destroy();
            loadCartPembelian();
        <?php } ?>
        <?php if ($request->uri->getSegment(2) == 'editPembelian') { ?>
            cartLS.destroy();
            <?php if (in_array("approvalPembelian", $akses)) { ?>
                loadCartApprovePembelian();
            <?php } else { ?>
                loadCartPembelian();
            <?php } ?>
        <?php } ?>
        <?php if ($request->uri->getSegment(2) == 'addBarangKeluar' || $request->uri->getSegment(2) == 'editBarangKeluar') { ?>
            cartLS.destroy();
            loadCartKeluar();
        <?php } ?>
        <?php if ($request->uri->getSegment(2) == 'addUbk' || $request->uri->getSegment(2) == 'editUbk') { ?>
            cartLS.destroy();
            loadCartUpah();
            loadCartUpahLain();
        <?php } ?>

        <?php if ($request->uri->getSegment(2) == 'addLedger' || $request->uri->getSegment(2) == 'editLedger') { ?>
            cartLS.destroy();
            loadCartLedger();
        <?php } ?>

        $(document).on('keyup', '#qtyP', function() {
            const qty = $(this).val().match(/[0-9,]+/g).join([]);
            const quantity = qty.replace(',', '.');

            let stok = 0;
            if ($('#stokSaatIni').html()) {
                const stk = $('#stokSaatIni').html().match(/[0-9,]+/g).join([]);
                stok = stk.replace(',', '.');
            } else {
                $('.addCartKeluar').attr('disabled', false);
            }

            // if ($('#stokSaatIni').html()) {
            //     if (parseFloat(quantity) > parseFloat(stok)) {
            //         $('.addCartKeluar').attr('disabled', true);
            //     }
            //     if (parseFloat(quantity) <= parseFloat(stok)) {
            //         $('.addCartKeluar').attr('disabled', false);
            //     }
            // } else {
            //     $('.addCartKeluar').attr('disabled', false);
            // }

            let harga = 0;
            if ($('#harga').val()) {
                const hrg = $('#harga').val().match(/[0-9,]+/g).join([]);
                harga = hrg.replace(',', '.');
            }
            const total = quantity * harga;
            $('#total').val(formatRupiah(total.toFixed(2).toString().replace(".", ",")));
        });

        $(document).on('keyup', '#harga', function() {
            const qty = $('#qtyP').val().match(/[0-9,]+/g).join([]);
            const quantity = qty.replace(',', '.');
            const hrg = $(this).val().match(/[0-9,]+/g).join([]);
            const harga = hrg.replace(',', '.');
            const total = quantity * harga;
            $('#total').val(formatRupiah(total.toFixed(2).toString().replace(".", ",")));
        });

        $(document).on('change', '#suplier', function() {
            const id = $(this).val();
            $.ajax({
                url: '/dashboard/suplierJson/' + id,
                type: 'get',
                dataType: 'json',
                success: function(suplier) {
                    $.ajax({
                        url: '/dashboard/rekeningJson/' + suplier.suplier_rekening,
                        type: 'get',
                        dataType: 'json',
                        success: function(response) {
                            const kodeRek = response.rek_kode + '.' + suplier.suplier_kode;
                            $.ajax({
                                url: '/dashboard/rekeningByKodeJson/' + kodeRek,
                                type: 'get',
                                dataType: 'json',
                                success: function(rek) {
                                    $('#kredit option[value=' + rek.rek_id + ']').attr('selected', true);
                                    $('#kredit').selectpicker('refresh');
                                }
                            });
                        }
                    });
                }
            });
        });

        $(document).on('click', '.addCartPembelian', function() {
            const idBrg = $('#barang').val();
            if (idBrg) {
                $.ajax({
                    url: '/dashboard/barangJson/' + idBrg,
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                        const qty = $('#qtyP').val().match(/[0-9,]+/g).join([]);
                        const quantity = qty.replace(',', '.');
                        const dataBrg = {
                            id: parseInt(response.barang.barang_id),
                            name: response.barang.barang_nama,
                            price: 1,
                        };
                        cartLS.add(dataBrg, parseFloat(quantity ? quantity : 1));
                        $('#qtyP').val('0');
                        loadCartPembelian();
                    }
                });
            } else {
                $("#toastAlert").toast({
                    delay: 3000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oops!');
                $(".toast-body>#message").html('Pilih barang, quantity dan harga harus diisi.');
            }
        });

        $(document).on('click', '.addApproveCartPembelian', function() {
            const idBrg = $('#barang').val();
            if (idBrg) {
                $.ajax({
                    url: '/dashboard/barangJson/' + idBrg,
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                        const qty = $('#qtyP').val().match(/[0-9,]+/g).join([]);
                        const quantity = qty.replace(',', '.');

                        const hrg = $('#harga').val().match(/[0-9,]+/g).join([]);
                        const harga = hrg.replace(',', '.');

                        const dataBrg = {
                            id: parseInt(response.barang.barang_id),
                            name: response.barang.barang_nama,
                            price: parseFloat(harga),
                            suplier: $('#suplier').val(),
                            suplierText: $('#suplier option:selected').text(),
                            debet: $('#debet').val(),
                            debetText: $('#debet option:selected').text(),
                            kredit: $('#kredit').val(),
                            kreditText: $('#kredit option:selected').text(),
                            tempo: $('#tempo').val()
                        };
                        cartLS.add(dataBrg, parseFloat(quantity ? quantity : 1));

                        $('#qtyP').val('0');
                        $('#harga').val('0');
                        $('#total').val('0');
                        $('#tempo').val('0');
                        loadCartApprovePembelian();
                    }
                });
            } else {
                $("#toastAlert").toast({
                    delay: 3000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oops!');
                $(".toast-body>#message").html('Pilih barang, quantity dan harga harus diisi.');
            }
        });

        // <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].debetText + `</td>
        // <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].kreditText + `</td>
        function loadCartApprovePembelian() {
            const cartList = cartLS.list();
            if (cartList.length > 0) {
                $('#cartApproveListPembelian').html('');
                let total = 0;
                for (var i = 0; i < cartList.length; i++) {
                    const qty = cartList[i].quantity.toString();
                    const quantity = qty.replace('.', ',');
                    const harga = cartList[i].price;
                    const subtotal = (harga * cartList[i].quantity);

                    $('#cartApproveListPembelian').append(`
                            <tr>
                                <td style="vertical-align:middle;">` + (parseInt(i) + 1) + `</td>
                                <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].name + `</td>
                                <td style="vertical-align:middle;"><a href="#" class="editQty" data-id="` + cartList[i].id + `" data-toggle="modal" data-target="#qtyHargaModal" title="Edit">` + formatRupiah(quantity) + `</a></td>
                                <td style="vertical-align:middle;">` + formatRupiah(harga.toFixed(2).toString().replace('.', ',')) + `</td>
                                <td style="vertical-align:middle;">` + formatRupiah(subtotal.toFixed(2).toString().replace('.', ',')) + `</td>
                                <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].suplierText + `</td>
                                <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].tempo + ` Hari</td>
                                <td style="vertical-align:middle;"><button class="btn btn-danger btn-sm removeItem" type="button" data-id="` + cartList[i].id + `"><i class="fas fa-sm fa-trash fa-sm"></i></button></td>
                            </tr>
                        `);
                    total += subtotal;
                }

                let ongkir = 0;
                if ($('#ongkir').val()) {
                    const ogk = $('#ongkir').val().match(/[0-9,]+/g).join([]);
                    ongkir = ogk.replace(',', '.');
                }
                $('#grandTotalAkhir').val(formatRupiah(total + parseInt(ongkir)));

                $('#cartApproveListPembelian').append(`
                    <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                        <td style="vertical-align:middle;" colspan="4" class="text-uppercase font-weight-bold text-right">TOTAL</td>
                        <td class="text-uppercase font-weight-bold">
                            ` + formatRupiah(total.toFixed(2).toString().replace(".", ",")) + `
                            <input type="hidden" id="grandTotal" value="` + total + `">
                        </td>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                `);
            } else {
                $('#cartApproveListPembelian').html('');
                $('#cartApproveListPembelian').append(`
                    <tr>
                        <td colspan="10" class="font-italic text-center">Data belum tersedia.</td>
                    </tr>
                `);
            }

            $(document).on('click', '.editQty', function() {
                const id = $(this).data('id');
                const cartItem = cartLS.get(parseInt(id));
                $('#itemId').val(cartItem.id);
                $('#newQty').val(formatRupiah(cartItem.quantity.toString().replace('.', ',')));
                if ($('#newHarga').val()) {
                    $('#newHarga').val(formatRupiah(cartItem.price.toString().replace('.', ',')));
                }
            });

            $(document).on('click', '.updateQtyHarga', function() {
                const id = parseInt($('#itemId').val());
                const qty = $('#newQty').val().match(/[0-9,]+/g).join([]);
                const quantity = qty.replace(',', '.');

                let hargaItem = 0;
                if ($('#newHarga').val()) {
                    const hrgItem = $('#newHarga').val().match(/[0-9,]+/g).join([]);
                    hargaItem = hrgItem.replace(',', '.');
                }
                cartLS.update(id, 'price', parseFloat(hargaItem));

                const cartItem = cartLS.get(id);
                cartLS.remove(id)
                cartLS.add(cartItem, parseFloat(quantity));

                <?php if ($request->uri->getSegment(2) == 'editPembelian') { ?>
                    <?php if (in_array("approvalPembelian", $akses)) { ?>
                        loadCartApprovePembelian();
                    <?php } else { ?>
                        loadCartPembelian();
                    <?php } ?>
                <?php } ?>
                $('#qtyHargaModal').modal('hide');
            });

            $(document).on('keyup', '#ongkir', function() {
                let total = 0;
                if ($('#grandTotal').val()) {
                    const ttl = $('#grandTotal').val().match(/[0-9,]+/g).join([]);
                    total = ttl.replace(',', '.');
                }

                const ogk = $(this).val().match(/[0-9,]+/g).join([]);
                const ongkir = ogk.replace(',', '.');

                $('#grandTotalAkhir').val(formatRupiah(parseInt(total) + parseInt(ongkir)));
            });
        }

        $(document).on('click', '.addCartKeluar', function() {
            const idBrg = $('#barang').val();
            if (idBrg) {
                $.ajax({
                    url: '/dashboard/barangJson/' + idBrg,
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                        const qty = $('#qtyP').val().match(/[0-9,]+/g).join([]);
                        const quantity = qty.replace(',', '.');

                        const hrg = $('#harga').val().match(/[0-9,]+/g).join([]);
                        const harga = hrg.replace(',', '.');

                        const dataBrg = {
                            id: parseInt(response.barang.barang_id),
                            name: response.barang.barang_nama,
                            price: parseFloat(harga)
                        };
                        cartLS.add(dataBrg, parseFloat(quantity ? quantity : 1));

                        $('#barang option[value=' + response.barang.barang_id + ']').attr('selected', false);
                        $('#barang option[value=""]').attr('selected', true);
                        $('#barang').selectpicker('refresh');

                        $('#namaBarang').html('');
                        $('#stokSaatIni').html('');
                        $('#kategoriSatuanBarang').html('');
                        $('#qtyP').val('0');
                        $('#harga').val('0');
                        $('#total').val('0');
                        loadCartKeluar();
                    }
                });
            } else {
                $("#toastAlert").toast({
                    delay: 3000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oops!');
                $(".toast-body>#message").html('Pilih unit, barang dan quantity harus diisi.');
            }
        });

        $(document).on('click', '.removeItem', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                const id = $(this).data('id');
                if (cartLS.exists(id)) {
                    cartLS.remove(id);
                    <?php if ($request->uri->getSegment(2) == 'addPembelian') { ?>
                        loadCartPembelian();
                    <?php } ?>
                    <?php if ($request->uri->getSegment(2) == 'editPembelian') { ?>
                        <?php if (in_array("approvalPembelian", $akses)) { ?>
                            loadCartApprovePembelian();
                        <?php } else { ?>
                            loadCartPembelian();
                        <?php } ?>
                    <?php } ?>
                    <?php if ($request->uri->getSegment(2) == 'addBarangKeluar' || $request->uri->getSegment(2) == 'editBarangKeluar') { ?>
                        loadCartKeluar();
                    <?php } ?>
                    <?php if ($request->uri->getSegment(2) == 'addUbk' || $request->uri->getSegment(2) == 'editUbk') { ?>
                        loadCartUpah();
                    <?php } ?>
                }
            }
        });

        $(document).on('click', '.clearItem', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                cartLS.destroy();
                <?php if ($request->uri->getSegment(2) == 'addPembelian') { ?>
                    loadCartPembelian();
                <?php } ?>
                <?php if ($request->uri->getSegment(2) == 'editPembelian') { ?>
                    <?php if (in_array("approvalPembelian", $akses)) { ?>
                        loadCartApprovePembelian();
                    <?php } else { ?>
                        loadCartPembelian();
                    <?php } ?>
                <?php } ?>
                <?php if ($request->uri->getSegment(2) == 'addBarangKeluar' || $request->uri->getSegment(2) == 'editBarangKeluar') { ?>
                    loadCartKeluar();
                <?php } ?>
                <?php if ($request->uri->getSegment(2) == 'addUbk' || $request->uri->getSegment(2) == 'editUbk') { ?>
                    loadCartUpah();
                <?php } ?>
            }
        });

        function loadCartPembelian() {
            const cartList = cartLS.list();
            if (cartList.length > 0) {
                $('#cartListPembelian').html('');
                let total = 0;
                for (var i = 0; i < cartList.length; i++) {
                    const qty = cartList[i].quantity.toString();
                    const quantity = qty.replace('.', ',');

                    $('#cartListPembelian').append(`
                        <tr>
                            <td style="vertical-align:middle;">` + (parseInt(i) + 1) + `</td>
                            <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].name + `</td>
                            <td style="vertical-align:middle;">` + formatRupiah(quantity) + `</td>
                            <td style="vertical-align:middle;"><button class="btn btn-danger btn-sm removeItem" type="button" data-id="` + cartList[i].id + `"><i class="fas fa-sm fa-trash fa-sm"></i></button></td>
                        </tr>
                    `);
                }
            } else {
                $('#cartListPembelian').html('');
                $('#cartListPembelian').append(`
                    <tr>
                        <td colspan="4" class="font-italic text-center">Data belum tersedia.</td>
                    </tr>
                `);
            }
        }

        $(document).on('click', '.addCartUpah', function() {
            const idUpah = $('#ubk').val();
            if (idUpah && $('#unitUpah').val() && $('#tanggalUpah').val()) {
                $.ajax({
                    url: '/dashboard/upahJson/' + idUpah,
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                        $.ajax({
                            url: '/dashboard/unitJson/' + $('#unitUpah').val(),
                            type: 'get',
                            dataType: 'json',
                            success: function(unit) {
                                const qty = $('#qtyUpah').val().match(/[0-9,]+/g).join([]);
                                const quantity = qty.replace(',', '.');

                                const hrg = $('#nilaiUbk').val().match(/[0-9,]+/g).join([]);
                                const harga = hrg.replace(',', '.');

                                const tgl = $('#tanggalUpah').val();

                                const dataBrg = {
                                    id: parseInt(tgl.replace(/[^a-zA-Z0-9]/g, '') + response.up_id.toString()),
                                    idUpah: response.up_id,
                                    name: response.up_nama,
                                    price: parseFloat(harga),
                                    unitText: unit.unit_nama,
                                    unit: unit.unit_id,
                                    debet: $('#debet').val(),
                                    kredit: $('#kredit').val(),
                                    tanggal: $('#tanggalUpah').val()
                                };
                                cartLS.add(dataBrg, parseFloat(quantity ? quantity : 1));

                                // $('#ubk option[value=' + response.up_id + ']').attr('selected', false);
                                // $('#ubk option[value=""]').attr('selected', true);
                                // $('#ubk').selectpicker('refresh');

                                $('#qtyUpah').val('0');
                                // $('#nilaiUbk').val('0');
                                $('#total').val('0');
                                loadCartUpah();
                            }
                        });
                    }
                });
            } else {
                $("#toastAlert").toast({
                    delay: 3000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oops!');
                $(".toast-body>#message").html('Pilih unit, upah dan jumlah dan nilai harus diisi.');
            }
        });

        $(document).on('click', '.addCartUpahLain', function() {
            if ($('#nominalLain').val() && $('#jenistrx').val() && $('#tanggalLain').val()) {
                const tgl = $('#tanggalLain').val();
                const jenistrx = $('#jenistrx').val();

                const hrg = $('#nominalLain').val().match(/[0-9,]+/g).join([]);
                const harga = hrg.replace(',', '.');

                const dataBrg = {
                    id: parseInt(tgl.replace(/[^a-zA-Z0-9]/g, '') + jenistrx),
                    name: $("#jenistrx option:selected").text(),
                    jenistrx: jenistrx,
                    price: parseFloat(harga),
                    tanggal: tgl,
                    kaskecil: $('#kaskecil').val(),
                    kaskecilText: $('#kaskecil').val() ? $("#kaskecil option:selected").text() : '-',
                    debetLain: $('#debetLain').val(),
                    debetText: $("#debetLain option:selected").text(),
                    kreditLain: $('#kreditLain').val(),
                    kreditText: $("#kreditLain option:selected").text(),
                    keteranganLain: $('#keteranganLain').val()
                };
                cartLS.add(dataBrg, 1);
                loadCartUpahLain();
            } else {
                $("#toastAlert").toast({
                    delay: 3000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oops!');
                $(".toast-body>#message").html('Pilih jenis transaksi dan isi nominal.');
            }
        });

        function loadCartUpahLain() {
            const cartList = cartLS.list();
            if (cartList.length > 0) {
                $('#cartListLain').html('');
                let total = 0;
                for (var i = 0; i < cartList.length; i++) {
                    const hrg = cartList[i].price.toString();
                    const nominal = hrg.match(/[0-9,]+/g).join([]);

                    $('#cartListLain').append(`
                        <tr>
                            <td style="vertical-align:middle;">` + (parseInt(i) + 1) + `</td>
                            <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].tanggal + `</td>
                            <td style="vertical-align:middle;">` + cartList[i].name + `</td>
                            <td style="vertical-align:middle;">` + formatRupiah(nominal) + `</td>
                            <td style="vertical-align:middle;">` + (cartList[i].kaskecil ? cartList[i].kaskecilText : '-') + `</td>
                            <td style="vertical-align:middle;">` + (cartList[i].debetLain ? cartList[i].debetText : '-') + `</td>
                            <td style="vertical-align:middle;">` + (cartList[i].kreditLain ? cartList[i].kreditText : '-') + `</td>
                            <td style="vertical-align:middle;">` + (cartList[i].keteranganLain ? cartList[i].keteranganLain : '-') + `</td>
                            <td style="vertical-align:middle;">
                                <button class="btn btn-danger btn-sm removeItemUpahLain" type="button" data-id="` + cartList[i].id + `"><i class="fas fa-sm fa-trash fa-sm"></i></button>
                            </td>
                        </tr>
                    `);
                }
            } else {
                $('#cartListLain').html('');
                $('#cartListLain').append(`
                    <tr>
                        <td colspan="9" class="font-italic text-center">Data belum tersedia.</td>
                    </tr>
                `);
            }
        }

        $(document).on('click', '.inserTrxupahLain', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                const cartList = cartLS.list();
                if (cartList.length > 0) {
                    $.ajax({
                        url: '/dashboard/inserTrxupahLain',
                        method: 'post',
                        data: {
                            idUpah: $('#id').val(),
                            data: JSON.stringify(cartList)
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                setInterval('refreshPage()', 1000);
                            } else {
                                $("#toastAlert").toast({
                                    delay: 3000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oops!');
                                $(".toast-body>#message").html(response.msg);
                            }
                        }
                    });
                } else {
                    $("#toastAlert").toast({
                        delay: 3000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oops!');
                    $(".toast-body>#message").html('Tambahkan data transaksi kas bon atau lembur.');
                }
            }
        });

        $(document).on('click', '.updateUpah', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                const id = $('#id').val();
                const tanggal = $('#tanggalTrx').val();
                const faktur = $('#faktur').val();
                const tukang = $('#tukang').val();
                const catatan = $('#catatan').val();
                const total = $('#grandTotal').val();
                const debet = $('#debet').val();
                const kredit = $('#kredit').val();
                const cartList = cartLS.list();
                if (id && faktur && tanggal && tukang && cartList) {
                    $.ajax({
                        url: '/dashboard/updateUpah',
                        method: 'post',
                        data: {
                            id,
                            tanggal,
                            faktur,
                            tukang,
                            catatan,
                            total,
                            debet,
                            kredit,
                            data: JSON.stringify(cartList)
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                setInterval('refreshPage()', 1000);
                            } else {
                                $("#toastAlert").toast({
                                    delay: 3000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oops!');
                                $(".toast-body>#message").html(response.msg);
                            }
                        }
                    });
                } else {
                    $("#toastAlert").toast({
                        delay: 3000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oops!');
                    $(".toast-body>#message").html('Pilih tukang dan data upah.');
                }
            }
        });

        $(document).on('click', '.insertUbk', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                const tanggal = $('#tanggalTrx').val();
                const faktur = $('#faktur').val();
                const tukang = $('#tukang').val();
                const catatan = $('#catatan').val();
                // const debet = $('#debet').val();
                // const kredit = $('#kredit').val();
                const cartList = cartLS.list();
                if (faktur && tanggal && tukang && cartList) {

                    $.ajax({
                        url: '/dashboard/insertUbk',
                        method: 'post',
                        data: {
                            tanggal,
                            faktur,
                            tukang,
                            catatan,
                            // debet,
                            // kredit,
                            data: JSON.stringify(cartList)
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                setInterval('refreshPage()', 1000);
                            } else {
                                $("#toastAlert").toast({
                                    delay: 3000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oops!');
                                $(".toast-body>#message").html(response.msg);
                            }
                        }
                    });
                } else {
                    $("#toastAlert").toast({
                        delay: 3000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oops!');
                    $(".toast-body>#message").html('Pilih tukang dan data upah.');
                }
            }
        });

        $(document).on('click', '.updateUbk', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                const id = $('#id').val();
                const tanggal = $('#tanggalTrx').val();
                const faktur = $('#faktur').val();
                const tukang = $('#tukang').val();
                const catatan = $('#catatan').val();
                // const debet = $('#debet').val();
                // const kredit = $('#kredit').val();
                const cartList = cartLS.list();
                if (id && faktur && tanggal && tukang && cartList) {

                    $.ajax({
                        url: '/dashboard/updateUbk',
                        method: 'post',
                        data: {
                            id,
                            tanggal,
                            faktur,
                            tukang,
                            catatan,
                            // debet,
                            // kredit,
                            data: JSON.stringify(cartList)
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                cartLS.destroy();
                                setInterval('refreshPage()', 1000);
                            } else {
                                $("#toastAlert").toast({
                                    delay: 3000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oops!');
                                $(".toast-body>#message").html(response.msg);
                            }
                        }
                    });
                } else {
                    $("#toastAlert").toast({
                        delay: 3000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oops!');
                    $(".toast-body>#message").html('Pilih tukang dan data upah.');
                }
            }
        });

        function loadCartUpah() {
            const cartList = cartLS.list();
            if (cartList.length > 0) {
                $('#cartListUpah').html('');
                let total = 0;
                for (var i = 0; i < cartList.length; i++) {
                    const qty = cartList[i].quantity.toString();
                    const quantity = qty.replace('.', ',');
                    const harga = cartList[i].price;
                    const subtotal = (harga * cartList[i].quantity);

                    $('#cartListUpah').append(`
                            <tr>
                                <td style="vertical-align:middle;">` + (parseInt(i) + 1) + `</td>
                                <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].tanggal + `</td>
                                <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].unitText + `</td>
                                <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].name + `</td>
                                <td style="vertical-align:middle;"><a href="#" class="editOutQty" data-id="` + cartList[i].id + `" data-toggle="modal" data-target="#qtyKeluarModal" title="Edit">` + formatRupiah(quantity) + `</a></td>
                                <td style="vertical-align:middle;">` + formatRupiah(harga.toFixed(2).toString().replace('.', ',')) + `</td>
                                <td style="vertical-align:middle;">` + formatRupiah(subtotal.toFixed(2).toString().replace('.', ',')) + `</td>
                                <td style="vertical-align:middle;"><button class="btn btn-danger btn-sm removeItem" type="button" data-id="` + cartList[i].id + `"><i class="fas fa-sm fa-trash fa-sm"></i></button></td>
                            </tr>
                        `);
                    total += subtotal;
                }

                $('#cartListUpah').append(`
                    <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                        <td style="vertical-align:middle;" colspan="6" class="text-uppercase font-weight-bold text-right">GRAND TOTAL</td>
                        <td class="text-uppercase font-weight-bold">
                            ` + formatRupiah(total.toFixed(2).toString().replace(".", ",")) + `
                            <input type="hidden" id="grandTotal" value="` + total + `">
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                `);
            } else {
                $('#cartListUpah').html('');
                $('#cartListUpah').append(`
                    <tr>
                        <td colspan="8" class="font-italic text-center">Data belum tersedia.</td>
                    </tr>
                `);
            }
        }

        $(document).on('keyup', '#ongkir', function() {
            let total = 0;
            if ($('#grandTotal2').val()) {
                const ttl = $('#grandTotal2').val().match(/[0-9,]+/g).join([]);
                total = ttl.replace(',', '.');
            }

            const ogk = $(this).val().match(/[0-9,]+/g).join([]);
            const ongkir = ogk.replace(',', '.');

            $('#grandTotalAkhir2').val(formatRupiah(parseInt(total) + parseInt(ongkir)));
        });

        function loadCartKeluar() {
            const cartList = cartLS.list();
            if (cartList.length > 0) {
                $('#cartListKeluar').html('');
                let total = 0;
                for (var i = 0; i < cartList.length; i++) {
                    const qty = cartList[i].quantity.toString();
                    const quantity = qty.replace('.', ',');
                    const harga = cartList[i].price;
                    const subtotal = (harga * cartList[i].quantity);

                    $('#cartListKeluar').append(`
                            <tr>
                                <td style="vertical-align:middle;">` + (parseInt(i) + 1) + `</td>
                                <td style="vertical-align:middle;" class="text-uppercase">` + cartList[i].name + `</td>
                                <td style="vertical-align:middle;"><a href="#" class="editOutQty" data-id="` + cartList[i].id + `" data-toggle="modal" data-target="#qtyKeluarModal" title="Edit">` + formatRupiah(quantity) + `</a></td>
                                <td style="vertical-align:middle;">` + formatRupiah(harga.toFixed(2).toString().replace('.', ',')) + `</td>
                                <td style="vertical-align:middle;">` + formatRupiah(subtotal.toFixed(2).toString().replace('.', ',')) + `</td>
                                <td style="vertical-align:middle;"><button class="btn btn-danger btn-sm removeItem" type="button" data-id="` + cartList[i].id + `"><i class="fas fa-sm fa-trash fa-sm"></i></button></td>
                            </tr>
                        `);
                    total += subtotal;
                }

                $('#cartListKeluar').append(`
                    <tr style="background-color: #eaecf4; border-color: #eaecf4;">
                        <td style="vertical-align:middle;" colspan="4" class="text-uppercase font-weight-bold text-right">GRAND TOTAL</td>
                        <td class="text-uppercase font-weight-bold">
                            ` + formatRupiah(total.toFixed(2).toString().replace(".", ",")) + `
                            <input type="hidden" id="grandTotal" value="` + total + `">
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:middle;" colspan="5" class="text-uppercase font-weight-bold text-left">
                            <button class="btn btn-secondary btn-sm clearItem" type="button">
                                <i class="fas fa-sm fa-broom fa-sm"></i> BERSIHKAN
                            </button>
                        </td>
                        <td class="text-uppercase font-weight-bold">
                            <?php if ($request->uri->getSegment(2) == 'addBarangKeluar') { ?>
                                <button class="btn btn-primary btn-sm insertBarangKeluar" type="button">
                                    <i class="fas fa-sm fa-save fa-sm"></i> SIMPAN
                                </button>
                            <?php } ?>
                            <?php if ($request->uri->getSegment(2) == 'editBarangKeluar') { ?>
                                <button class="btn btn-primary btn-sm updateBarangKeluar" type="button">
                                    <i class="fas fa-sm fa-save fa-sm"></i> SIMPAN
                                </button>
                            <?php } ?> 
                        </td>
                    </tr>
                `);
            } else {
                $('#cartListKeluar').html('');
                $('#cartListKeluar').append(`
                    <tr>
                        <td colspan="6" class="font-italic text-center">Data belum tersedia.</td>
                    </tr>
                `);
            }

            $(document).on('click', '.editOutQty', function() {
                const id = $(this).data('id');
                const cartItem = cartLS.get(parseInt(id));
                $('#outId').val(cartItem.id);
                $('#outQty').val(formatRupiah(cartItem.quantity.toString().replace('.', ',')));
            });

            $(document).on('click', '.updateQtyKeluar', function() {
                const id = parseInt($('#outId').val());
                const qty = $('#outQty').val().match(/[0-9,]+/g).join([]);
                const quantity = qty.replace(',', '.');

                const cartItem = cartLS.get(id);
                cartLS.remove(id)
                cartLS.add(cartItem, parseFloat(quantity));

                <?php if ($request->uri->getSegment(2) == 'addBarangKeluar' || $request->uri->getSegment(2) == 'editBarangKeluar') { ?>
                    loadCartKeluar();
                <?php } ?>
                $('#qtyKeluarModal').modal('hide');
            });
        }

        function refreshPage() {
            location.reload(true);
        }

        $(document).on('click', '.insertPembelian', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                const tanggal = $('#tanggalTrx').val();
                const faktur = $('#faktur').val();
                const catatan = $('#catatan').val();

                const cartList = cartLS.list();
                if (faktur && tanggal && cartList) {

                    $.ajax({
                        url: '/dashboard/insertPembelian',
                        method: 'post',
                        data: {
                            tanggal,
                            faktur,
                            catatan,
                            data: JSON.stringify(cartList)
                        },
                        dataType: 'json',
                        success: function(response) {
                            // console.log(response);
                            if (response.status) {
                                cartLS.destroy();
                                setInterval('refreshPage()', 1000);
                            } else {
                                $("#toastAlert").toast({
                                    delay: 3000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oops!');
                                $(".toast-body>#message").html(response.msg);
                            }
                        }
                    });
                } else {
                    $("#toastAlert").toast({
                        delay: 3000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oops!');
                    $(".toast-body>#message").html('Pilih data barang.');
                }
            }
        });

        $(document).on('click', '.addNewItemPembelian', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                const id = $('#id').val();
                const tanggal = $('#tanggalTrx').val();
                const faktur = $('#faktur').val();
                const catatan = $('#catatan').val();

                const cartList = cartLS.list();
                if (faktur && tanggal && cartList) {
                    $.ajax({
                        url: '/dashboard/addNewItemPembelian',
                        method: 'post',
                        data: {
                            id,
                            tanggal,
                            faktur,
                            catatan,
                            data: JSON.stringify(cartList)
                        },
                        dataType: 'json',
                        success: function(response) {
                            // console.log(response);
                            if (response.status) {
                                cartLS.destroy();
                                setInterval('refreshPage()', 1000);
                            } else {
                                $("#toastAlert").toast({
                                    delay: 3000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oops!');
                                $(".toast-body>#message").html(response.msg);
                            }
                        }
                    });
                } else {
                    $("#toastAlert").toast({
                        delay: 3000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oops!');
                    $(".toast-body>#message").html('Pilih data barang.');
                }
            }
        });

        $(document).on('click', '.insertBarangKeluar', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                const tanggal = $('#tanggalTrx').val();
                const faktur = $('#faktur').val();
                const unit = $('#unit').val();
                const catatan = $('#catatan').val();
                const debet = $('#debet').val();
                const kredit = $('#kredit').val();
                const cartList = cartLS.list();
                if (tanggal && faktur && unit && cartList) {
                    $.ajax({
                        url: '/dashboard/insertBarangKeluar',
                        method: 'post',
                        data: {
                            tanggal,
                            faktur,
                            unit,
                            catatan,
                            debet,
                            kredit,
                            data: JSON.stringify(cartList)
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                setInterval('refreshPage()', 1000);
                            } else {
                                $("#toastAlert").toast({
                                    delay: 3000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oops!');
                                $(".toast-body>#message").html(response.msg);
                            }
                        }
                    });
                } else {
                    $("#toastAlert").toast({
                        delay: 3000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oops!');
                    $(".toast-body>#message").html('Pilih unit dan data barang.');
                }
            }
        });

        $(document).on('click', '.updatePembelian', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                const id = $('#id').val();
                const tanggal = $('#tanggalTrx').val();
                const faktur = $('#faktur').val();
                const jenisTrx = $('#jenisTrx').val();
                const kaskecil = $('#kaskecil').val();
                const saldoKas = $('#saldoKas').val();
                // const supplier = $('#supplier').val();
                const catatan = $('#catatan').val();
                // const tempo = $('#tempo').val();
                const total = $('#grandTotal').val();
                // const debet = $('#debet').val();
                // const kredit = $('#kredit').val();
                const cartList = cartLS.list();
                if (id && jenisTrx && faktur && tanggal && cartList) {

                    $.ajax({
                        url: '/dashboard/updatePembelian',
                        method: 'post',
                        data: {
                            id,
                            tanggal,
                            faktur,
                            jenisTrx,
                            kaskecil,
                            saldoKas,
                            // supplier,
                            catatan,
                            // tempo,
                            total,
                            // debet,
                            // kredit,
                            data: JSON.stringify(cartList)
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                setInterval('refreshPage()', 1000);
                            } else {
                                $("#toastAlert").toast({
                                    delay: 3000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oops!');
                                $(".toast-body>#message").html(response.msg);
                            }
                        }
                    });
                } else {
                    $("#toastAlert").toast({
                        delay: 3000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oops!');
                    $(".toast-body>#message").html('Pilih jenis transaksi, suplier dan data barang.');
                }
            }
        });

        $(document).on('click', '.updateBarangKeluar', function() {
            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                const id = $('#id').val();
                const tanggal = $('#tanggalTrx').val();
                const faktur = $('#faktur').val();
                const unit = $('#unit').val();
                const catatan = $('#catatan').val();
                const debet = $('#debet').val();
                const kredit = $('#kredit').val();
                const cartList = cartLS.list();
                if (id && faktur && tanggal && unit && cartList) {
                    $.ajax({
                        url: '/dashboard/updateBarangKeluar',
                        method: 'post',
                        data: {
                            id,
                            tanggal,
                            faktur,
                            unit,
                            catatan,
                            debet,
                            kredit,
                            data: JSON.stringify(cartList)
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                setInterval('refreshPage()', 1000);
                            } else {
                                $("#toastAlert").toast({
                                    delay: 3000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oops!');
                                $(".toast-body>#message").html(response.msg);
                            }
                        }
                    });
                } else {
                    $("#toastAlert").toast({
                        delay: 3000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oops!');
                    $(".toast-body>#message").html('Pilih unit dan data barang.');
                }
            }
        });

        $(document).on('click', '.editItemPembelian', function() {
            const pid = $(this).data('pid');
            $('#itemPembelianModalLabel').html('Perbarui Item Pembelian');
            $('.modal form').attr('action', '/dashboard/updateItemPembelian');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/itemPembelianJson/' + pid,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#pid').val(response.pi_id);
                    $('#namaBrg').val(response.barang_nama);
                    $('#qtyBeli').val(formatRupiah(response.pi_qtybeli));
                    $('#qtyDatang').val(formatRupiah(response.pi_qtymasuk));
                    $('#hargaBarang').val(formatRupiah(response.pi_harga));
                }
            });
        });

        $(document).on('click', '.editItemKeluar', function() {
            const bkid = $(this).data('bkid');
            $('#itemKeluarModalLabel').html('Perbarui Item Barang Keluar');
            $('.modal form').attr('action', '/dashboard/updateItemBarangKeluar');
            $('.modal-footer button[type=submit]').html('Perbarui');
            $.ajax({
                url: '/dashboard/itemBarangKeluarJson/' + bkid,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#bid').val(response.bki_id);
                    $('#namaBrg').val(response.barang_nama);
                    $('#qtyOut').val(formatRupiah(response.bki_qty));
                    $('#hargaOut').val(formatRupiah(response.bki_harga));
                }
            });
        });

        $('#kaskecilbon').hide();
        $(document).on('change', '#jenistrx', function() {
            const jenis = $(this).val();
            if (jenis === '1') {
                $('#kaskecilbon').show();
            } else {
                $('#kaskecilbon').hide();
            }
        });

        <?php if ($request->uri->getSegment(2) == 'editPembelian') { ?>
            const totalItem = $('#dataItem').data('total');
            for (let i = 0; i < totalItem; i++) {
                $('#qtybeli' + i).val(formatRupiah($('#qtybeli' + i).val()));
                $('#qtybeli' + i).keyup(function() {
                    $(this).val(formatRupiah($(this).val()));
                });

                $('#qtysetuju' + i).val(formatRupiah($('#qtysetuju' + i).val()));
                $('#qtysetuju' + i).keyup(function() {
                    $(this).val(formatRupiah($(this).val()));
                });

                $('#qtydatang' + i).val(formatRupiah($('#qtydatang' + i).val()));
                $('#qtydatang' + i).keyup(function() {
                    $(this).val(formatRupiah($(this).val()));
                });

                $('#hargabrg' + i).val(formatRupiah($('#hargabrg' + i).val()));
                $('#hargabrg' + i).keyup(function() {
                    $(this).val(formatRupiah($(this).val()));
                });
            }
        <?php } ?>

        function loadCartLedger() {
            const prodList = cartLS.list();
            if (prodList.length > 0) {
                $('#ledgerListKredit').html('');
                if (prodList.length > 0) {
                    $('#tanggal').val(prodList[0].tanggal);
                    $('#nomor').val(prodList[0].name);
                    $('#uraian').val(prodList[0].uraian);
                }
                var totalKredit = 0;
                for (var i = 0; i < prodList.length; i++) {
                    if (prodList[i].nominalKredit) {
                        totalKredit += prodList[i].nominalKredit;
                        $('#ledgerListKredit').append(`
                            <tr>
                                <td style="vertical-align:middle;" class="text-uppercase">` + prodList[i].kreditText + `</td>
                                <td style="vertical-align:middle;">` + formatRupiah(prodList[i].nominalKredit) + `</td>
                                <td style="vertical-align:middle;"><button class="btn btn-danger btn-sm removeItem" type="button" data-id="` + prodList[i].id + `"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        `);
                    }
                }
                $('#ledgerListKredit').append(`
                    <tr class="bg-light font-weight-bold">
                        <td>TOTAL</td>
                        <td>` + formatRupiah(totalKredit) + `</td>
                        <td></td>
                    </tr>
                `);


                $('#ledgerListDebet').html('');
                var totalDebet = 0;
                for (var i = 0; i < prodList.length; i++) {
                    if (prodList[i].nominalDebet) {
                        totalDebet += prodList[i].nominalDebet;
                        $('#ledgerListDebet').append(`
                            <tr>
                                <td style="vertical-align:middle;" class="text-uppercase">` + prodList[i].debetText + `</td>
                                <td style="vertical-align:middle;">` + formatRupiah(prodList[i].nominalDebet) + `</td>
                                <td style="vertical-align:middle;"><button class="btn btn-danger btn-sm removeItem" type="button" data-id="` + prodList[i].id + `"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        `);
                    }
                }
                $('#ledgerListDebet').append(`
                    <tr class="bg-light font-weight-bold">
                        <td>TOTAL</td>
                        <td>` + formatRupiah(totalDebet) + `</td>
                        <td></td>
                    </tr>
                `);
            } else {
                $('#ledgerListDebet').append(`
                    <tr>
                        <td colspan="3" class="font-italic text-center">Data belum tersedia.</td>
                    </tr>
                `);

                $('#ledgerListKredit').append(`
                    <tr>
                        <td colspan="3" class="font-italic text-center">Data belum tersedia.</td>
                    </tr>
                `);
            }
        }

        $(".formAddLedger").on('click', function() {
            if ($('#nomor').val() && $('#uraian').val() && $('#debet').val() || $('#kredit').val()) {
                const prodList = cartLS.list();
                const dataLedger = {
                    id: (prodList.length > 0 ? prodList[prodList.length - 1].id + 1 : 1),
                    price: 1000,
                    name: $('#nomor').val(),
                    uraian: $('#uraian').val(),
                    tanggal: $('#tanggalPicker').val(),
                    ket_trx: $('#ket_trx').val(),

                    debetId: $('#debet').val(),
                    debetText: $('#debet option:selected').text(),
                    nominalDebet: parseInt($('#nominaldebet').val().match(/[0-9]+/g).join([])),

                    kreditId: $('#kredit').val(),
                    kreditText: $('#kredit option:selected').text(),
                    nominalKredit: parseInt($('#nominalkredit').val().match(/[0-9]+/g).join([]))
                };
                cartLS.add(dataLedger, 1);
                $('#debet').val('').selectpicker("refresh");
                $('#kredit').val('').selectpicker("refresh");
                $('#nominaldebet').val('0');
                $('#nominalkredit').val('0');
                loadCartLedger();
            } else {
                $("#toastAlert").toast({
                    delay: 2000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oppss!');
                $(".toast-body>#message").html('Nomor, uraian, debet atau kredit belum ditentukan.');
            }
        });

        function insertLedger() {

            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                var grandDebet = 0;
                var grandKredit = 0;
                for (var i = 0; i < cartLS.list().length; i++) {
                    grandDebet += cartLS.list()[i].nominalDebet;
                    grandKredit += cartLS.list()[i].nominalKredit;
                }

                const tanggalLedger = $('#tanggalPicker').val();
                const nomorLedger = $('#nomor').val();
                const uraian = $('#uraian').val();
                const ket_trx = $('#ket_trx').val();

                if (grandDebet != grandKredit) {
                    <?php if ($request->uri->getSegment(2) == 'addLedger') { ?>
                        $("#toastAlert").toast({
                            delay: 2000
                        });
                        $("#toastAlert").toast('show');
                        $("#infoToast").html('Oppss!');
                        $(".toast-body>#message").html('Grand total tidak sesuai.');
                    <?php } else { ?>
                        if (tanggalLedger && nomorLedger && uraian && ket_trx) {
                            $.ajax({
                                url: '/dashboard/insertLedger',
                                method: 'post',
                                data: {
                                    data: JSON.stringify(cartLS.list())
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status) {
                                        cartLS.destroy();
                                        loadCartLedger();
                                        location.reload();
                                        $("#toastAlert").toast({
                                            delay: 2000
                                        });
                                        $("#toastAlert").toast('show');
                                        $("#infoToast").html('Sukses!');
                                        $(".toast-body>#message").html('Data berhasil disimpan.');
                                    } else {
                                        $("#toastAlert").toast({
                                            delay: 2000
                                        });
                                        $("#toastAlert").toast('show');
                                        $("#infoToast").html('Oppss!');
                                        $(".toast-body>#message").html('Belum ada item yang ditambahkan.');
                                    }
                                }
                            });
                        } else {
                            $("#toastAlert").toast({
                                delay: 2000
                            });
                            $("#toastAlert").toast('show');
                            $("#infoToast").html('Oppss!');
                            $(".toast-body>#message").html('Tanggal, nomor, uraian dan keterangan transaksi belum diisi.');
                        }
                    <?php } ?>
                } else {
                    if (tanggalLedger && nomorLedger && uraian && ket_trx) {
                        $.ajax({
                            url: '/dashboard/insertLedger',
                            method: 'post',
                            data: {
                                data: JSON.stringify(cartLS.list())
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status) {
                                    cartLS.destroy();
                                    loadCartLedger();
                                    location.reload();
                                    $("#toastAlert").toast({
                                        delay: 2000
                                    });
                                    $("#toastAlert").toast('show');
                                    $("#infoToast").html('Sukses!');
                                    $(".toast-body>#message").html('Data berhasil disimpan.');
                                } else {
                                    $("#toastAlert").toast({
                                        delay: 2000
                                    });
                                    $("#toastAlert").toast('show');
                                    $("#infoToast").html('Oppss!');
                                    $(".toast-body>#message").html('Belum ada item yang ditambahkan.');
                                }
                            }
                        });
                    } else {
                        $("#toastAlert").toast({
                            delay: 2000
                        });
                        $("#toastAlert").toast('show');
                        $("#infoToast").html('Oppss!');
                        $(".toast-body>#message").html('Tanggal, nomor, uraian dan keterangan transaksi belum diisi.');
                    }
                }
            }
        }

        function updateLedger() {

            r = confirm('Yakin ingin melanjutkan?');
            if (r == true) {
                var grandDebet = 0;
                var grandKredit = 0;
                for (var i = 0; i < cartLS.list().length; i++) {
                    grandDebet += cartLS.list()[i].nominalDebet;
                    grandKredit += cartLS.list()[i].nominalKredit;
                }

                const tanggalLedger = $('#tanggalPicker').val();
                const nomorLedger = $('#nomor').val();
                const uraian = $('#uraian').val();
                const ket_trx = $('#ket_trx').val();

                if (tanggalLedger && nomorLedger && uraian && ket_trx) {
                    $.ajax({
                        url: '/dashboard/updateLedger',
                        method: 'post',
                        data: {
                            tanggalLedger,
                            nomorLedger,
                            uraian,
                            ket_trx,
                            data: JSON.stringify(cartLS.list())
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                loadCartLedger();
                                location.reload();
                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Sukses!');
                                $(".toast-body>#message").html('Data berhasil disimpan.');
                            } else {
                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oppss!');
                                $(".toast-body>#message").html('Belum ada item yang ditambahkan.');
                            }
                        }
                    });
                } else {
                    $("#toastAlert").toast({
                        delay: 2000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oppss!');
                    $(".toast-body>#message").html('Tanggal, nomor, uraian dan keterangan transaksi belum diisi.');
                }
            }
        }

        function loadCartBiayaLain() {
            $('#biayaLainList').html('');
            const prodList = cartLS.list();
            var totalLain = 0;
            for (var i = 0; i < prodList.length; i++) {
                if (prodList[i].price) {
                    totalLain += prodList[i].price;
                    $('#biayaLainList').append(`
                        <tr>
                            <td style="vertical-align:middle;" class="text-uppercase">` + prodList[i].tanggal + `</td>
                            <td style="vertical-align:middle;" class="text-uppercase">` + prodList[i].biayalainText + `</td>
                            <td style="vertical-align:middle;" class="text-uppercase">` + prodList[i].name + `</td>
                            <td style="vertical-align:middle;" align="right">` + formatRupiah(prodList[i].price) + `</td>
                            <td style="vertical-align:middle;">` + prodList[i].debetText + `</td>
                            <td style="vertical-align:middle;">` + prodList[i].kreditText + `</td>
                            <td style="vertical-align:middle;"><button class="btn btn-danger btn-sm removeItem" type="button" data-id="` + prodList[i].id + `"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    `);
                }
            }
            $('#biayaLainList').append(`
                <tr class="bg-light font-weight-bold">
                    <td colspan="3">TOTAL</td>
                    <td colspan="1" align="right">` + formatRupiah(totalLain) + `</td>
                    <td colspan="3"></td>
                </tr>
            `);
        }

        <?php if ($request->uri->getSegment(2) == 'piutangpenjualan') { ?>
            const prodList = cartLS.list();
            if (prodList.length > 0) {
                $('#btnTagSave').attr('disabled', false);
            } else {
                $('#btnTagSave').attr('disabled', true);
            }

            function insertPiutang() {
                const prodList = cartLS.list();
                if (prodList.length > 0) {
                    r = confirm('Yakin ingin melanjutkan?');
                    if (r == true) {
                        $.ajax({
                            url: '/dashboard/insertPiutang',
                            method: 'post',
                            data: {
                                puId: $('#puId').val(),
                                data: JSON.stringify(cartLS.list())
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status) {
                                    cartLS.destroy();
                                    loadCartTagihan();
                                    location.reload();

                                    $("#toastAlert").toast({
                                        delay: 2000
                                    });
                                    $("#toastAlert").toast('show');
                                    $("#infoToast").html('Sukses!');
                                    $(".toast-body>#message").html('Data berhasil disimpan.');
                                } else {
                                    cartLS.destroy();
                                    loadCartTagihan();
                                    location.reload();

                                    $("#toastAlert").toast({
                                        delay: 2000
                                    });
                                    $("#toastAlert").toast('show');
                                    $("#infoToast").html('Oppss!');
                                    $(".toast-body>#message").html('Mohon periksa kembali, ada beberapa field yang belum terisi.');
                                }
                            }
                        });
                    }
                } else {
                    $("#toastAlert").toast({
                        delay: 2000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oppss!');
                    $(".toast-body>#message").html('Data tagihan belum ditentukan.');
                }
            }
        <?php } ?>

        $(document).on('click', '.editPiutang', function() {
            const id = $(this).data('id');
            $('#piutangModalLabel').html('Perbarui/Bayar Tagihan');
            $('.modal form').attr('action', '/dashboard/updatePiutang');
            $('.modal-footer button[type=submit]').html('Simpan');
            $("#debet option[value='']").prop("selected", true);
            $('#debet').selectpicker('refresh');
            $("#kredit option[value='']").prop("selected", true);
            $('#kredit').selectpicker('refresh');
            $.ajax({
                url: '/dashboard/tagihanJson/' + id,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#idTp').val(response.tp_id);
                    $('#nofaktur').val(response.tp_nomor);
                    $("#jenis option[value=" + response.tp_jenis + "]").prop("selected", true);
                    $('#angsuran').val(response.tp_angsuran);
                    $('#tgljthtempo').val(response.tp_jthtempo);
                    $('#tagnilai').val(response.tp_nilai ? formatRupiah(response.tp_nilai) : 0);
                    $('#tglbayar').val(response.tp_tglbayar);
                    $('#tagbayar').val(response.tp_nominal ? formatRupiah(response.tp_nominal) : 0);
                    $('#uraian').val(response.tp_keterangan);
                    $("#debet option[value=" + response.tp_debet + "]").prop("selected", true);
                    $('#debet').selectpicker('refresh');
                    $("#kredit option[value=" + response.tp_kredit + "]").prop("selected", true);
                    $('#kredit').selectpicker('refresh');
                }
            });
        });

        function loadCartTagihan() {
            $('#tagihanList').html('');
            const prodList = cartLS.list();
            var totalTagihan = 0;
            for (var i = 0; i < prodList.length; i++) {
                if (prodList[i].price) {
                    totalTagihan += prodList[i].price;
                    $('#tagihanList').append(`
                        <tr>
                            <td style="vertical-align:middle;" class="text-uppercase">` + prodList[i].jenisText + `</td>
                            <td style="vertical-align:middle;" class="text-uppercase">` + prodList[i].name + `</td>
                            <td style="vertical-align:middle;" class="text-uppercase">` + prodList[i].tanggal + `</td>
                            <td style="vertical-align:middle;" align="right">` + formatRupiah(prodList[i].price) + `</td>
                            <td style="vertical-align:middle;" class="text-uppercase">` + prodList[i].keterangan + `</td>
                            <td style="vertical-align:middle;"><button class="btn btn-danger btn-sm removeItemTag" type="button" data-id="` + prodList[i].id + `"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    `);
                }
            }
            $('#tagihanList').append(`
                <tr class="bg-light font-weight-bold">
                    <td colspan="3">TOTAL</td>
                    <td align="right">` + formatRupiah(totalTagihan) + `</td>
                    <td colspan="2"></td>
                </tr>
            `);
        }

        <?php if ($request->uri->getSegment(2) == 'penjualanunit' || $request->uri->getSegment(2) == 'addPenjualan' || $request->uri->getSegment(2) == 'piutangpenjualan' || $request->uri->getSegment(2) == 'editPenjualan' || $request->uri->getSegment(2) == 'editpiutangpenjualan' || $request->uri->getSegment(2) == 'batalkanPenjualanUnit') { ?>

            $.ajax({
                url: '/dashboard/customerJson/' + $('#customerselect').val(),
                method: 'get',
                dataType: 'json',
                error: function() {
                    $('#namaCustomer').html('');
                    $('#alamatCustomer').html('');
                    $('#telpCustomer').html('');
                },
                success: function(response) {
                    $('#namaCustomer').html(response.cust_nama);
                    $('#alamatCustomer').html(response.cust_alamat);
                    $('#telpCustomer').html(response.cust_telp);
                }
            });

            $('#customerselect').on('change', function() {
                $.ajax({
                    url: '/dashboard/customerJson/' + $(this).val(),
                    method: 'get',
                    dataType: 'json',
                    error: function() {
                        $('#namaCustomer').html('');
                        $('#alamatCustomer').html('');
                        $('#telpCustomer').html('');
                    },
                    success: function(response) {
                        $('#namaCustomer').html(response.cust_nama);
                        $('#alamatCustomer').html(response.cust_alamat);
                        $('#telpCustomer').html(response.cust_telp);
                    }
                });
            });

            $('#hargariil').keyup(function() {
                let harga = 0;
                if ($(this).val()) {
                    const hargariil = $(this).val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#ttlharga').val(penambahan ? formatRupiah(penambahan) : '0');
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#nnup').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($(this).val()) {
                    const nnup = $(this).val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#ttlharga').val(penambahan ? formatRupiah(penambahan) : '0');
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#mutu').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($(this).val()) {
                    const unit_mutu = $(this).val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#ttlharga').val(penambahan ? formatRupiah(penambahan) : '0');
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#tanahLebih').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($(this).val()) {
                    const tanahLebih = $(this).val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#ttlharga').val(penambahan ? formatRupiah(penambahan) : '0');
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#sbum').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($(this).val()) {
                    const nsbum = $(this).val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#ttlharga').val(penambahan ? formatRupiah(penambahan) : '0');
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#ajbn').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($(this).val()) {
                    const najbn = $(this).val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#pph').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($(this).val()) {
                    const npph = $(this).val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#bphtb').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($(this).val()) {
                    const nbphtb = $(this).val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#realisasi').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($(this).val()) {
                    const nrealisasi = $(this).val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#shm').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($(this).val()) {
                    const nshm = $(this).val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#kanopi').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($(this).val()) {
                    const nkanopi = $(this).val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#tandon').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($(this).val()) {
                    const ntandon = $(this).val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#pompair').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($(this).val()) {
                    const npompair = $(this).val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#teralis').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($(this).val()) {
                    const nteralis = $(this).val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#tembok').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($(this).val()) {
                    const ntembok = $(this).val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#pondasi').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($(this).val()) {
                    const npondasi = $(this).val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#pijb').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($(this).val()) {
                    const npijb = $(this).val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#ppn').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($(this).val()) {
                    const nppn = $(this).val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#fee').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($(this).val()) {
                    const nfee = $(this).val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#bayar').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($(this).val()) {
                    const nbayar = $(this).val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($('#nilaiAccKpr').val()) {
                    const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            $('#nilaiAccKpr').keyup(function() {
                let harga = 0;
                if ($('#hargariil').val()) {
                    const hargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                    harga = hargariil.replace(',', '.');
                }

                let nup = 0;
                if ($('#nnup').val()) {
                    const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                    nup = nnup.replace(',', '.');
                }

                let kelebihantanah = 0;
                if ($('#tanahLebih').val()) {
                    const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                    kelebihantanah = tanahLebih.replace(',', '.');
                }

                let mutu = 0;
                if ($('#mutu').val()) {
                    const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                    mutu = unit_mutu.replace(',', '.');
                }

                let sbum = 0;
                if ($('#sbum').val()) {
                    const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                    sbum = nsbum.replace(',', '.');
                }

                let ajbn = 0;
                if ($('#ajbn').val()) {
                    const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                    ajbn = najbn.replace(',', '.');
                }

                let pph = 0;
                if ($('#pph').val()) {
                    const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                    pph = npph.replace(',', '.');
                }

                let bphtb = 0;
                if ($('#bphtb').val()) {
                    const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                    bphtb = nbphtb.replace(',', '.');
                }

                let realisasi = 0;
                if ($('#realisasi').val()) {
                    const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                    realisasi = nrealisasi.replace(',', '.');
                }

                let shm = 0;
                if ($('#shm').val()) {
                    const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                    shm = nshm.replace(',', '.');
                }

                let kanopi = 0;
                if ($('#kanopi').val()) {
                    const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                    kanopi = nkanopi.replace(',', '.');
                }

                let tandon = 0;
                if ($('#tandon').val()) {
                    const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                    tandon = ntandon.replace(',', '.');
                }

                let pompair = 0;
                if ($('#pompair').val()) {
                    const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                    pompair = npompair.replace(',', '.');
                }

                let teralis = 0;
                if ($('#teralis').val()) {
                    const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                    teralis = nteralis.replace(',', '.');
                }

                let tembok = 0;
                if ($('#tembok').val()) {
                    const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                    tembok = ntembok.replace(',', '.');
                }

                let pondasi = 0;
                if ($('#pondasi').val()) {
                    const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                    pondasi = npondasi.replace(',', '.');
                }

                let pijb = 0;
                if ($('#pijb').val()) {
                    const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                    pijb = npijb.replace(',', '.');
                }

                let ppn = 0;
                if ($('#ppn').val()) {
                    const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                    ppn = nppn.replace(',', '.');
                }

                let fee = 0;
                if ($('#fee').val()) {
                    const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                    fee = nfee.replace(',', '.');
                }

                let bayar = 0;
                if ($('#bayar').val()) {
                    const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                    bayar = nbayar.replace(',', '.');
                }

                let nilaiAccKpr = 0;
                if ($(this).val()) {
                    const accKpr = $(this).val().match(/[0-9,]+/g).join([]);
                    nilaiAccKpr = accKpr.replace(',', '.');
                }

                const penambahan = (parseFloat(harga) + parseFloat(nup) + parseFloat(mutu) + parseFloat(kelebihantanah) + parseFloat(sbum));
                const pengurangan1 = (parseFloat(ajbn) + parseFloat(pph) + parseFloat(bphtb) + parseFloat(realisasi));
                const bonus = (parseFloat(shm) + parseFloat(kanopi) + parseFloat(tandon) + parseFloat(pompair) + parseFloat(teralis) + parseFloat(tembok) + parseFloat(pondasi));
                const pengurangan2 = (parseFloat(pijb) + parseFloat(ppn) + parseFloat(fee));

                const hrgtrx = penambahan - pengurangan1 - bonus - pengurangan2;
                $('#harga').val(hrgtrx ? formatRupiah(hrgtrx) : '0');

                const sisaBayar = hrgtrx - parseFloat(bayar) - parseFloat(nilaiAccKpr);
                $('#sisaBayar').val(sisaBayar ? formatRupiah(sisaBayar) : '0');
            });

            cartLS.destroy();
        <?php } ?>

        $(function() {
            loadCartBiayaLain();
            $(".addBiayaLain").on('click', function() {
                if ($('#tanggalNup').val() && $('#biayalain').val() && $('#uraian_lain').val() && $('#nominal_lain').val() && $('#debet_lain').val() || $('#kredit_lain').val()) {
                    const prodList = cartLS.list();
                    const dataBiayaLain = {
                        id: (prodList.length > 0 ? prodList[prodList.length - 1].id + 1 : 1),
                        price: parseInt($('#nominal_lain').val().match(/[0-9]+/g).join([])),
                        name: $('#uraian_lain').val(),
                        biayalain: $('#biayalain').val(),
                        biayalainText: $("#biayalain option:selected").text(),
                        tanggal: $('#tanggalNup').val(),
                        kembali: 0,

                        debetId: $('#debet_lain').val(),
                        debetText: $('#debet_lain option:selected').text(),

                        kreditId: $('#kredit_lain').val(),
                        kreditText: $('#kredit_lain option:selected').text()
                    };
                    cartLS.add(dataBiayaLain, 1);
                    $('#biayalain').val('').selectpicker("refresh");
                    $('#debet_lain').val('').selectpicker("refresh");
                    $('#kredit_lain').val('').selectpicker("refresh");
                    $('#nominal_lain').val('0');
                    $('#uraian_lain').val('');
                    loadCartBiayaLain();
                } else {
                    $("#toastAlert").toast({
                        delay: 2000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oppss!');
                    $(".toast-body>#message").html('Uraian, nominal, debet atau kredit belum ditentukan.');
                }
            });
        });

        $(function() {
            loadCartTagihan();
            $(".addTagihan").on('click', function() {
                if ($('#tagJenis').val() && $('#tagJthTempo').val() && $('#tagNama').val() && $('#tagNominal').val()) {
                    const prodList = cartLS.list();
                    const nom = $('#tagNominal').val().match(/[0-9,]+/g).join([]);
                    const nominal = nom.replace(',', '.');
                    const dataTagihan = {
                        id: (prodList.length > 0 ? prodList[prodList.length - 1].id + 1 : 1),
                        price: parseInt(nominal),
                        name: $('#tagNama').val(),
                        jenis: $('#tagJenis').val(),
                        jenisText: $('#tagJenis option:selected').text(),
                        tanggal: $('#tagJthTempo').val(),
                        keterangan: $('#tagUraian').val()
                    };
                    cartLS.add(dataTagihan, 1);
                    $("#tagJenis option[value='']").prop("selected", true);
                    $('#tagJthTempo').val('');
                    $('#tagNama').val('');
                    $('#tagNominal').val('0');
                    $('#tagUraian').val('');
                    loadCartTagihan();
                } else {
                    $("#toastAlert").toast({
                        delay: 2000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oppss!');
                    $(".toast-body>#message").html('Tanggal, angsuran atau nominal belum ditentukan.');
                }
            });
        });

        $(function() {
            loadCartBiayaLain();
            $(".addBiayaPembatalan").on('click', function() {
                if ($('#uraian_lain').val() && $('#nominal_lain').val() && $('#debet_lain').val() || $('#kredit_lain').val()) {
                    const prodList = cartLS.list();
                    const dataBiayaLain = {
                        id: (prodList.length > 0 ? prodList[prodList.length - 1].id + 1 : 1),
                        price: parseInt($('#nominal_lain').val().match(/[0-9]+/g).join([])),
                        name: $('#uraian_lain').val(),
                        tanggal: $('#tanggalBayarPicker').val(),
                        biayalain: $('#biayalain').val(),
                        biayalainText: $("#biayalain option:selected").text(),
                        kembali: 1,

                        debetId: $('#debet_lain').val(),
                        debetText: $('#debet_lain').val() ? $('#debet_lain option:selected').text() : '-',

                        kreditId: $('#kredit_lain').val(),
                        kreditText: $('#kredit_lain').val() ? $('#kredit_lain option:selected').text() : '-',
                    };
                    cartLS.add(dataBiayaLain, 1);
                    $('#biayalain').val('').selectpicker("refresh");
                    $('#debet_lain').val('').selectpicker("refresh");
                    $('#kredit_lain').val('').selectpicker("refresh");
                    $('#nominal_lain').val('0');
                    $('#uraian_lain').val('');
                    loadCartBiayaLain();
                } else {
                    $("#toastAlert").toast({
                        delay: 2000
                    });
                    $("#toastAlert").toast('show');
                    $("#infoToast").html('Oppss!');
                    $(".toast-body>#message").html('Uraian, nominal, debet atau kredit belum ditentukan.');
                }
            });
        });

        function insertPenjualanUnit() {
            if ($('#tanggalPicker').val() && $('#jenisSelect').val() && $('#nomor').val() && $('#customerselect').val() && $('#unitselect').val() && $('#harga').val()) {
                r = confirm('Yakin ingin melanjutkan?');
                if (r == true) {
                    let hargariil = 0;
                    if ($('#hargariil').val()) {
                        const nhargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                        hargariil = nhargariil.replace(',', '.');
                    }

                    let harga = 0;
                    if ($('#harga').val()) {
                        const nharga = $('#harga').val().match(/[0-9,]+/g).join([]);
                        harga = nharga.replace(',', '.');
                    }

                    let nup = 0;
                    if ($('#nnup').val()) {
                        const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                        nup = nnup.replace(',', '.');
                    }

                    let kelebihantanah = 0;
                    if ($('#tanahLebih').val()) {
                        const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                        kelebihantanah = tanahLebih.replace(',', '.');
                    }

                    let mutu = 0;
                    if ($('#mutu').val()) {
                        const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                        mutu = unit_mutu.replace(',', '.');
                    }

                    let sbum = 0;
                    if ($('#sbum').val()) {
                        const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                        sbum = nsbum.replace(',', '.');
                    }

                    let ajbn = 0;
                    if ($('#ajbn').val()) {
                        const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                        ajbn = najbn.replace(',', '.');
                    }

                    let pph = 0;
                    if ($('#pph').val()) {
                        const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                        pph = npph.replace(',', '.');
                    }

                    let bphtb = 0;
                    if ($('#bphtb').val()) {
                        const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                        bphtb = nbphtb.replace(',', '.');
                    }

                    let realisasi = 0;
                    if ($('#realisasi').val()) {
                        const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                        realisasi = nrealisasi.replace(',', '.');
                    }

                    let shm = 0;
                    if ($('#shm').val()) {
                        const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                        shm = nshm.replace(',', '.');
                    }

                    let kanopi = 0;
                    if ($('#kanopi').val()) {
                        const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                        kanopi = nkanopi.replace(',', '.');
                    }

                    let tandon = 0;
                    if ($('#tandon').val()) {
                        const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                        tandon = ntandon.replace(',', '.');
                    }

                    let pompair = 0;
                    if ($('#pompair').val()) {
                        const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                        pompair = npompair.replace(',', '.');
                    }

                    let teralis = 0;
                    if ($('#teralis').val()) {
                        const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                        teralis = nteralis.replace(',', '.');
                    }

                    let tembok = 0;
                    if ($('#tembok').val()) {
                        const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                        tembok = ntembok.replace(',', '.');
                    }

                    let pondasi = 0;
                    if ($('#pondasi').val()) {
                        const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                        pondasi = npondasi.replace(',', '.');
                    }

                    let pijb = 0;
                    if ($('#pijb').val()) {
                        const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                        pijb = npijb.replace(',', '.');
                    }

                    let ppn = 0;
                    if ($('#ppn').val()) {
                        const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                        ppn = nppn.replace(',', '.');
                    }

                    let fee = 0;
                    if ($('#fee').val()) {
                        const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                        fee = nfee.replace(',', '.');
                    }

                    let bayar = 0;
                    if ($('#bayar').val()) {
                        const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                        bayar = nbayar.replace(',', '.');
                    }

                    let nilaiPengajuanKpr = 0;
                    if ($('#nilaiPengajuanKpr').val()) {
                        const nnilaiPengajuanKpr = $('#nilaiPengajuanKpr').val().match(/[0-9,]+/g).join([]);
                        nilaiPengajuanKpr = nnilaiPengajuanKpr.replace(',', '.');
                    }

                    let nilaiAccKpr = 0;
                    if ($('#nilaiAccKpr').val()) {
                        const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                        nilaiAccKpr = accKpr.replace(',', '.');
                    }

                    let sisaBayar = 0;
                    if ($('#sisaBayar').val()) {
                        const nsisaBayar = $('#sisaBayar').val().match(/[0-9,]+/g).join([]);
                        sisaBayar = nsisaBayar.replace(',', '.');
                    }

                    $.ajax({
                        url: '/dashboard/insertPenjualan',
                        method: 'post',
                        data: {
                            jenis: $('#jenisSelect').val(),
                            kaliangsur: $('#kaliangsur').val(),
                            nomor: $('#nomor').val(),
                            tanggal: $('#tanggalPicker').val(),
                            marketing: $('#marketing').val(),
                            customer: $('#customerselect').val(),
                            unit: $('#unitselect').val(),

                            hargariil: $('#hargariil').val().match(/[0-9,]+/g).join([]),
                            nup: nup,
                            mutu: mutu,
                            tanahLebih: kelebihantanah,
                            sbum: sbum,
                            ajbn: ajbn,
                            pph: pph,
                            bphtb: bphtb,
                            realisasi: realisasi,
                            shm: shm,
                            kanopi: kanopi,
                            tandon: tandon,
                            pompair: pompair,
                            teralis: teralis,
                            tembok: tembok,
                            pondasi: pondasi,
                            pijb: pijb,
                            ppn: ppn,
                            fee: fee,

                            harga: $('#harga').val().match(/[0-9,]+/g).join([]),
                            kreditHarga: $('#kredit_harga').val(),
                            bayar: bayar,
                            debetBayar: $('#debet_bayar').val(),
                            kpr: $('#kpr').val(),

                            tglPengajuanKpr: $('#tglPengajuanKpr').val(),
                            nilaiPengajuanKpr: nilaiPengajuanKpr,
                            tglAccKpr: $('#tglAccKpr').val(),
                            nilaiAccKpr: nilaiAccKpr,
                            tglRealisasiKpr: $('#tglRealisasiKpr').val(),
                            debetKpr: $('#debetKpr').val(),

                            sisaBayar: sisaBayar,
                            debetSisa: $('#debet_sisa').val(),
                            catatan: $('#catatan').val(),
                            data: JSON.stringify(cartLS.list())
                        },
                        dataType: 'json',
                        // beforeSend: function() {
                        //     $("#loadLoader").show();
                        // },
                        success: function(response) {
                            // console.log(response);
                            if (response.status) {
                                cartLS.destroy();
                                loadCartBiayaLain();
                                location.replace('/dashboard/penjualanunit');

                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Sukses!');
                                $(".toast-body>#message").html('Data berhasil disimpan.');
                            } else {
                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oppss!');
                                $(".toast-body>#message").html('Mohon periksa kembali, ada beberapa field yang belum terisi.');
                            }
                        },
                        // complete: function(response) {
                        //     if (response) {
                        //         $("#loadLoader").hide();
                        //     }
                        // }
                    });
                }
            } else {
                $("#toastAlert").toast({
                    delay: 2000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oppss!');
                $(".toast-body>#message").html('Jenis, tanggal, nomor, customer atau unit belum diisi.');
            }
        }

        function updatePenjualanUnit() {
            if ($('#jenisSelect').val() && $('#puId').val() && $('#nomor').val() && $('#customerselect').val() && $('#unitselect').val() && $('#harga').val()) {
                r = confirm('Yakin ingin melanjutkan?');
                if (r == true) {
                    let hargariil = 0;
                    if ($('#hargariil').val()) {
                        const nhargariil = $('#hargariil').val().match(/[0-9,]+/g).join([]);
                        hargariil = nhargariil.replace(',', '.');
                    }

                    let harga = 0;
                    if ($('#harga').val()) {
                        const nharga = $('#harga').val().match(/[0-9,]+/g).join([]);
                        harga = nharga.replace(',', '.');
                    }

                    let nup = 0;
                    if ($('#nnup').val()) {
                        const nnup = $('#nnup').val().match(/[0-9,]+/g).join([]);
                        nup = nnup.replace(',', '.');
                    }

                    let kelebihantanah = 0;
                    if ($('#tanahLebih').val()) {
                        const tanahLebih = $('#tanahLebih').val().match(/[0-9,]+/g).join([]);
                        kelebihantanah = tanahLebih.replace(',', '.');
                    }

                    let mutu = 0;
                    if ($('#mutu').val()) {
                        const unit_mutu = $('#mutu').val().match(/[0-9,]+/g).join([]);
                        mutu = unit_mutu.replace(',', '.');
                    }

                    let sbum = 0;
                    if ($('#sbum').val()) {
                        const nsbum = $('#sbum').val().match(/[0-9,]+/g).join([]);
                        sbum = nsbum.replace(',', '.');
                    }

                    let ajbn = 0;
                    if ($('#ajbn').val()) {
                        const najbn = $('#ajbn').val().match(/[0-9]+/g).join([]);
                        ajbn = najbn.replace(',', '.');
                    }

                    let pph = 0;
                    if ($('#pph').val()) {
                        const npph = $('#pph').val().match(/[0-9]+/g).join([]);
                        pph = npph.replace(',', '.');
                    }

                    let bphtb = 0;
                    if ($('#bphtb').val()) {
                        const nbphtb = $('#bphtb').val().match(/[0-9]+/g).join([]);
                        bphtb = nbphtb.replace(',', '.');
                    }

                    let realisasi = 0;
                    if ($('#realisasi').val()) {
                        const nrealisasi = $('#realisasi').val().match(/[0-9]+/g).join([]);
                        realisasi = nrealisasi.replace(',', '.');
                    }

                    let shm = 0;
                    if ($('#shm').val()) {
                        const nshm = $('#shm').val().match(/[0-9]+/g).join([]);
                        shm = nshm.replace(',', '.');
                    }

                    let kanopi = 0;
                    if ($('#kanopi').val()) {
                        const nkanopi = $('#kanopi').val().match(/[0-9]+/g).join([]);
                        kanopi = nkanopi.replace(',', '.');
                    }

                    let tandon = 0;
                    if ($('#tandon').val()) {
                        const ntandon = $('#tandon').val().match(/[0-9]+/g).join([]);
                        tandon = ntandon.replace(',', '.');
                    }

                    let pompair = 0;
                    if ($('#pompair').val()) {
                        const npompair = $('#pompair').val().match(/[0-9]+/g).join([]);
                        pompair = npompair.replace(',', '.');
                    }

                    let teralis = 0;
                    if ($('#teralis').val()) {
                        const nteralis = $('#teralis').val().match(/[0-9]+/g).join([]);
                        teralis = nteralis.replace(',', '.');
                    }

                    let tembok = 0;
                    if ($('#tembok').val()) {
                        const ntembok = $('#tembok').val().match(/[0-9]+/g).join([]);
                        tembok = ntembok.replace(',', '.');
                    }

                    let pondasi = 0;
                    if ($('#pondasi').val()) {
                        const npondasi = $('#pondasi').val().match(/[0-9]+/g).join([]);
                        pondasi = npondasi.replace(',', '.');
                    }

                    let pijb = 0;
                    if ($('#pijb').val()) {
                        const npijb = $('#pijb').val().match(/[0-9]+/g).join([]);
                        pijb = npijb.replace(',', '.');
                    }

                    let ppn = 0;
                    if ($('#ppn').val()) {
                        const nppn = $('#ppn').val().match(/[0-9]+/g).join([]);
                        ppn = nppn.replace(',', '.');
                    }

                    let fee = 0;
                    if ($('#fee').val()) {
                        const nfee = $('#fee').val().match(/[0-9]+/g).join([]);
                        fee = nfee.replace(',', '.');
                    }

                    let bayar = 0;
                    if ($('#bayar').val()) {
                        const nbayar = $('#bayar').val().match(/[0-9]+/g).join([]);
                        bayar = nbayar.replace(',', '.');
                    }

                    let nilaiPengajuanKpr = 0;
                    if ($('#nilaiPengajuanKpr').val()) {
                        const nnilaiPengajuanKpr = $('#nilaiPengajuanKpr').val().match(/[0-9,]+/g).join([]);
                        nilaiPengajuanKpr = nnilaiPengajuanKpr.replace(',', '.');
                    }

                    let nilaiAccKpr = 0;
                    if ($('#nilaiAccKpr').val()) {
                        const accKpr = $('#nilaiAccKpr').val().match(/[0-9,]+/g).join([]);
                        nilaiAccKpr = accKpr.replace(',', '.');
                    }

                    let sisaBayar = 0;
                    if ($('#sisaBayar').val()) {
                        const nsisaBayar = $('#sisaBayar').val().match(/[0-9,]+/g).join([]);
                        sisaBayar = nsisaBayar.replace(',', '.');
                    }

                    $.ajax({
                        url: '/dashboard/updatePenjualan',
                        method: 'post',
                        data: {
                            puId: $('#puId').val(),
                            tagId: $('#tagId').val(),

                            jenis: $('#jenisSelect').val(),
                            kaliangsur: $('#kaliangsur').val(),
                            nomor: $('#nomor').val(),
                            tanggal: $('#tanggalPicker').val(),
                            marketing: $('#marketing').val(),
                            customer: $('#customerselect').val(),
                            unit: $('#unitselect').val(),

                            hargariil: $('#hargariil').val().match(/[0-9,]+/g).join([]),
                            nup: nup,
                            mutu: mutu,
                            tanahLebih: kelebihantanah,
                            sbum: sbum,
                            ajbn: ajbn,
                            pph: pph,
                            bphtb: bphtb,
                            realisasi: realisasi,
                            shm: shm,
                            kanopi: kanopi,
                            tandon: tandon,
                            pompair: pompair,
                            teralis: teralis,
                            tembok: tembok,
                            pondasi: pondasi,
                            pijb: pijb,
                            ppn: ppn,
                            fee: fee,

                            harga: $('#harga').val().match(/[0-9,]+/g).join([]),
                            kreditHarga: $('#kredit_harga').val(),
                            bayar: $('#bayar').val(),
                            debetBayar: $('#debet_bayar').val(),
                            kpr: $('#kpr').val(),

                            tglPengajuanKpr: $('#tglPengajuanKpr').val(),
                            nilaiPengajuanKpr: nilaiPengajuanKpr,
                            tglAccKpr: $('#tglAccKpr').val(),
                            nilaiAccKpr: nilaiAccKpr,
                            tglRealisasiKpr: $('#tglRealisasiKpr').val(),
                            debetKpr: $('#debetKpr').val(),

                            sisaBayar: sisaBayar,
                            debetSisa: $('#debet_sisa').val(),
                            catatan: $('#catatan').val(),
                            data: JSON.stringify(cartLS.list())
                        },
                        dataType: 'json',
                        // beforeSend: function() {
                        //     $("#loadLoader").show();
                        // },
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                loadCartBiayaLain();

                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Sukses!');
                                $(".toast-body>#message").html('Perubahan berhasil disimpan.');

                                location.reload();
                            } else {
                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oppss!');
                                $(".toast-body>#message").html('Mohon periksa kembali, ada beberapa field yang belum terisi.');
                            }
                        },
                        // complete: function(response) {
                        //     if (response) {
                        //         $("#loadLoader").hide();
                        //     }
                        // }
                    });
                }
            } else {
                $("#toastAlert").toast({
                    delay: 2000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oppss!');
                $(".toast-body>#message").html('Jenis, nomor, customer, unit atau harga belum diisi.');
            }
        }

        function bayarPiutangPenjualan() {
            if ($('#puId').val() && $('#nomor').val()) {
                r = confirm('Yakin ingin melanjutkan?');
                if (r == true) {
                    let bayarKpr = 0
                    if ($('#bayarKpr').val()) {
                        bayarKpr = $('#bayarKpr').val().match(/[0-9]+/g).join([])
                    }

                    let bayarCustomer = 0
                    if ($('#bayarCustomer').val()) {
                        bayarCustomer = $('#bayarCustomer').val().match(/[0-9]+/g).join([])
                    }

                    let piutangCustomer = 0
                    if ($('#piutangCustomer').val()) {
                        piutangCustomer = $('#piutangCustomer').val().match(/[0-9]+/g).join([])
                    }

                    let lebihCustomer = 0
                    if ($('#lebihCustomer').val()) {
                        lebihCustomer = $('#lebihCustomer').val().match(/[0-9]+/g).join([])
                    }
                    $.ajax({
                        url: '/dashboard/bayarPiutangPenjualan',
                        method: 'post',
                        data: {
                            puId: $('#puId').val(),
                            nomor: $('#nomor').val(),
                            tanggal: $('#tanggalBayarPicker').val(),
                            bayarKpr: bayarKpr,
                            debetBayarKpr: $('#debetBayarKpr').val(),
                            kreditBayarKpr: $('#kreditBayarKpr').val(),
                            bayarCustomer: bayarCustomer,
                            debetBayarCustomer: $('#debetBayarCustomer').val(),
                            kreditBayarCustomer: $('#kreditBayarCustomer').val(),
                            piutangCustomer: piutangCustomer,
                            catatan: $('#catatan').val(),

                            lebihCustomer: lebihCustomer,
                            debetLebihCustomer: $('#debetLebihCustomer').val(),
                            kreditLebihCustomer: $('#kreditLebihCustomer').val(),
                            catatanLebihBayar: $('#catatanLebihBayar').val(),

                            data: JSON.stringify(cartLS.list())
                        },
                        dataType: 'json',
                        // beforeSend: function() {
                        //     $("#loadLoader").show();
                        // },
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                loadCartBiayaLain();

                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Sukses!');
                                $(".toast-body>#message").html('Data berhasil disimpan.');

                                location.reload();
                            } else {
                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oppss!');
                                $(".toast-body>#message").html('Mohon periksa kembali, ada beberapa field yang belum terisi.');
                            }
                        },
                        // complete: function(response) {
                        //     if (response) {
                        //         $("#loadLoader").hide();
                        //     }
                        // }
                    });
                }
            } else {
                $("#toastAlert").toast({
                    delay: 2000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oppss!');
                $(".toast-body>#message").html('Nomor, customer, unit atau harga belum diisi.');
            }
        }

        function updateBayarPiutangPenjualan() {
            if ($('#puId').val() && $('#pjId').val() && $('#nomor').val()) {
                r = confirm('Yakin ingin melanjutkan?');
                if (r == true) {
                    let byrKpr = 0;
                    if ($('#bayarKpr').val()) {
                        byrKpr = $('#bayarKpr').val().match(/[0-9]+/g).join([]);
                    }

                    let byrCust = 0
                    if ($('#bayarCustomer').val()) {
                        byrCust = $('#bayarCustomer').val().match(/[0-9-]+/g).join([]);
                    }

                    let lbhCust = 0
                    if ($('#lebihCustomer').val()) {
                        lbhCusr = $('#lebihCustomer').val().match(/[0-9]+/g).join([]);
                    }
                    $.ajax({
                        url: '/dashboard/updateBayarPiutangPenjualan',
                        method: 'post',
                        data: {
                            puId: $('#puId').val(),
                            pjId: $('#pjId').val(),
                            nomor: $('#nomor').val(),
                            tanggal: $('#tanggalBayarPicker').val(),
                            bayarKpr: byrKpr,
                            debetBayarKpr: $('#debetBayarKpr').val(),
                            kreditBayarKpr: $('#kreditBayarKpr').val(),
                            bayarCustomer: byrCust,
                            debetBayarCustomer: $('#debetBayarCustomer').val(),
                            kreditBayarCustomer: $('#kreditBayarCustomer').val(),
                            catatan: $('#catatan').val(),

                            lebihCustomer: lbhCust,
                            debetLebihCustomer: $('#debetLebihCustomer').val(),
                            kreditLebihCustomer: $('#kreditLebihCustomer').val(),
                            catatanLebihBayar: $('#catatanLebihBayar').val(),

                            data: JSON.stringify(cartLS.list())
                        },
                        dataType: 'json',
                        // beforeSend: function() {
                        //     $("#loadLoader").show();
                        // },
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                loadCartBiayaLain();

                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Sukses!');
                                $(".toast-body>#message").html('Perubahan berhasil disimpan.');

                                location.reload();
                            } else {
                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oppss!');
                                $(".toast-body>#message").html('Mohon periksa kembali, ada beberapa field yang belum terisi.');
                            }
                        },
                        // complete: function(response) {
                        //     if (response) {
                        //         $("#loadLoader").hide();
                        //     }
                        // }
                    });
                }
            } else {
                $("#toastAlert").toast({
                    delay: 2000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oppss!');
                $(".toast-body>#message").html('Nomor, customer, unit atau harga belum diisi.');
            }
        }

        if ($("#unitselect").val()) {
            const idUnit = $("#unitselect").val();
            $.ajax({
                url: '/dashboard/hppUnitJson/' + idUnit,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#hpp').html(formatRupiah(response.nilaiTanah));
                }
            });
        }

        $("#unitselect").on('change', function() {
            const idUnit = $(this).val();
            $.ajax({
                url: '/dashboard/hppUnitJson/' + idUnit,
                method: 'get',
                dataType: 'json',
                success: function(response) {
                    $('#hpp').html(formatRupiah(response.nilaiTanah));
                }
            });
        });

        function pembatalanPenjualanUnit() {
            if ($('#puId').val()) {
                r = confirm('Yakin ingin melanjutkan?');
                if (r == true) {
                    let harga = 0;
                    if ($('#harga').val()) {
                        harga = $('#harga').val().match(/[0-9]+/g).join([]);
                    }
                    $.ajax({
                        url: '/dashboard/pembatalanPenjualanUnit',
                        method: 'post',
                        data: {
                            puId: $('#puId').val(),
                            data: JSON.stringify(cartLS.list())
                        },
                        dataType: 'json',
                        // beforeSend: function() {
                        //     $("#loadLoader").show();
                        // },
                        success: function(response) {
                            if (response.status) {
                                cartLS.destroy();
                                loadCartBiayaLain();
                                location.replace('/dashboard/penjualanunit');

                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Sukses!');
                                $(".toast-body>#message").html('Data berhasil disimpan.');
                            } else {
                                $("#toastAlert").toast({
                                    delay: 2000
                                });
                                $("#toastAlert").toast('show');
                                $("#infoToast").html('Oppss!');
                                $(".toast-body>#message").html('Mohon periksa kembali, ada beberapa field yang belum terisi.');
                            }
                        },
                        // complete: function(response) {
                        //     if (response) {
                        //         $("#loadLoader").hide();
                        //     }
                        // }
                    });
                }
            } else {
                $("#toastAlert").toast({
                    delay: 2000
                });
                $("#toastAlert").toast('show');
                $("#infoToast").html('Oppss!');
                $(".toast-body>#message").html('Jenis, nomor, customer, unit atau harga belum diisi.');
            }
        }
    </script>

    <div class="toast" id="toastAlert" style="position: fixed; bottom: 50px; right: 23px; width: 300px;">
        <div class="toast-header bg-warning text-white">
            <strong class="mr-auto"><span id="infoToast"></span></strong>
            <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <div id="message" class="font-italic"></div>
        </div>
    </div>
</body>

</html>