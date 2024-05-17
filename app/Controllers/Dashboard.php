<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

use PHPExcel;
use PHPExcel_IOFactory;

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Dashboard extends BaseController
{
    public function index()
    {
        $userModel = new \App\Models\UserModel();
        // $barangModel = new \App\Models\BarangModel();
        // $barang = $barangModel->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
        //     ->join('kategori_barang', 'kategori_barang.kabar_id = barang.barang_kategori', 'left')
        //     ->join('user', 'user.usr_id = barang.barang_user', 'left')
        //     ->join('rekening', 'rekening.rek_id = barang.barang_rekening', 'left')
        //     ->where('barang.barang_kategori !=', 7)
        //     ->select('barang.*, satuan.satuan_id, satuan.satuan_nama, kategori_barang.kabar_id, kategori_barang.kabar_nama, user.usr_id, user.usr_nama, rekening.rek_id, rekening.rek_kode, rekening.rek_nama')
        //     ->orderBy('barang.barang_id', 'DESC')->findAll();

        $data = [
            'title_bar'     => 'Dashboard',
            'barang'        => [],
            'session'       => $userModel->find($this->session->get('usr_id')),
            // 'barangModel'   => $barangModel
        ];
        return view('dashboard/home', $data);
    }

    public function profile()
    {
        $userId = $this->session->get('usr_id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        if ($user) {
            $data = [
                'title_bar'     => 'Profil Saya',
                'user'          => $user,
                'validation'    => $this->validation
            ];
            return view('dashboard/profile', $data);
        } else {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }
    }

    public function updateProfile()
    {
        $request = \Config\Services::request();
        $userModel = new \App\Models\UserModel();
        $old = $userModel->find($this->session->get('usr_id'));
        if ($old['usr_username'] == $request->getVar('username')) {
            $rule_username = 'required';
        } else {
            $rule_username = 'required|is_unique[user.usr_username]';
        }

        if ($old['usr_email'] == $request->getVar('email')) {
            $rule_email = 'required';
        } else {
            $rule_email = 'required|is_unique[user.usr_email]';
        }

        $validate = $this->validate([
            'id' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'username' => [
                'rules'     => $rule_username,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'ini sudah digunakan.'
                ]
            ],
            'email' => [
                'rules'     => $rule_email,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'ini sudah digunakan.'
                ]
            ],
            'nama' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nohp' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/profile')->withInput();
        }

        $photo = $request->getFile('photo');
        if ($photo->getError() == 4) {
            $filePhoto = $old['usr_photo'];
        } else {
            if ($old['usr_photo']) {
                unlink('assets/img/' . $old['usr_photo']);
            }
            $filePhoto = $photo->getRandomName();
            $photo->move('assets/img/', $filePhoto);
        }

        $password = $request->getVar('password');
        $query = $userModel->update(['usr_id' => $request->getVar('id')], [
            'usr_username'      => url_title($request->getVar('username'), '', true),
            'usr_email'         => $request->getVar('email'),
            'usr_nama'          => $request->getVar('nama'),
            'usr_nohp'          => $request->getVar('nohp'),
            'usr_photo'         => $filePhoto,
            'usr_password'      => $password ? password_hash($password, PASSWORD_DEFAULT) : $old['usr_password'],
            'usr_token'         => random_string('alnum', 77)
        ]);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil diperbarui.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/profile');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal diperbarui.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/profile')->withInput();
        }
    }

    public function jamkerja()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("masterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $jamkerjaModel = new \App\Models\JamkerjaModel();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;
        $jamkerja = $jamkerjaModel->orderBy('jk_id', 'DESC');
        $data = [
            'title_bar'     => 'Data Jam Kerja',
            'jamkerja'      => $jamkerja->paginate(100, 'view'),
            'roleModel'     => new \App\Models\RoleModel(),
            'bagianModel'   => new \App\Models\BagianModel(),
            'pager'         => $jamkerjaModel->pager,
            'current'       => $currentPage,
            'totalRows'     => count($jamkerja->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/jamkerja', $data);
    }

    public function jamKerjaJson($id)
    {
        $jamkerjaModel = new \App\Models\JamkerjaModel();
        $timework = $jamkerjaModel->find($id);
        if ($timework) {
            return json_encode($timework, true);
        } else {
            return json_encode(['status' => false], true);
        }
    }

    public function insertTime()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertMasterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $validate = $this->validate([
            'mulai' => [
                'rules'        => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'selesai' => [
                'rules'        => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/jamkerja')->withInput();
        }

        $data = [
            'jk_mulai'      => $this->request->getPost('mulai'),
            'jk_selesai'    => $this->request->getPost('selesai')
        ];

        $jamkerjaModel = new \App\Models\JamkerjaModel();
        $query = $jamkerjaModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/jamkerja');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/jamkerja')->withInput();
        }
    }

    public function updateTime()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateMasterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $id = $this->request->getPost('id');
        $jamkerjaModel = new \App\Models\JamkerjaModel();

        $validate = $this->validate([
            'mulai' => [
                'rules'        => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'selesai' => [
                'rules'        => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/jamkerja')->withInput();
        }

        $data = [
            'jk_mulai'      => $this->request->getPost('mulai'),
            'jk_selesai'    => $this->request->getPost('selesai'),
        ];

        $query = $jamkerjaModel->update(['jk_id' => $id], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/jamkerja');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/jamkerja')->withInput();
        }
    }

    public function deleteTime()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteMasterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $id = $this->request->getPost('id');
        $jamkerjaModel = new \App\Models\JamkerjaModel();
        $timework = $jamkerjaModel->find($id);
        if ($timework) {
            if ($timework['jk_id'] >= 4 && $timework['jk_id'] <= 6) {
                session()->setFlashdata('pesan', '<div class="alert alert-warning alert-dismissible fade show" role="alert">Jam kerja tidak dapat dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/jamkerja')->withInput();
            } else {
                $query = $jamkerjaModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/jamkerja');
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/jamkerja')->withInput();
                }
            }
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/jamkerja')->withInput();
        }
    }

    public function punishment()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("masterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $punishmentModel = new \App\Models\PunishmentModel();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;
        $punishment = $punishmentModel->join('user', 'user.usr_id = punishment.pun_user', 'left')
            ->select('punishment.*, user.usr_nama')
            ->orderBy('punishment.pun_id', 'DESC');
        $data = [
            'title_bar'     => 'Data Punishment',
            'punishment'    => $punishment->paginate(100, 'view'),
            'roleModel'     => new \App\Models\RoleModel(),
            'bagianModel'   => new \App\Models\BagianModel(),
            'pager'         => $punishmentModel->pager,
            'current'       => $currentPage,
            'totalRows'     => count($punishment->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/punishment', $data);
    }

    public function punishmentJson($id)
    {
        $punishmentModel = new \App\Models\PunishmentModel();
        $punishment = $punishmentModel->find($id);
        return json_encode($punishment, true);
    }

    public function addPunishment()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addMasterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Punishment Baru',
            'roleModel'     => new \App\Models\RoleModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/addPunishment', $data);
    }

    public function insertPunishment()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertMasterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'punishment' => [
                'rules'     => 'required|is_unique[punishment.pun_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/addPunishment')->withInput();
        }

        $data = [
            'pun_nama'      => $request->getVar('punishment'),
            'pun_user'      => $request->getVar('user'),
            'pun_waktu'     => $request->getVar('waktu'),
            'pun_potongan'  => $request->getVar('potongan') ? str_replace('.', '', $request->getVar('potongan')) : 0,
            'pun_deskripsi' => $request->getVar('keterangan'),
        ];

        $punishmentModel = new \App\Models\PunishmentModel();
        $query = $punishmentModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/punishment');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addPunishment')->withInput();
        }
    }

    public function editPunishment($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editMasterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $punishmentModel = new \App\Models\PunishmentModel();
        $punishment = $punishmentModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Punishment',
            'punishment'    => $punishment,
            'roleModel'     => new \App\Models\RoleModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/editPunishment', $data);
    }

    public function updatePunishment()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateMasterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $punishmentModel = new \App\Models\PunishmentModel();
        $old = $punishmentModel->find($request->getVar('id'));

        if ($old['pun_nama'] == $request->getVar('punishment')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[punishment.pun_nama]';
        }

        $validate = $this->validate([
            'punishment' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editPunishment/' . $request->getVar('id'))->withInput();
        }

        $data = [
            'pun_nama'      => $request->getVar('punishment'),
            'pun_user'      => $request->getVar('user'),
            'pun_waktu'     => $request->getVar('waktu'),
            'pun_potongan'  => $request->getVar('potongan') ? str_replace('.', '', $request->getVar('potongan')) : 0,
            'pun_deskripsi' => $request->getVar('keterangan'),
        ];

        $query = $punishmentModel->update(['pun_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/punishment');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editPunishment/' . $request->getVar('id'))->withInput();
        }
    }

    public function deletePunishment()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteMasterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $punishmentModel = new \App\Models\PunishmentModel();
            $punishment = $punishmentModel->find($id);
            if ($punishment) {
                $query = $punishmentModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function users()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("users", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $userModel = new \App\Models\UserModel();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;
        $users = $userModel->orderBy('usr_id', 'DESC');
        $data = [
            'title_bar'     => 'Data User',
            'users'         => $users->paginate(100, 'view'),
            'roleModel'     => new \App\Models\RoleModel(),
            'bagianModel'   => new \App\Models\BagianModel(),
            'pager'         => $userModel->pager,
            'current'       => $currentPage,
            'totalRows'        => count($users->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/users', $data);
    }

    public function addUser()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addUser", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'User Baru',
            'roleModel'     => new \App\Models\RoleModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/addUser', $data);
    }

    public function insertUser()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertUser", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'nama' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nohp' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'ini sudah digunakan.'
                ]
            ],
            'status' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'bagian' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'password' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/addUser')->withInput();
        }

        $userModel = new \App\Models\UserModel();
        if ($request->getVar('username')) {
            $username = url_title($request->getVar('username'), '', true);
            $checkUsername = $userModel->where('usr_username', $username)->first();
            if ($checkUsername) {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Username telah digunakan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/addUser')->withInput();
            } else {
                $outUsername = $username;
            }
        } else {
            $createUsername = url_title(substr(str_replace(' ', '', $request->getVar('nama')), 0, 6), '', true);
            $checkUsername = $userModel->where('usr_username', $createUsername)->first();
            if ($checkUsername) {
                $outUsername = $createUsername . ($checkUsername['usr_id'] + 1);
            } else {
                $outUsername = $createUsername;
            }
        }
        $password = $request->getVar('password');
        $data = [
            'usr_username'      => $outUsername,
            'usr_email'         => $request->getVar('email'),
            'usr_nama'          => $request->getVar('nama'),
            'usr_nohp'          => $request->getVar('nohp'),
            'usr_password'      => password_hash($password, PASSWORD_DEFAULT),
            'usr_aktif'         => $request->getVar('status'),
            'usr_role'          => 2,
            'usr_jamkerja'      => $request->getVar('jamkerja'),
            'usr_jamkerja2'     => $request->getVar('jamkerja2'),
            'usr_bagian'        => $request->getVar('bagian'),
            'usr_token'         => random_string('alnum', 77)
        ];
        $userModel = new \App\Models\UserModel();
        $query = $userModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/users');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addUser')->withInput();
        }
    }

    public function editUser($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editUser", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui User',
            'user'          => $user,
            'roleModel'     => new \App\Models\RoleModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/editUser', $data);
    }

    public function updateUser()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateUser", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $userModel = new \App\Models\UserModel();
        $old = $userModel->find($request->getVar('id'));

        $validate = $this->validate([
            'id' => [
                'rules'     => 'required'
            ],
            'nama' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nohp' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'No HP sudah digunakan.'
                ]
            ],
            'status' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'bagian' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editUser/' . $request->getVar('id'))->withInput();
        }

        $username = $request->getVar('username');
        $email = $request->getVar('email');
        if ($username) {
            if ($username != $old['usr_username']) {
                $checkUsername = $userModel->where('usr_username', $username)->first();
                if ($checkUsername) {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Username telah digunakan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/editUser/' . $request->getVar('id'))->withInput();
                } else {
                    $outUsername = url_title($username, '', true);
                }
            } else {
                $outUsername = $old['usr_username'];
            }
        } else {
            $createUsername = url_title(substr(str_replace(' ', '', $request->getVar('nama')), 0, 6), '', true);
            $checkUsername = $userModel->where('usr_username', $createUsername)->first();
            if ($checkUsername) {
                $outUsername = $createUsername . ($checkUsername['usr_id'] + 1);
            } else {
                $outUsername = $createUsername;
            }
        }
        if ($email != '' && $email != $old['usr_email']) {
            $checkEmail = $userModel->where('usr_email', $email)->first();
            if ($checkEmail) {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Email telah digunakan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/editUser/' . $request->getVar('id'))->withInput();
            }
        }
        $password = $request->getVar('password');
        $query = $userModel->update(['usr_id' => $request->getVar('id')], [
            'usr_username'      => $outUsername,
            'usr_email'         => $email,
            'usr_nama'          => $request->getVar('nama'),
            'usr_nohp'          => $request->getVar('nohp'),
            'usr_aktif'         => $request->getVar('status'),
            'usr_jamkerja'      => $request->getVar('jamkerja'),
            'usr_jamkerja2'     => $request->getVar('jamkerja2'),
            'usr_bagian'        => $request->getVar('bagian'),
            'usr_password'      => $password ? password_hash($password, PASSWORD_DEFAULT) : $old['usr_password'],
            'usr_token'         => random_string('alnum', 77)
        ]);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil diperbarui.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/users');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal diperbarui.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editUser/' . $request->getVar('id'))->withInput();
        }
    }

    public function deleteUser()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteUser", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($id);
            if ($user) {
                if ($user['usr_id'] != 1) {
                    $query = $userModel->delete($id);
                    if ($query) {
                        if ($user['usr_photo']) {
                            unlink('assets/img/' . $user['usr_photo']);
                        }
                        session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                        return '<script>window.history.go(-1);</script>';
                    } else {
                        session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                        return '<script>window.history.go(-1);</script>';
                    }
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">User ini tidak dapat dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function bagian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("bagian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $bagianModel = new \App\Models\BagianModel();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;
        $bagians = $bagianModel->orderBy('bagian_id', 'DESC');
        $data = [
            'title_bar'     => 'Data Bagian',
            'bagian'        => $bagians->paginate(25, 'view'),
            'bagianModel'   => $bagianModel,
            'pager'         => $bagianModel->pager,
            'current'       => $currentPage,
            'totalRows'        => count($bagians->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/bagian', $data);
    }

    public function addBagian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addBagian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Tambah Bagian',
            'bagianModel'   => new \App\Models\BagianModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/addBagian', $data);
    }

    public function insertBagian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertBagian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'nama' => [
                'rules'     => 'required|is_unique[bagian.bagian_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/addBagian')->withInput();
        }

        $akses = $request->getVar('bagian_akses');
        if ($akses) {
            foreach ($akses as $row => $value) {
                $hakAkses[] = $value;
            }
        }

        $data = [
            'bagian_nama'    => $request->getVar('nama'),
            'bagian_akses'    => isset($hakAkses) ? implode(',', $hakAkses) : NULL
        ];
        $bagianModel = new \App\Models\BagianModel();
        $query = $bagianModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/bagian');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addBagian')->withInput();
        }
    }

    public function editBagian($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editBagian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Bagian',
            'bagian'        => $bagian,
            'bagianModel'   => $bagianModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/editBagian', $data);
    }

    public function updateBagian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateBagian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $bagianModel = new \App\Models\BagianModel();
        $old = $bagianModel->find($request->getVar('id'));

        if ($old['bagian_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[bagian.bagian_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editBagian/' . $request->getVar('id'))->withInput();
        }

        $akses = $request->getVar('bagian_akses');
        if ($akses) {
            foreach ($akses as $row => $value) {
                $hakAkses[] = $value;
            }
        }

        $data = [
            'bagian_nama'    => $request->getVar('nama'),
            'bagian_akses'    => isset($hakAkses) ? implode(',', $hakAkses) : $old['bagian_akses']
        ];

        $query = $bagianModel->update(['bagian_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/bagian');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editBagian/' . $request->getVar('id'))->withInput();
        }
    }

    public function deleteBagian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteBagian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $bagianModel = new \App\Models\BagianModel();
            $bagian = $bagianModel->find($id);
            if ($bagian) {
                $query = $bagianModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function satuan()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("satuan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $satuanModel = new \App\Models\SatuanModel();
        $satuans = $satuanModel->orderBy('satuan_id', 'DESC');
        $data = [
            'title_bar'     => 'Data Satuan Barang',
            'satuan'          => $satuans->paginate(25, 'view'),
            'satuanModel'    => $satuanModel,
            'pager'         => $satuanModel->pager,
            'current'       => $currentPage,
            'totalRows'        => count($satuans->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/satuan', $data);
    }

    public function insertSatuan()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertSatuan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'nama' => [
                'rules'     => 'required|is_unique[satuan.satuan_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama satuan harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/satuan')->withInput();
        }
        $data = [
            'satuan_nama'    => $request->getVar('nama')
        ];
        $satuanModel = new \App\Models\SatuanModel();
        $query = $satuanModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/satuan');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/satuan')->withInput();
        }
    }

    public function satuanJson($id)
    {
        $satuanModel = new \App\Models\SatuanModel();
        $satuan = $satuanModel->find($id);
        return json_encode($satuan, true);
    }

    public function updateSatuan()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateSatuan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $satuanModel = new \App\Models\SatuanModel();
        $old = $satuanModel->find($request->getVar('id'));

        if ($old['satuan_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[satuan.satuan_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama satuan harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/satuan')->withInput();
        }

        $data = [
            'satuan_nama'    => $request->getVar('nama')
        ];

        $query = $satuanModel->update(['satuan_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/satuan');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/satuan')->withInput();
        }
    }

    public function deleteSatuan()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteSatuan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $satuanModel = new \App\Models\SatuanModel();
            $satuan = $satuanModel->find($id);
            if ($satuan) {
                $query = $satuanModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function kategoriBarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kategoriBarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $kabarModel = new \App\Models\KabarModel();
        $kategoriBarang = $kabarModel->orderBy('kabar_id', 'DESC');
        $data = [
            'title_bar'     => 'Data Kategori Barang',
            'kabar'          => $kategoriBarang->paginate(25, 'view'),
            'kabarModel'    => $kabarModel,
            'pager'         => $kabarModel->pager,
            'current'       => $currentPage,
            'totalRows'        => count($kategoriBarang->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/kategoriBarang', $data);
    }

    public function insertKategoriBarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertKategoriBarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'nama' => [
                'rules'     => 'required|is_unique[kategori_barang.kabar_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama kategori harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kategoriBarang')->withInput();
        }
        $data = [
            'kabar_nama'    => $request->getVar('nama')
        ];
        $kabarModel = new \App\Models\KabarModel();
        $query = $kabarModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kategoriBarang');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kategoriBarang')->withInput();
        }
    }

    public function kabarJson($id)
    {
        $kabarModel = new \App\Models\KabarModel();
        $kabar = $kabarModel->find($id);
        return json_encode($kabar, true);
    }

    public function updateKategoriBarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateKategoriBarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $kabarModel = new \App\Models\KabarModel();
        $old = $kabarModel->find($request->getVar('id'));

        if ($old['kabar_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[kategori_barang.kabar_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama kategori harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kategoriBarang')->withInput();
        }

        $data = [
            'kabar_nama'    => $request->getVar('nama')
        ];

        $query = $kabarModel->update(['kabar_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kategoriBarang');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kategoriBarang')->withInput();
        }
    }

    public function deleteKategoriBarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteKategoriBarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $kabarModel = new \App\Models\KabarModel();
            $kabar = $kabarModel->find($id);
            if ($kabar) {
                $query = $kabarModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function tukang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("tukang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $tukangModel = new \App\Models\TukangModel();
        $tukang = $tukangModel->orderBy('tk_id', 'DESC');
        $data = [
            'title_bar'     => 'Data Tukang',
            'tukang'        => $tukang->paginate(25, 'view'),
            'tukangModel'   => $tukangModel,
            'pager'         => $tukangModel->pager,
            'current'       => $currentPage,
            'totalRows'     => count($tukang->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/tukang', $data);
    }

    public function insertTukang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertTukang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'nama' => [
                'rules'     => 'required|is_unique[tukang.tk_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama tukang harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tukang')->withInput();
        }
        $data = [
            'tk_nama'    => $request->getVar('nama'),
            'tk_alamat'  => $request->getVar('alamat'),
        ];
        $tukangModel = new \App\Models\TukangModel();
        $query = $tukangModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tukang');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tukang')->withInput();
        }
    }

    public function tukangJson($id)
    {
        $tukangModel = new \App\Models\TukangModel();
        $tukang = $tukangModel->find($id);
        return json_encode($tukang, true);
    }

    public function updateTukang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateTukang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tukangModel = new \App\Models\TukangModel();
        $old = $tukangModel->find($request->getVar('id'));

        if ($old['tk_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[tukang.tk_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama tukang harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tukang')->withInput();
        }

        $data = [
            'tk_nama'    => $request->getVar('nama'),
            'tk_alamat'  => $request->getVar('alamat'),
        ];

        $query = $tukangModel->update(['tk_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tukang');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tukang')->withInput();
        }
    }

    public function deleteTukang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteTukang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $tukangModel = new \App\Models\TukangModel();
            $tukang = $tukangModel->find($id);
            if ($tukang) {
                $query = $tukangModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function upah()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("upah", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        // $request = \Config\Services::request();
        // $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $upahModel = new \App\Models\UpahModel();
        $upah = $upahModel->join('kategori_barang', 'kategori_barang.kabar_id = upah.up_kategori', 'left')
            ->join('satuan', 'satuan.satuan_id = upah.up_satuan', 'left')
            ->join('user', 'user.usr_id = upah.up_user', 'left')
            ->join('rekening', 'rekening.rek_id = upah.up_rekening', 'left')
            ->select('upah.*, kategori_barang.kabar_nama, satuan.satuan_nama, user.usr_nama, rekening.rek_kode, rekening.rek_nama')
            ->orderBy('upah.up_id', 'DESC')->findAll();
        $data = [
            'title_bar'     => 'Data Upah',
            'upah'          => $upah,
            'upahModel'     => $upahModel,
            // 'pager'     	=> $upahModel->pager,
            // 'current'   	=> $currentPage,
            'validation'    => $this->validation
        ];
        return view('dashboard/upah', $data);
    }

    public function addUpah()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addUpah", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Tambah Data Upah',
            'upahModel'     => new \App\Models\UpahModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/addUpah', $data);
    }

    public function insertUpah()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertUpah", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'kode' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'rekening' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nama' => [
                'rules'     => 'required|is_unique[upah.up_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        $upahModel = new \App\Models\UpahModel();
        if (!$validate) {
            return redirect()->to('/dashboard/addUpah')->withInput();
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($request->getVar('rekening'));
        $rekKode = $request->getVar('kode');
        $kodeRekening = $rekening['rek_kode'] . '.' . $rekKode;
        if ($rekeningModel->where('rek_kode', $kodeRekening)->first()) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data dengan rekening dan kode barang tersebut sudah tersedia.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addUpah')->withInput();
            die;
        } else {
            $dataRekening = [
                'reksub1_id'    => $rekening['reksub1_id'],
                'reksub2_id'    => $rekening['reksub2_id'],
                'reksub3_id'    => $rekening['reksub3_id'],
                'reksub4_id'    => $request->getVar('rekening'),
                'reksub5_id'    => $rekening['reksub5_id'],
                'rek_kode'      => $kodeRekening,
                'rek_nama'      => strtoupper($request->getVar('nama')),
            ];
            $rekeningModel->save($dataRekening);

            $data = [
                'up_kode'       => $rekKode,
                'up_rekening'   => $request->getVar('rekening'),
                'up_nama'       => $request->getVar('nama'),
                'up_satuan'     => $request->getVar('satuan') ? $request->getVar('satuan') : NULL,
                'up_kategori'   => $request->getVar('kategori') ? $request->getVar('kategori') : NULL,
                'up_nilai'      => $request->getVar('nilai') ? str_replace('.', '', $request->getVar('nilai')) : 0,
                'up_user'       => $this->session->get('usr_id')
            ];

            $query = $upahModel->save($data);
            if ($query) {
                session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/upah');
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/addUpah')->withInput();
            }
        }
    }

    public function editUpah($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editUpah", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $upahModel = new \App\Models\UpahModel();
        $upah = $upahModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Data Upah',
            'upah'          => $upah,
            'barangModel'   => $upahModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/editUpah', $data);
    }

    public function updateUpah()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateUpah", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $upahModel = new \App\Models\UpahModel();
        $old = $upahModel->find($request->getVar('id'));

        if ($old['up_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[upah.up_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editUpah/' . $request->getVar('id'))->withInput();
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($old['up_rekening']);
        $kodeRekening = $rekening['rek_kode'] . '.' . $old['up_kode'];
        $rekeningIni = $rekeningModel->where('rek_kode', $kodeRekening)->first();
        $dataRekening = [
            'rek_nama'  => strtoupper($request->getVar('nama')),
        ];
        $rekeningModel->update(['rek_id' => $rekeningIni['rek_id']], $dataRekening);

        $data = [
            'up_nama'       => $request->getVar('nama'),
            'up_satuan'     => $request->getVar('satuan') ? $request->getVar('satuan') : NULL,
            'up_kategori'   => $request->getVar('kategori') ? $request->getVar('kategori') : NULL,
            'up_nilai'      => $request->getVar('nilai') ? str_replace('.', '', $request->getVar('nilai')) : 0,
            'up_user'       => $this->session->get('usr_id')
        ];

        $query = $upahModel->update(['up_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/upah');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editUpah/' . $request->getVar('id'))->withInput();
        }
    }

    public function deleteUpah()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteUpah", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $upahModel = new \App\Models\UpahModel();
            $upah = $upahModel->find($id);
            if ($upah) {
                $query = $upahModel->delete($id);
                if ($query) {
                    $rekeningModel = new \App\Models\KoderekeningModel();
                    $rekening = $rekeningModel->find($upah['up_rekening']);
                    $kodeRekening = $rekening['rek_kode'] . '.' . $upah['up_kode'];
                    $rekeningModel->where('rek_kode', $kodeRekening)->delete();

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function barang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("barang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        // $request = \Config\Services::request();
        // $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $barangModel = new \App\Models\BarangModel();
        $barang = $barangModel->where('barang.barang_rekening', 35)
            ->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
            ->join('kategori_barang', 'kategori_barang.kabar_id = barang.barang_kategori', 'left')
            ->join('user', 'user.usr_id = barang.barang_user', 'left')
            ->join('rekening', 'rekening.rek_id = barang.barang_rekening', 'left')
            ->select('barang.*, satuan.satuan_id, satuan.satuan_nama, kategori_barang.kabar_id, kategori_barang.kabar_nama, user.usr_id, user.usr_nama, rekening.rek_id, rekening.rek_kode, rekening.rek_nama')
            ->orderBy('barang.barang_id', 'DESC')->findAll();
        $data = [
            'title_bar'     => 'Persediaan Material Gudang',
            'barang'        => $barang,
            'barangModel'   => $barangModel,
            // 'pager'     	=> $barangModel->pager,
            // 'current'   	=> $currentPage,
            'validation'    => $this->validation
        ];
        return view('dashboard/barang', $data);
    }

    public function addBarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addBarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Tambah Material Gudang',
            'barangModel'   => new \App\Models\BarangModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/addBarang', $data);
    }

    public function insertBarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertBarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'kode' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'rekening' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nama' => [
                'rules'     => 'required|is_unique[barang.barang_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kategori' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'satuan' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            // 'jumlah' => [
            // 	'rules'     => 'required',
            // 	'errors'    => [
            // 		'required'  => 'Kolom ini harus diisi.'
            // 	]
            // ],
            // 'minstok' => [
            // 	'rules'     => 'required',
            // 	'errors'    => [
            // 		'required'  => 'Kolom ini harus diisi.'
            // 	]
            // ],
            // 'harga' => [
            // 	'rules'     => 'required',
            // 	'errors'    => [
            // 		'required'  => 'Kolom ini harus diisi.'
            // 	]
            // ]
        ]);

        $barangModel = new \App\Models\BarangModel();
        if (!$validate) {
            return redirect()->to('/dashboard/addBarang')->withInput();
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($request->getVar('rekening'));
        $rekExt = explode('.', $rekening['rek_kode']);
        $rekKode = $request->getVar('kode');
        $kodeRekening = $rekExt[0] . '.' . $rekExt[1] . '.' . ($rekKode);
        if ($rekeningModel->where('rek_kode', $kodeRekening)->first()) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data dengan rekening dan kode barang tersebut sudah tersedia.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addBarang')->withInput();
            die;
        } else {
            $dataRekening = [
                'reksub1_id'    => $rekening['reksub1_id'],
                'reksub2_id'    => $rekening['reksub2_id'],
                'reksub3_id'    => $rekening['reksub3_id'],
                'reksub4_id'    => $rekening['reksub4_id'],
                'reksub5_id'    => $request->getVar('rekening'),
                'rek_kode'      => $kodeRekening,
                'rek_nama'      => strtoupper($request->getVar('nama')),
            ];
            $rekeningModel->save($dataRekening);

            $data = [
                'barang_kode'        => $rekKode,
                'barang_rekening'    => $request->getVar('rekening'),
                'barang_nama'        => $request->getVar('nama'),
                'barang_kategori'    => $request->getVar('kategori'),
                'barang_satuan'        => $request->getVar('satuan'),
                'barang_jumlah'        => $request->getVar('jumlah') ? $request->getVar('jumlah') : 0,
                'barang_minstok'    => $request->getVar('minstok') ? $request->getVar('minstok') : 0,
                'barang_harga'        => $request->getVar('harga') ? str_replace('.', '', $request->getVar('harga')) : 0,
                'barang_user'        => $this->session->get('usr_id')
            ];

            $query = $barangModel->save($data);
            if ($query) {
                if ($request->getVar('jumlah')) {
                    $logStokModel = new \App\Models\LogstokModel();
                    $lastBrg = $barangModel->orderBy('barang_id', 'DESC')->first();
                    $logData = [
                        'ls_barang' => $lastBrg['barang_id'],
                        'ls_before' => 0,
                        'ls_after'    => $request->getVar('jumlah') ? $request->getVar('jumlah') : 0,
                        'ls_user'    => $this->session->get('usr_id')
                    ];
                    $logStokModel->save($logData);
                }
                session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/barang');
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/addBarang')->withInput();
            }
        }
    }

    public function editBarang($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editBarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $barangModel = new \App\Models\BarangModel();
        $barang = $barangModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Material Gudang',
            'barang'        => $barang,
            'barangModel'   => $barangModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/editBarang', $data);
    }

    public function updateBarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateBarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $barangModel = new \App\Models\BarangModel();
        $old = $barangModel->find($request->getVar('id'));

        if ($old['barang_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[barang.barang_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kategori' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'satuan' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            // 'jumlah' => [
            // 	'rules'     => 'required',
            // 	'errors'    => [
            // 		'required'  => 'Kolom ini harus diisi.'
            // 	]
            // ],
            // 'minstok' => [
            // 	'rules'     => 'required',
            // 	'errors'    => [
            // 		'required'  => 'Kolom ini harus diisi.'
            // 	]
            // ],
            // 'harga' => [
            // 	'rules'     => 'required',
            // 	'errors'    => [
            // 		'required'  => 'Kolom ini harus diisi.'
            // 	]
            // ]
        ]);

        $barangModel = new \App\Models\BarangModel();
        if (!$validate) {
            return redirect()->to('/dashboard/editBarang/' . $request->getVar('id'))->withInput();
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($old['barang_rekening']);
        $rekExt = explode('.', $rekening['rek_kode']);
        $kodeRekening = $rekExt[0] . '.' . $rekExt[1] . '.' . ($old['barang_kode']);
        $rekeningIni = $rekeningModel->where('rek_kode', $kodeRekening)->first();
        $dataRekening = [
            'rek_nama'  => strtoupper($request->getVar('nama')),
        ];
        $rekeningModel->update(['rek_id' => $rekeningIni['rek_id']], $dataRekening);

        $data = [
            'barang_nama'        => $request->getVar('nama'),
            'barang_kategori'    => $request->getVar('kategori'),
            'barang_satuan'        => $request->getVar('satuan'),
            'barang_jumlah'        => $request->getVar('jumlah') ? $request->getVar('jumlah') : 0,
            'barang_minstok'    => $request->getVar('minstok') ? $request->getVar('minstok') : 0,
            'barang_harga'        => $request->getVar('harga') ? str_replace('.', '', $request->getVar('harga')) : 0,
            'barang_user'        => $this->session->get('usr_id')
        ];

        $query = $barangModel->update(['barang_id' => $request->getVar('id')], $data);
        if ($query) {
            if ($request->getVar('jumlah')) {
                $logStokModel = new \App\Models\LogstokModel();
                $logData = [
                    'ls_barang'         => $request->getVar('id'),
                    'ls_before'         => $old['barang_jumlah'],
                    'ls_after'            => $request->getVar('jumlah') ? $request->getVar('jumlah') : 0,
                    'ls_beforeHarga'    => $old['barang_harga'],
                    'ls_afterHarga'        => $request->getVar('harga') ? str_replace('.', '', $request->getVar('harga')) : 0,
                    'ls_user'            => $this->session->get('usr_id')
                ];
                $logStokModel->save($logData);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/barang');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editBarang/' . $request->getVar('id'))->withInput();
        }
    }

    public function deleteBarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteBarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $barangModel = new \App\Models\BarangModel();
            $barang = $barangModel->find($id);
            if ($barang) {
                $query = $barangModel->delete($id);
                if ($query) {
                    $logStokModel = new \App\Models\LogstokModel();
                    $logStokModel->where('ls_barang', $id)->delete();

                    $rekeningModel = new \App\Models\KoderekeningModel();
                    $rekening = $rekeningModel->find($barang['barang_rekening']);
                    $rekExt = explode('.', $rekening['rek_kode']);
                    $kodeRekening = $rekExt[0] . '.' . $rekExt[1] . '.' . ($barang['barang_kode']);
                    $rekeningModel->where('rek_kode', $kodeRekening)->delete();

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function unit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("unit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $unitModel = new \App\Models\UnitModel();
        $unit = $unitModel->join('user', 'user.usr_id = unit.unit_user', 'left')
            ->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->join('rekening', 'rekening.rek_id = unit.unit_rekening', 'left')
            ->select('unit.*, user.usr_id, user.usr_nama, rekening.rek_id, rekening.rek_kode, rekening.rek_nama, types.type_id, types.type_nama')
            ->orderBy('unit.unit_id', 'DESC');
        $data = [
            'title_bar'     => 'Data Unit',
            'unit'           => $unit->paginate(100, 'view'),
            'unitModel'       => $unitModel,
            'pager'         => $unitModel->pager,
            'current'       => $currentPage,
            'totalRows'        => count($unit->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/unit', $data);
    }

    public function unitJson($id)
    {
        $unitModel = new \App\Models\UnitModel();
        $unit = $unitModel->find($id);
        if ($unit) {
            return json_encode($unit, true);
        } else {
            return false;
        }
    }

    public function addUnit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addUnit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Tambah Unit',
            'unitModel'     => new \App\Models\UnitModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/addUnit', $data);
    }

    public function insertUnit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertUnit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'rekening' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'tipe' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kode' => [
                'rules'     => 'required|is_unique[unit.unit_kode]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Kode telah digunakan.'
                ]
            ],
            'nama' => [
                'rules'     => 'required|is_unique[unit.unit_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Nama telah terdaftar.'
                ]
            ]
        ]);

        $unitModel = new \App\Models\UnitModel();
        $checkUnit = $unitModel->where(['unit_rekening' => $request->getVar('rekening'), 'unit_kode' => $request->getVar('kode')])->first();
        if ($checkUnit) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data dengan rekening dan kode barang tersebut sudah tersedia.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addUnit')->withInput();
            die;
        }

        if (!$validate) {
            return redirect()->to('/dashboard/addUnit')->withInput();
            die;
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($request->getVar('rekening'));
        $rekExt = explode('.', $rekening['rek_kode']);
        $kodeRekening = $rekExt[0] . '.' . $rekExt[1] . '.' . ($request->getVar('kode'));
        if ($rekeningModel->where('rek_kode', $kodeRekening)->first()) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data dengan rekening dan kode barang tersebut sudah tersedia.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addUnit')->withInput();
            die;
        } else {
            $dataRekening = [
                'reksub1_id'    => $rekening['reksub1_id'],
                'reksub2_id'    => $rekening['reksub2_id'],
                'reksub3_id'    => $rekening['reksub3_id'],
                'reksub4_id'    => $rekening['reksub4_id'],
                'reksub5_id'    => $request->getVar('rekening'),
                'rek_kode'      => $kodeRekening,
                'rek_nama'      => strtoupper($request->getVar('nama')),
            ];
            $rekeningModel->save($dataRekening);

            $data = [
                'unit_rekening'     => $request->getVar('rekening'),
                'unit_kode'         => $request->getVar('kode'),
                'unit_tipe'         => $request->getVar('tipe'),
                'unit_nama'         => $request->getVar('nama'),
                'unit_keterangan'   => $request->getVar('keterangan'),
                'unit_nilaitanah'   => str_replace('.', '', $request->getVar('nilaitanah')),
                'unit_user'         => $this->session->get('usr_id')
            ];
            $query = $unitModel->save($data);
            if ($query) {
                session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/unit');
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/addUnit')->withInput();
            }
        }
    }

    public function editUnit($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editUnit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $unitModel = new \App\Models\UnitModel();
        $unit = $unitModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Unit',
            'unit'            => $unit,
            'unitModel'       => $unitModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/editUnit', $data);
    }

    public function updateUnit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateUnit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $unitModel = new \App\Models\UnitModel();
        $old = $unitModel->find($request->getVar('id'));

        if ($old['unit_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[unit.unit_nama]';
        }

        $validate = $this->validate([
            'tipe' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editUnit/' . $request->getVar('id'))->withInput();
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($old['unit_rekening']);
        $rekExt = explode('.', $rekening['rek_kode']);
        $kodeRekening = $rekExt[0] . '.' . $rekExt[1] . '.' . ($old['unit_kode']);
        $rekeningIni = $rekeningModel->where('rek_kode', $kodeRekening)->first();

        $dataRekening = [
            'rek_nama'  => strtoupper($request->getVar('nama')),
        ];
        $rekeningModel->update(['rek_id' => $rekeningIni['rek_id']], $dataRekening);

        $unitModel = new \App\Models\UnitModel();
        $data = [
            'unit_nama'         => $request->getVar('nama'),
            'unit_tipe'         => $request->getVar('tipe'),
            'unit_keterangan'   => $request->getVar('keterangan'),
            'unit_nilaitanah'   => str_replace('.', '', $request->getVar('nilaitanah')),
            'unit_user'         => $this->session->get('usr_id')
        ];

        $query = $unitModel->update(['unit_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/unit');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editUnit/' . $request->getVar('id'))->withInput();
        }
    }

    public function deleteUnit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteUnit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $unitModel = new \App\Models\UnitModel();
            $unit = $unitModel->find($id);
            if ($unit) {
                $query = $unitModel->delete($id);
                if ($query) {
                    $rekeningModel = new \App\Models\KoderekeningModel();
                    $rekening = $rekeningModel->find($unit['unit_rekening']);
                    $rekExt = explode('.', $rekening['rek_kode']);
                    $kodeRekening = $rekExt[0] . '.' . $rekExt[1] . '.' . ($unit['unit_kode']);
                    $rekeningModel->where('rek_kode', $kodeRekening)->delete();

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function tipeUnit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("tipeUnit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $typeModel = new \App\Models\TypesModel();
        $types = $typeModel->orderBy('type_id', 'DESC');
        $data = [
            'title_bar'     => 'Data Tipe Unit',
            'types'          => $types->paginate(25, 'view'),
            'typeModel'        => $typeModel,
            'pager'         => $typeModel->pager,
            'current'       => $currentPage,
            'totalRows'       => count($types->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/tipeUnit', $data);
    }

    public function tipeUnitJson($id)
    {
        $typeModel = new \App\Models\TypesModel();
        $type = $typeModel->find($id);
        return json_encode($type, true);
    }

    public function insertTipeUnit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertTipeUnit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'nama' => [
                'rules'     => 'required|is_unique[types.type_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Tipe unit harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tipeUnit')->withInput();
        }
        $data = [
            'type_nama'    => $request->getVar('nama')
        ];
        $typeModel = new \App\Models\TypesModel();
        $query = $typeModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tipeUnit');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tipeUnit')->withInput();
        }
    }

    public function updateTipeUnit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateTipeUnit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $typeModel = new \App\Models\TypesModel();
        $old = $typeModel->find($request->getVar('id'));

        if ($old['type_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[types.type_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Tipe unit harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tipeUnit')->withInput();
        }

        $data = [
            'type_nama'    => $request->getVar('nama')
        ];

        $query = $typeModel->update(['type_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tipeUnit');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/tipeUnit')->withInput();
        }
    }

    public function deleteTipeUnit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteTipeUnit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $typeModel = new \App\Models\TypesModel();
            $type = $typeModel->find($id);
            if ($type) {
                $query = $typeModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function kpr()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kpr", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $kprModel = new \App\Models\KprModel();
        $kpr = $kprModel->orderBy('kpr_id', 'DESC');
        $data = [
            'title_bar'     => 'Data KPR',
            'kpr'              => $kpr->paginate(25, 'view'),
            'kprModel'        => $kprModel,
            'pager'         => $kprModel->pager,
            'current'       => $currentPage,
            'totalRows'       => count($kpr->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/kpr', $data);
    }

    public function kprJson($id)
    {
        $kprModel = new \App\Models\KprModel();
        $kpr = $kprModel->find($id);
        return json_encode($kpr, true);
    }

    public function insertKpr()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertKpr", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'nama' => [
                'rules'     => 'required|is_unique[kpr.kpr_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama KPR harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kpr')->withInput();
        }
        $data = [
            'kpr_nama'            => $request->getVar('nama'),
            'kpr_keterangan'    => $request->getVar('keterangan')
        ];
        $KprModel = new \App\Models\KprModel();
        $query = $KprModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kpr');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kpr')->withInput();
        }
    }

    public function updateKpr()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateKpr", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $kprModel = new \App\Models\KprModel();
        $old = $kprModel->find($request->getVar('id'));

        if ($old['kpr_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[kpr.kpr_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama KPR harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kpr')->withInput();
        }

        $data = [
            'kpr_nama'            => $request->getVar('nama'),
            'kpr_keterangan'    => $request->getVar('keterangan'),
        ];

        $query = $kprModel->update(['kpr_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kpr');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kpr')->withInput();
        }
    }

    public function deleteKpr()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteKpr", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $kprModel = new \App\Models\KprModel();
            $kpr = $kprModel->find($id);
            if ($kpr) {
                $query = $kprModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }


    public function suplier()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("suplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $suplierModel = new \App\Models\SuplierModel();
        $suplier = $suplierModel->join('user', 'user.usr_id = suplier.suplier_user', 'left')
            ->join('rekening', 'rekening.rek_id = suplier.suplier_rekening', 'left')
            ->select('suplier.*, user.usr_id, user.usr_nama, rekening.rek_id, rekening.rek_kode, rekening.rek_nama')
            ->orderBy('suplier.suplier_id', 'DESC');

        $data = [
            'title_bar'     => 'Data Suplier',
            'suplier'          => $suplier->paginate(100, 'view'),
            'suplierModel'  => $suplierModel,
            'pager'         => $suplierModel->pager,
            'current'       => $currentPage,
            'totalRows'       => count($suplier->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/suplier', $data);
    }

    public function suplierJson($id)
    {
        $suplierModel = new \App\Models\SuplierModel();
        $supplier = $suplierModel->find($id);
        return json_encode($supplier, true);
    }

    public function addSuplier()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addSuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Tambah Suplier',
            'suplierModel'  => new \App\Models\SuplierModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/addSuplier', $data);
    }

    public function insertSuplier()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertSuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'rekening' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kode' => [
                'rules'     => 'required|is_unique[suplier.suplier_kode]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nama' => [
                'rules'     => 'required|is_unique[suplier.suplier_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/addSuplier')->withInput();
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($request->getVar('rekening'));
        $kodeRekening = $rekening['rek_kode'] . '.' . $request->getVar('kode');
        if ($rekeningModel->where('rek_kode', $kodeRekening)->first()) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data dengan rekening dan kode barang tersebut sudah tersedia.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addSuplier')->withInput();
            die;
        } else {
            $dataRekening = [
                'reksub1_id'    => $rekening['reksub1_id'],
                'reksub2_id'    => $rekening['reksub2_id'],
                'reksub3_id'    => $rekening['reksub3_id'],
                'reksub4_id'    => $request->getVar('rekening'),
                'reksub5_id'    => $rekening['reksub5_id'],
                'rek_kode'      => $kodeRekening,
                'rek_nama'      => strtoupper($request->getVar('nama')),
            ];
            $rekeningModel->save($dataRekening);

            $data = [
                'suplier_rekening'    => $request->getVar('rekening'),
                'suplier_kode'        => $request->getVar('kode'),
                'suplier_nama'        => $request->getVar('nama'),
                'suplier_alamat'    => $request->getVar('alamat'),
                'suplier_telp'        => $request->getVar('telp'),
                'suplier_user'        => $this->session->get('usr_id')
            ];
            $suplierModel = new \App\Models\SuplierModel();
            $query = $suplierModel->save($data);
            if ($query) {
                session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/suplier');
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/addSuplier')->withInput();
            }
        }
    }

    public function editSuplier($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editSuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $suplierModel = new \App\Models\SuplierModel();
        $suplier = $suplierModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Suplier',
            'suplier'       => $suplier,
            'suplierModel'  => $suplierModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/editSuplier', $data);
    }

    public function updateSuplier()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateSuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $suplierModel = new \App\Models\SuplierModel();
        $old = $suplierModel->find($request->getVar('id'));

        if ($old['suplier_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[suplier.suplier_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editSuplier/' . $request->getVar('id'))->withInput();
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($old['suplier_rekening']);
        $kodeRekening = $rekening['rek_kode'] . '.' . $old['suplier_kode'];
        $rekeningIni = $rekeningModel->where('rek_kode', $kodeRekening)->first();

        $dataRekening = [
            'rek_nama'  => strtoupper($request->getVar('nama')),
        ];
        $rekeningModel->update(['rek_id' => $rekeningIni['rek_id']], $dataRekening);

        $data = [
            'suplier_nama'        => $request->getVar('nama'),
            'suplier_alamat'    => $request->getVar('alamat'),
            'suplier_telp'        => $request->getVar('telp'),
            'suplier_user'        => $this->session->get('usr_id')
        ];

        $query = $suplierModel->update(['suplier_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/suplier');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editSuplier/' . $request->getVar('id'))->withInput();
        }
    }

    public function deleteSuplier()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteSuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $suplierModel = new \App\Models\SuplierModel();
            $suplier = $suplierModel->find($id);
            if ($suplier) {
                $query = $suplierModel->delete($id);
                if ($query) {
                    $rekeningModel = new \App\Models\KoderekeningModel();
                    $rekening = $rekeningModel->find($suplier['suplier_rekening']);
                    $kodeRekening = $rekening['rek_kode'] . '.' . $suplier['suplier_kode'];
                    $rekeningModel->where('rek_kode', $kodeRekening)->delete();

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function customer()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("customer", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $custModel = new \App\Models\CustomerModel();
        $customers = $custModel->join('user', 'user.usr_id = customers.cust_user', 'left')
            ->join('rekening', 'rekening.rek_id = customers.cust_rekening', 'left')
            ->select('customers.*, user.usr_id, user.usr_nama, rekening.rek_id, rekening.rek_kode, rekening.rek_nama')
            ->orderBy('customers.cust_id', 'DESC');

        $data = [
            'title_bar'     => 'Data Customer',
            'customers'     => $customers->paginate(100, 'view'),
            'custModel'     => $custModel,
            'pager'         => $custModel->pager,
            'current'       => $currentPage,
            'totalRows'       => count($customers->findAll()),
            'validation'    => $this->validation
        ];
        return view('dashboard/customer', $data);
    }

    public function addCustomer()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addCustomer", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Tambah Customer',
            'custModel'      => new \App\Models\CustomerModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/addCustomer', $data);
    }

    public function insertCustomer()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertCustomer", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'rekening' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kode' => [
                'rules'     => 'required|is_unique[customers.cust_kode]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique'    => 'Kode sudah digunakan.'
                ]
            ],
            'nama' => [
                'rules'     => 'required|is_unique[customers.cust_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique'    => 'Nama telah terdaftar.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/addCustomer')->withInput();
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($request->getVar('rekening'));
        $kodeRekening = $rekening['rek_kode'] . '.' . $request->getVar('kode');
        if ($rekeningModel->where('rek_kode', $kodeRekening)->first()) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data dengan rekening dan kode barang tersebut sudah tersedia.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addCustomer')->withInput();
            die;
        } else {
            $dataRekening = [
                'reksub1_id'    => $rekening['reksub1_id'],
                'reksub2_id'    => $rekening['reksub2_id'],
                'reksub3_id'    => $rekening['reksub3_id'],
                'reksub4_id'    => $request->getVar('rekening'),
                'reksub5_id'    => $rekening['reksub5_id'],
                'rek_kode'      => $kodeRekening,
                'rek_nama'      => strtoupper($request->getVar('nama')),
            ];
            $rekeningModel->save($dataRekening);

            $data = [
                'cust_rekening'  => $request->getVar('rekening'),
                'cust_kode'      => $request->getVar('kode'),
                'cust_nik'       => $request->getVar('nik') ? $request->getVar('nik') : NULL,
                'cust_nama'      => $request->getVar('nama'),
                'cust_alamat'    => $request->getVar('alamat'),
                'cust_telp'      => $request->getVar('telp'),
                'cust_user'      => $this->session->get('usr_id')
            ];
            $custModel = new \App\Models\CustomerModel();
            $query = $custModel->save($data);
            if ($query) {
                session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/customer');
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/addCustomer')->withInput();
            }
        }
    }

    public function customerJson($id)
    {
        $custModel = new \App\Models\CustomerModel();
        $customer = $custModel->find($id);
        if ($customer) {
            return json_encode($customer, true);
        } else {
            return false;
        }
    }

    public function editCustomer($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editCustomer", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $custModel = new \App\Models\CustomerModel();
        $customer = $custModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Customer',
            'customer'      => $customer,
            'custModel'      => $custModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/editCustomer', $data);
    }

    public function updateCustomer()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateCustomer", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $custModel = new \App\Models\CustomerModel();
        $old = $custModel->find($request->getVar('id'));

        if ($old['cust_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[customers.cust_nama]';
        }

        $validate = $this->validate([
            'rekening' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kode' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editCustomer/' . $request->getVar('id'))->withInput();
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($old['cust_rekening']);
        $kodeRekening = $rekening['rek_kode'] . '.' . $old['cust_kode'];
        $rekeningIni = $rekeningModel->where('rek_kode', $kodeRekening)->first();

        $dataRekening = [
            'rek_nama'  => strtoupper($request->getVar('nama')),
        ];
        $rekeningModel->update(['rek_id' => $rekeningIni['rek_id']], $dataRekening);

        $data = [
            'cust_rekening'    => $request->getVar('rekening'),
            'cust_kode'        => $request->getVar('kode'),
            'cust_nik'        => $request->getVar('nik') ? $request->getVar('nik') : NULL,
            'cust_nama'        => $request->getVar('nama'),
            'cust_alamat'    => $request->getVar('alamat'),
            'cust_telp'        => $request->getVar('telp'),
            'cust_user'        => $this->session->get('usr_id')
        ];

        $query = $custModel->update(['cust_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/customer');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editCustomer/' . $request->getVar('id'))->withInput();
        }
    }

    public function deleteCustomer()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteCustomer", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $custModel = new \App\Models\CustomerModel();
            $customer = $custModel->find($id);
            if ($customer) {
                $query = $custModel->delete($id);
                if ($query) {
                    $rekeningModel = new \App\Models\KoderekeningModel();
                    $rekening = $rekeningModel->find($customer['cust_rekening']);
                    $kodeRekening = $rekening['rek_kode'] . '.' . $customer['cust_kode'];
                    $rekeningModel->where('rek_kode', $kodeRekening)->delete();

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function statusbarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("statusbarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $sabarModel = new \App\Models\StatusbarangModel();
        $data = [
            'title_bar'     => 'Data Status Barang',
            'sabar'          => $sabarModel->orderBy('sb_id', 'DESC')->paginate(20, 'view'),
            'kabarModel'    => $sabarModel,
            'pager'         => $sabarModel->pager,
            'current'       => $currentPage,
            'validation'    => $this->validation
        ];
        return view('dashboard/statusbarang', $data);
    }

    public function insertStatusbarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertStatusbarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'nama' => [
                'rules'     => 'required|is_unique[status_barang.sb_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Status harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/statusbarang')->withInput();
        }
        $data = [
            'sb_nama'    => $request->getVar('nama')
        ];
        $sabarModel = new \App\Models\StatusbarangModel();
        $query = $sabarModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/statusbarang');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/statusbarang')->withInput();
        }
    }

    public function sabarJson($id)
    {
        $sabarModel = new \App\Models\StatusbarangModel();
        $sabar = $sabarModel->find($id);
        return json_encode($sabar, true);
    }

    public function updateStatusbarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateStatusbarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $sabarModel = new \App\Models\StatusbarangModel();
        $old = $sabarModel->find($request->getVar('id'));

        if ($old['sb_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[status_barang.sb_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Status harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/statusbarang')->withInput();
        }

        $data = [
            'sb_nama'    => $request->getVar('nama')
        ];

        $query = $sabarModel->update(['sb_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/statusbarang');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/statusbarang')->withInput();
        }
    }

    public function deleteStatusbarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteStatusbarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id != 1) {
            $sabarModel = new \App\Models\StatusbarangModel();
            $kabar = $sabarModel->find($id);
            if ($kabar) {
                $query = $sabarModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        } else {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }
    }

    public function pengaturan()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("pengaturan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $settingModel = new \App\Models\SettingModel();
        $pengaturan = $settingModel->find(1);
        $data = [
            'title_bar'     => 'Pengaturan Aplikasi',
            'pengaturan'    => $pengaturan,
            'validation'    => $this->validation
        ];
        return view('dashboard/pengaturan', $data);
    }

    public function updatePengaturan()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updatePengaturan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $pengaturanModel = new \App\Models\SettingModel();
        $pengaturan = $pengaturanModel->find(1);

        if ($pengaturan['setting_logo'] == '' || $pengaturan['setting_logo'] == NULL) {
            $logo_rule = 'uploaded[logo]|is_image[logo]';
        } else {
            $logo_rule = 'is_image[logo]';
        }

        if ($pengaturan['setting_logo2'] == '' || $pengaturan['setting_logo2'] == NULL) {
            $logo2_rule = 'uploaded[logo2]|is_image[logo2]';
        } else {
            $logo2_rule = 'is_image[logo2]';
        }

        if ($pengaturan['setting_favicon'] == '' || $pengaturan['setting_favicon'] == NULL) {
            $favicon_rule = 'uploaded[favicon]|is_image[favicon]';
        } else {
            $favicon_rule = 'is_image[favicon]';
        }

        $validate = $this->validate([
            'logo' => [
                'rules'     => $logo_rule,
                'errors'    => [
                    'uploaded'  => 'Pilih gambar logo.',
                    'is_image'  => 'Gambar tidak valid.'
                ]
            ],
            'logo2' => [
                'rules'     => $logo2_rule,
                'errors'    => [
                    'uploaded'  => 'Pilih gambar logo.',
                    'is_image'  => 'Gambar tidak valid.'
                ]
            ],
            'favicon' => [
                'rules'     => $favicon_rule,
                'errors'    => [
                    'uploaded'  => 'Pilih gambar favicon.',
                    'is_image'  => 'Gambar tidak valid.'
                ]
            ],
            'nama' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/pengaturan')->withInput();
        }

        $logo = $request->getFile('logo');
        if ($logo->getError() == 4) {
            $logoFile = $pengaturan['setting_logo'];
        } else {
            if ($pengaturan['setting_logo'] != 'logo1.png') {
                unlink('assets/img/' . $pengaturan['setting_logo']);
            }
            $logoFile = $logo->getRandomName();
            $logo->move('assets/img/', $logoFile);
        }

        $logo2 = $request->getFile('logo2');
        if ($logo2->getError() == 4) {
            $logo2File = $pengaturan['setting_logo2'];
        } else {
            if ($pengaturan['setting_logo2'] != 'logo2.png') {
                unlink('assets/img/' . $pengaturan['setting_logo2']);
            }
            $logo2File = $logo2->getRandomName();
            $logo2->move('assets/img/', $logo2File);
        }

        $favicon = $request->getFile('favicon');
        if ($favicon->getError() == 4) {
            $faviconFile = $pengaturan['setting_favicon'];
        } else {
            if ($pengaturan['setting_favicon'] != 'logo3.png') {
                unlink('assets/img/' . $pengaturan['setting_favicon']);
            }
            $faviconFile = $favicon->getRandomName();
            $favicon->move('assets/img/', $faviconFile);
        }

        $data = [
            'setting_nama'       => $request->getVar('nama'),
            'setting_logo'       => $logoFile,
            'setting_logo2'      => $logo2File,
            'setting_favicon'    => $faviconFile,
            'setting_latitude'   => $request->getVar('latitude'),
            'setting_longitude'  => $request->getVar('longitude'),
            'setting_radius'     => $request->getVar('radius')
        ];

        $query = $pengaturanModel->update(['setting_id' => 1], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/pengaturan');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/pengaturan')->withInput();
        }
    }

    public function rekening()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("rekening", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $rekeningModel = new \App\Models\KoderekeningModel();
        $data = [
            'title_bar'     => 'Data Rekening',
            'rekening'      => $rekeningModel->orderBy('rek_id', 'DESC')->findAll(),
            'rekeningModel' => $rekeningModel,
            'pager'         => $rekeningModel->pager,
            'current'       => $currentPage,
            'validation'    => $this->validation
        ];
        return view('dashboard/rekening', $data);
    }

    public function addRekening()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addRekening", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'         => 'Tambah Rekening',
            'rekeningModel'     => new \App\Models\KoderekeningModel(),
            'validation'        => $this->validation
        ];
        return view('dashboard/addRekening', $data);
    }

    public function insertRekening()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertRekening", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'kode' => [
                'rules'     => 'required|is_unique[rekening.rek_kode]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Kode rekening sudah tersedia.'
                ]
            ],
            'rekening' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Nama rekening sudah tersedia.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/addRekening')->withInput();
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $kode = str_replace(',', '.', $request->getVar('kode'));
        $check = $rekeningModel->where('rek_kode', $kode)->first();
        if ($check) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Kode rekening sudah digunakan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addRekening')->withInput();
            die;
        }

        $data = [
            'reksub1_id'    => 1,
            'reksub2_id'    => $request->getVar('reksub1') ? $request->getVar('reksub1') : NULL,
            'reksub3_id'    => $request->getVar('reksub2') ? $request->getVar('reksub2') : NULL,
            'reksub4_id'    => $request->getVar('reksub3') ? $request->getVar('reksub3') : NULL,
            'reksub5_id'    => $request->getVar('reksub4') ? $request->getVar('reksub4') : NULL,
            'rek_kode'      => str_replace(',', '.', $request->getVar('kode')),
            'rek_nama'      => strtoupper($request->getVar('rekening')),
        ];
        $query = $rekeningModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addRekening');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addRekening')->withInput();
        }
    }

    public function editRekening($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editRekening", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $rekeningModel = new \App\Models\KoderekeningModel();
        $data = [
            'title_bar'         => 'Perbarui Rekening',
            'rekening'          => $rekeningModel->find($id),
            'rekeningModel'     => $rekeningModel,
            'validation'        => $this->validation
        ];
        return view('dashboard/editRekening', $data);
    }

    public function rekeningJson($id)
    {
        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($id);
        return json_encode($rekening, true);
    }

    public function rekeningByKodeJson($kode)
    {
        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->where('rek_kode', $kode)->first();
        return json_encode($rekening, true);
    }

    public function updateRekening()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateRekening", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekOld = $rekeningModel->find($request->getVar('id'));
        if ($rekOld['rek_kode'] == str_replace(',', '.', $request->getVar('kode'))) {
            $kode_rule = 'required';
        } else {
            $kode_rule = 'required|is_unique[rekening.rek_kode]';
        }

        $validate = $this->validate([
            'id' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kode' => [
                'rules'     => $kode_rule,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Kode rekening sudah tersedia.'
                ]
            ],
            'rekening' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editRekening/' . $request->getVar('id'))->withInput();
        }

        $data = [
            'reksub1_id'    => 1,
            'reksub2_id'    => $request->getVar('reksub1') ? $request->getVar('reksub1') : NULL,
            'reksub3_id'    => $request->getVar('reksub2') ? $request->getVar('reksub2') : NULL,
            'reksub4_id'    => $request->getVar('reksub3') ? $request->getVar('reksub3') : NULL,
            'reksub5_id'    => $request->getVar('reksub4') ? $request->getVar('reksub4') : NULL,
            'rek_kode'      => str_replace(',', '.', $request->getVar('kode')),
            'rek_nama'      => strtoupper($request->getVar('rekening')),
        ];

        $query = $rekeningModel->update(['rek_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil diperbarui.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/rekening');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal diperbarui.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editRekening/' . $request->getVar('id'))->withInput();
        }
    }

    public function deleteRekening()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteRekening", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        $rekeningModel = new \App\Models\KoderekeningModel();
        $rekening = $rekeningModel->find($id);
        if ($rekening) {
            $query = $rekeningModel->delete($id);
            if ($query) {
                session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return '<script>window.history.go(-1);</script>';
        }
    }

    public function barangJson($id)
    {
        $barangModel = new \App\Models\BarangModel();
        $barang = $barangModel->join('kategori_barang', 'kategori_barang.kabar_id = barang.barang_kategori', 'left')
            ->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
            ->join('user', 'user.usr_id = barang.barang_user', 'left')
            ->select('barang.*, kategori_barang.kabar_id, kategori_barang.kabar_nama, satuan.satuan_id, satuan.satuan_nama, user.usr_id, user.usr_nama')
            ->where('barang.barang_id', $id)->first();
        if ($barang) {
            $stokSaatIni = $barangModel->getStokSaatIni($barang['barang_id']);
            $hargaRataRata = $barangModel->rataRataHarga($barang['barang_id']);
            return json_encode(['barang' => $barang, 'harga' => $hargaRataRata, 'stok' => $stokSaatIni], true);
        } else {
            return null;
        }
    }

    public function upahJson($id)
    {
        $upahModel = new \App\Models\UpahModel();
        $upah = $upahModel->join('satuan', 'satuan.satuan_id = upah.up_satuan', 'left')
            ->join('user', 'user.usr_id = upah.up_user', 'left')
            ->select('upah.*, satuan.satuan_id, satuan.satuan_nama, user.usr_id, user.usr_nama')
            ->where('upah.up_id', $id)->first();
        if ($upah) {
            return json_encode($upah, true);
        } else {
            return null;
        }
    }

    public function rap()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("rap", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();

        $rapsModel = new \App\Models\RapsModel();
        $kabarModel = new \App\Models\KabarModel();
        $kabar = $kabarModel->orderBy('kabar_id', 'ASC')->findAll();

        if ($request->getVar('tipe')) {
            $rapsModel->where('raps.rap_tipe', $request->getVar('tipe'));
        }

        $rapsModel
            ->join('types', 'types.type_id = raps.rap_tipe', 'left')
            ->select('raps.*, types.type_id, types.type_nama')
            ->orderBy('raps.rap_id', 'ASC');

        // if ($request->getVar('src') != '') {
        //     $rapsModel->groupStart();
        //     $rapsModel->orLike([
        //         'barang.barang_nama' => $request->getVar('src'),
        //         'upah.up_nama' => $request->getVar('src'),
        //     ]);
        //     $rapsModel->groupEnd();
        // }

        $raps = $rapsModel->findAll();

        $data = [
            'title_bar'     => 'Rencana Analisa Pengeluaran (RAP)',
            'raps'           => $raps,
            'kabar'            => $kabar,
            'rapsModel'       => $rapsModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/rap', $data);
    }

    public function rapJson($type)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("rap", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $rapModel = new \App\Models\RapsModel();
        $rap = $rapModel->where('rap_tipe', $type)->findAll();
        return json_encode($rap, true);
    }

    public function rapBarangJson($type, $idBarang)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("rap", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $rapModel = new \App\Models\RapsModel();
        $rap = $rapModel->where(['rap_tipe' => $type, 'rap_barang' => $idBarang])->first();
        return json_encode($rap, true);
    }

    public function rapUpahJson($type, $idUpah)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("rap", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $rapModel = new \App\Models\RapsModel();
        $rap = $rapModel->where(['rap_tipe' => $type, 'rap_upah' => $idUpah])->first();
        return json_encode($rap, true);
    }

    public function addRap()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addRap", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Tambah Barang / Pekerjaan',
            'rapsModel'       => new \App\Models\RapsModel(),
            'validation'    => $this->validation
        ];
        return view('dashboard/addRap', $data);
    }

    public function insertRap()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertRap", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $rapsModel = new \App\Models\RapsModel();

        $dataRaw = json_decode($request->getPost('data'));
        foreach ($dataRaw as $row) {
            if ($row->jenis == 'brg') {
                $rap = $rapsModel->where(['rap_tipe' => $row->typeId, 'rap_barang' => $row->id])->first();
            }
            if ($row->jenis == 'ubk') {
                $rap = $rapsModel->where(['rap_tipe' => $row->typeId, 'rap_upah' => $row->id])->first();
            }
            if (!$rap) {
                $data[] = [
                    'rap_tipe'          => $row->typeId,
                    'rap_barang'        => $row->jenis == 'brg' ? $row->id : NULL,
                    'rap_upah'          => $row->jenis == 'ubk' ? $row->id : NULL,
                    'rap_volume'        => $row->quantity,
                    'rap_harga'         => $row->price,
                    'rap_keterangan'    => $row->rapKeterangan,
                    'rap_user'          => $this->session->get('usr_id')
                ];
            }
        }

        if (isset($data)) {
            $query = $rapsModel->insertBatch($data);
            if ($query) {
                return json_encode(['status' => true], true);
            } else {
                return json_encode(['status' => false], true);
            }
        } else {
            return json_encode(['status' => false], true);
        }
    }

    public function editRap($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editRap", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $rapsModel = new \App\Models\RapsModel();
        $rap = $rapsModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Barang / Pekerjaan',
            'rap'            => $rap,
            'rapsModel'       => $rapsModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/editRap', $data);
    }

    public function updateRap()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateRap", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $rapsModel = new \App\Models\RapsModel();

        $validate = $this->validate([
            'tipe' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'volume' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'harga' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editRap/' . $request->getVar('id'))->withInput();
        }

        $data = [
            'rap_tipe'          => $request->getVar('tipe'),
            'rap_barang'        => $request->getVar('barang') ? $request->getVar('barang') : NULL,
            'rap_upah'          => $request->getVar('ubk') ? $request->getVar('ubk') : NULL,
            'rap_volume'        => $request->getVar('volume'),
            'rap_harga'         => str_replace('.', '', $request->getVar('harga')),
            'rap_keterangan'    => $request->getVar('keterangan'),
            'rap_user'          => $this->session->get('usr_id')
        ];

        $query = $rapsModel->update(['rap_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return '<script>window.history.go(-2);</script>';
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editRap/' . $request->getVar('id'))->withInput();
        }
    }

    public function deleteRap()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteRap", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $rapsModel = new \App\Models\RapsModel();
            $rap = $rapsModel->find($id);
            if ($rap) {
                $query = $rapsModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function kaskecil()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;
        $kasKecilModel = new \App\Models\KasKecilModel();

        if ($request->getVar('q')) {
            $kasKecilModel->groupStart();
            $kasKecilModel->orLike(['kas_kecil.kk_nomor' => $request->getVar('q')]);
            $kasKecilModel->groupEnd();
        }

        $kaskecil = $kasKecilModel->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
            ->select('kas_kecil.*, user.usr_nama')
            ->orderBy('kas_kecil.kk_tanggal', 'DESC');

        $data = [
            'title_bar'         => 'Data Kas Kecil',
            'kaskecil'          => $kaskecil->paginate(100, 'view'),
            'kasKecilModel'     => $kasKecilModel,
            'pager'             => $kasKecilModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($kaskecil->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/kaskecil', $data);
    }

    public function addKaskecil()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addKaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Kas Kecil Baru',
            'validation'    => $this->validation
        ];
        return view('dashboard/addKaskecil', $data);
    }

    public function insertKaskecil()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertKaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'jenis' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'nomor' => [
                'rules'     => 'required|is_unique[kas_kecil.kk_nomor]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Nomor telah digunakan.'
                ]
            ],
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'penerima' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nominal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/addKaskecil')->withInput();
        }

        $data = [
            'kk_jenis'      => $request->getVar('jenis'),
            'kk_nomor'      => $request->getVar('nomor'),
            'kk_tanggal'    => date('Y-m-d H:i:s', strtotime($request->getVar('tanggal') . ' ' . date('H:i:s'))),
            'kk_uraian'     => $request->getVar('uraian'),
            'kk_nominal'    => str_replace('.', '', $request->getVar('nominal')),
            'kk_user'       => $request->getVar('penerima'),
            'kk_debet'      => $request->getVar('debet') ? $request->getVar('debet') : NULL,
            'kk_kredit'     => $request->getVar('kredit') ? $request->getVar('kredit') : NULL,
            'kk_approval'   => $this->session->get('usr_id'),
            'kk_status'     => 1
        ];

        $kasKecilModel = new \App\Models\KasKecilModel();
        $query = $kasKecilModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kaskecil');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addKaskecil')->withInput();
        }
    }

    public function statusKkJson($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateKaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $kasKecilModel = new \App\Models\KasKecilModel();
        $kaskecil = $kasKecilModel->find($id);
        return json_encode($kaskecil, true);
    }

    public function kasKecilJson($id)
    {
        $kasKecilModel = new \App\Models\KasKecilModel();

        $kaskecil = $kasKecilModel->find($id);
        $tglKasBerakhir = date('Y-m-d H:i:s', strtotime("+7 days", strtotime($kaskecil['kk_tanggal'])));
        $saldoAwal = str_replace(',', '.', $kaskecil['kk_nominal']);

        if ($kaskecil['kk_jenis'] == 1) {
            $pembelianModel = new \App\Models\PembelianModel();
            $ongkirModel = new \App\Models\OngkirPembelianModel();

            $pembelian = $pembelianModel->where('pb_kaskecil', $id)->findAll();
            $totalPembelian = 0;
            foreach ($pembelian as $row) {
                $totalPembelian += $row['pb_total'] ? str_replace(',', '.', $row['pb_total']) : 0;
            }

            $ongkir = $ongkirModel->where('op_kaskecil', $id)->findAll();
            $totalOngkir = 0;
            foreach ($ongkir as $row) {
                $totalOngkir += $row['op_bayar'] ? str_replace(',', '.', $row['op_bayar']) : 0;
            }
            $digunakan = ($totalPembelian + $totalOngkir);
            $saldo = ($saldoAwal - ($totalPembelian + $totalOngkir));
        } else if ($kaskecil['kk_jenis'] == 4) {
            $kasbonModel = new \App\Models\KasbonModel();
            $kasbon = $kasbonModel->where('bu_kaskecil', $id)->findAll();
            $totalKasbon = 0;
            foreach ($kasbon as $row) {
                $totalKasbon += $row['bu_nominal'] ? str_replace(',', '.', $row['bu_nominal']) : 0;
            }
            $digunakan = $totalKasbon;
            $saldo = $totalKasbon;
        } else {
            $operasionalModel = new \App\Models\OperasionalModel();
            $operasional = $operasionalModel->where('tl_kaskecil', $id)->findAll();
            $totalOperasional = 0;
            foreach ($operasional as $row) {
                $totalOperasional += $row['tl_nominal'] ? str_replace(',', '.', $row['tl_nominal']) : 0;
            }
            $digunakan = $totalOperasional;
            $saldo = $totalOperasional;
        }

        return json_encode([
            'kaskecil'  => $kaskecil,
            'diterima'  => $saldoAwal,
            'digunakan' => $digunakan,
            'saldo'     => $saldo
        ], true);
    }

    public function kasKecilJson2($id)
    {
        $kasKecilModel = new \App\Models\KasKecilModel();
        $bbModel = new \App\Models\BukuBesarModel();

        $kaskecil = $kasKecilModel->find($id);
        $nilaiKasKecil = $bbModel->getSaldoKasKecil($id);

        return json_encode([
            'kaskecil'      => $kaskecil,
            'diterima'      => $nilaiKasKecil['diterima'],
            'digunakan'     => $nilaiKasKecil['digunakan'],
            'dikembalikan'  => $nilaiKasKecil['dikembalikan'],
            'saldo'         => $nilaiKasKecil['saldo']
        ], true);
    }

    public function detailPembelianTunai($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();

        $kasKecilModel = new \App\Models\KasKecilModel();
        $pembelianModel = new \App\Models\PembelianModel();
        $kaskecil = $kasKecilModel->where('kk_id', $id)
            ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
            ->select('kas_kecil.*, user.usr_nama')->first();
        $tglKasBerakhir = date('Y-m-d H:i:s', strtotime("+7 days", strtotime($kaskecil['kk_tanggal'])));

        $pembelianModel->where('pembelian.pb_kaskecil', $id);
        $pembelianModel->join('suplier', 'suplier.suplier_id = pembelian.pb_supplier', 'left');
        $pembelian = $pembelianModel->select('pembelian.*, suplier.suplier_nama')
            ->orderBy('pembelian.pb_tanggal', 'DESC')->findAll();

        // $ongkirModel = new \App\Models\OngkirPembelianModel();
        // $ongkirModel->where(['ongkir_pembelian.op_kaskecil' => $id])
        //     ->join('hutangsuplier', 'hutangsuplier.hs_id = ongkir_pembelian.op_hutangsuplier', 'left')
        //     ->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left')
        //     ->join('kas_kecil', 'kas_kecil.kk_id = ongkir_pembelian.op_kaskecil', 'left')
        //     ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left');
        // $ongkir = $ongkirModel->select('ongkir_pembelian.*, hutangsuplier.hs_nomor, hutangsuplier.hs_pembelian, hutangsuplier.hs_suplier, hutangsuplier.hs_total, kas_kecil.kk_nomor, kas_kecil.kk_nominal, kas_kecil.kk_status, kas_kecil.kk_approval, user.usr_nama, suplier.suplier_nama')
        //     ->orderBy('ongkir_pembelian.op_tanggal', 'DESC')->findAll();

        $data = [
            'title_bar'         => 'Pembelian Kas Kecil ' . $kaskecil['kk_nomor'] . ' - ' . ucwords($kaskecil['usr_nama']),
            'pembelian'         => $pembelian,
            // 'ongkir'            => $ongkir,
            'kaskecil'          => $kaskecil,
            'validation'        => $this->validation
        ];
        return view('dashboard/detailPembelianTunai', $data);
    }

    public function detailOperasional($jenis, $idKk)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $kasKecilModel = new \App\Models\KasKecilModel();
        $operasionalModel = new \App\Models\OperasionalModel();
        $kaskecil = $kasKecilModel->where('kk_id', $idKk)
            ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
            ->select('kas_kecil.*, user.usr_nama')->first();
        $tglKasBerakhir = date('Y-m-d H:i:s', strtotime("+7 days", strtotime($kaskecil['kk_tanggal'])));

        $operasionalModel->where(['operasional.tl_kaskecil' => $idKk]);
        $operasionalModel->join('kas_kecil', 'kas_kecil.kk_id = operasional.tl_kaskecil', 'left');
        $operasionalModel->join('user', 'user.usr_id = kas_kecil.kk_user', 'left');
        $operasional = $operasionalModel->select('operasional.*, kas_kecil.kk_nomor, user.usr_nama')
            ->orderBy('operasional.tl_tanggal', 'DESC')->findAll();

        $ongkirModel = new \App\Models\OngkirPembelianModel();
        $ongkirModel->where(['ongkir_pembelian.op_kaskecil' => $idKk])
            ->join('hutangsuplier', 'hutangsuplier.hs_id = ongkir_pembelian.op_hutangsuplier', 'left')
            ->join('suplier', 'suplier.suplier_id = ongkir_pembelian.op_suplier', 'left')
            ->join('kas_kecil', 'kas_kecil.kk_id = ongkir_pembelian.op_kaskecil', 'left')
            ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left');
        $ongkir = $ongkirModel->select('ongkir_pembelian.*, hutangsuplier.hs_nomor, hutangsuplier.hs_pembelian, hutangsuplier.hs_suplier, hutangsuplier.hs_total, kas_kecil.kk_nomor, kas_kecil.kk_nominal, kas_kecil.kk_status, kas_kecil.kk_approval, user.usr_nama, suplier.suplier_nama')
            ->orderBy('ongkir_pembelian.op_tanggal', 'DESC')->findAll();

        $kkjenis = $kasKecilModel->getJenisKas($jenis);

        $data = [
            'title_bar'         => 'KAS KECIL ' . $kkjenis['name'] . ' - (' . $kaskecil['kk_nomor'] . ') ' . strtoupper($kaskecil['usr_nama']),
            'operasional'       => $operasional,
            'ongkir'            => $ongkir,
            'kaskecil'          => $kaskecil,
            'validation'        => $this->validation
        ];
        return view('dashboard/detailOperasional', $data);
    }

    public function detailKasbon($idKk)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $kasKecilModel = new \App\Models\KasKecilModel();
        $trxupahLainModel = new \App\Models\TrxupahLainModel();
        $kaskecil = $kasKecilModel->where('kk_id', $idKk)
            ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left')
            ->select('kas_kecil.*, user.usr_nama')->first();
        $tglKasBerakhir = date('Y-m-d H:i:s', strtotime("+7 days", strtotime($kaskecil['kk_tanggal'])));

        $trxupahLainModel->where(['trxupah_lain.tul_kaskecil' => $idKk]);
        $trxupahLainModel->join('kas_kecil', 'kas_kecil.kk_id = trxupah_lain.tul_kaskecil', 'left');
        $trxupahLainModel->join('user', 'user.usr_id = kas_kecil.kk_user', 'left');
        $kasbon = $trxupahLainModel->select('trxupah_lain.*, kas_kecil.kk_nomor, user.usr_nama')
            ->orderBy('trxupah_lain.tul_tanggal', 'DESC')->findAll();

        $kkjenis = $kasKecilModel->getJenisKas(4);

        $data = [
            'title_bar'         => 'KAS KECIL ' . $kkjenis['name'] . ' - (' . $kaskecil['kk_nomor'] . ') ' . strtoupper($kaskecil['usr_nama']),
            'kasbon'            => $kasbon,
            'kaskecil'          => $kaskecil,
            'validation'        => $this->validation
        ];
        return view('dashboard/detailKasbon', $data);
    }

    public function editKaskecil($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addKaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $kasKecilModel = new \App\Models\KasKecilModel();
        $kaskecil = $kasKecilModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Data Kas Kecil',
            'kaskecil'      => $kaskecil,
            'validation'    => $this->validation
        ];
        return view('dashboard/editKaskecil', $data);
    }

    public function updateKaskecil()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateKaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $kasKecilModel = new \App\Models\KasKecilModel();
        $old = $kasKecilModel->find($request->getVar('id'));

        if ($old['kk_nomor'] != $request->getVar('nomor')) {
            $rule_nomor = 'required|is_unique[kas_kecil.kk_nomor]';
        } else {
            $rule_nomor = 'required';
        }

        $validate = $this->validate([
            'jenis' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'nomor' => [
                'rules'     => $rule_nomor,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Nomor telah digunakan.'
                ]
            ],
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'penerima' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nominal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editKaskecil/' . $request->getVar('id'))->withInput();
        }

        $data = [
            'kk_jenis'      => $request->getVar('jenis'),
            'kk_nomor'      => $request->getVar('nomor'),
            'kk_tanggal'    => date('Y-m-d H:i:s', strtotime($request->getVar('tanggal') . ' ' . date('H:i:s'))),
            'kk_uraian'     => $request->getVar('uraian'),
            'kk_nominal'    => str_replace('.', '', $request->getVar('nominal')),
            'kk_user'       => $request->getVar('penerima'),
            'kk_debet'      => $request->getVar('debet') ? $request->getVar('debet') : NULL,
            'kk_kredit'     => $request->getVar('kredit') ? $request->getVar('kredit') : NULL,
            'kk_approval'   => $this->session->get('usr_id'),
            // 'kk_status'     => 1
        ];

        $query = $kasKecilModel->update(['kk_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kaskecil');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editKaskecil/' . $request->getVar('id'))->withInput();
        }
    }

    public function updateStatusKk()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateKaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $kasKecilModel = new \App\Models\KasKecilModel();
        // $kaskecil = $kasKecilModel->find($request->getVar('id'));
        $validate = $this->validate([
            'kembali' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'statusKasKecil' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/kaskecil');
        }

        $kembali = $request->getVar('kembali') ? str_replace('.', '', $request->getVar('kembali')) : 0;
        $data = [
            'kk_status'         => $request->getVar('statusKasKecil'),
            'kk_kembali'        => $request->getVar('statusKasKecil') == 2 ? $kembali : 0,
            'kk_kembaliDebet'   => $request->getVar('debet'),
            'kk_kembaliKredit'  => $request->getVar('kredit'),
            'kk_approval'       => $this->session->get('usr_id')
        ];

        $query = $kasKecilModel->update(['kk_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kaskecil');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kaskecil')->withInput();
        }
    }

    public function deleteKaskecil()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteKaskecil", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $kasKecilModel = new \App\Models\KasKecilModel();
            $kaskecil = $kasKecilModel->find($id);
            if ($kaskecil) {
                $query = $kasKecilModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function pembelian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("pembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $pembelianModel = new \App\Models\PembelianModel();
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $pembelianModel->where('pembelian.pb_tanggal >=', $start);
            $pembelianModel->where('pembelian.pb_tanggal <=', $end);
        }
        if ($request->getVar('supplier')) {
            $pembelianModel->where('pembelian.pb_supplier', $request->getVar('supplier'));
        }
        if ($request->getVar('jenis')) {
            $pembelianModel->where('pembelian.pb_jenis', $request->getVar('jenis'));
        }
        if ($request->getVar('faktur')) {
            $pembelianModel->where('pembelian.pb_nomor', $request->getVar('faktur'));
        }
        $pembelianModel->join('suplier', 'suplier.suplier_id = pembelian.pb_supplier', 'left');
        $pembelian = $pembelianModel->select('pembelian.*, suplier.suplier_nama')
            ->orderBy('pembelian.pb_tanggal', 'DESC');

        $data = [
            'title_bar'         => 'Data Pengajuan Barang',
            'pembelian'         => $pembelian->paginate(100, 'view'),
            'pembelianModel'    => $pembelianModel,
            'pager'             => $pembelianModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($pembelian->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/pembelian', $data);
    }

    public function addPembelian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addPembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Pengajuan Material',
            'validation'    => $this->validation
        ];
        return view('dashboard/addPembelian', $data);
    }

    public function insertPembelian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertPembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        // pembelian
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $pembelianModel = new \App\Models\PembelianModel();
        $pembelian = $pembelianModel->where('pb_nomor', $request->getPost('faktur'))->first();
        if ($pembelian) {
            $nomor = $pembelianModel->buatNoFaktur();
        } else {
            $nomor = $request->getPost('faktur');
        }

        $dataPembelian = [
            'pb_tanggal'    => $tanggal,
            'pb_nomor'      => $nomor,
            'pb_total'      => 0,
            'pb_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            'pb_jenis'      => NULL,
            'pb_kaskecil'   => NULL,
            'pb_debet'      => 35,
            'pb_status'     => 1,
            'pb_user'       => $this->session->get('usr_id')
        ];

        $queryPembelian = $pembelianModel->insert($dataPembelian);

        if ($queryPembelian) {
            $idPembelian = $pembelianModel->insertID();

            // item pembelian
            $dataRaw = json_decode($request->getPost('data'));
            foreach ($dataRaw as $row) {
                $itemPembelian[] = [
                    'pi_pembelian'  => $idPembelian,
                    'pi_barang'     => $row->id,
                    'pi_qtybeli'    => str_replace('.', ',', $row->quantity),
                    'pi_qtymasuk'   => 0,
                    'pi_qtydatang'  => 0,
                    'pi_harga'      => 0,
                    'pi_total'      => 0,
                    'pi_jenis'      => NULL,
                    'pi_suplier'    => NULL,
                    'pi_debet'      => 35,
                    'pi_kredit'     => NULL,
                    'pi_jatuhtempo' => NULL,
                    'created_at'    => $tanggal,
                    'updated_at'    => $tanggal
                ];
            }

            if (isset($itemPembelian)) {
                $itemPembelianModel = new \App\Models\PembelianItemModel();
                $itemPembelianModel->insertBatch($itemPembelian);
            }

            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => true, 'msg' => 'Data berhasil disimpan.'], true);
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => false, 'msg' => 'Data gagal disimpan.'], true);
        }
    }

    public function addNewItemPembelian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updatePembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');

        // add item pembelian
        $pembelianItemModel = new \App\Models\PembelianItemModel();
        $dataRaw = json_decode($request->getPost('data'));
        foreach ($dataRaw as $row) {
            $checkItem = $pembelianItemModel->where(['pi_pembelian' => $request->getPost('id'), 'pi_barang' => $row->id])->first();
            if ($checkItem) {
                $itemPembelian = [
                    'pi_pembelian'  => $request->getPost('id'),
                    'pi_barang'     => $row->id,
                    'pi_qtybeli'    => str_replace('.', ',', round($row->quantity)),
                    'created_at'    => $tanggal,
                    'updated_at'    => date('Y-m-d H:i:s')
                ];
                $pembelianItemModel->update(['pi_id' => $checkItem['pi_id']], $itemPembelian);
            } else {
                $itemPembelian = [
                    'pi_pembelian'  => $request->getPost('id'),
                    'pi_barang'     => $row->id,
                    'pi_qtybeli'    => str_replace('.', ',', round($row->quantity)),
                    'pi_qtymasuk'   => 0,
                    'pi_qtydatang'  => 0,
                    'pi_harga'      => 0,
                    'pi_total'      => 0,
                    'pi_jenis'      => NULL,
                    'pi_suplier'    => NULL,
                    'pi_debet'      => 35,
                    'pi_kredit'     => NULL,
                    'pi_jatuhtempo' => NULL,
                    'created_at'    => $tanggal,
                    'updated_at'    => $tanggal
                ];
                $pembelianItemModel->save($itemPembelian);
            }
        }

        $dataPembelian = [
            'pb_tanggal'    => $tanggal,
            'pb_nomor'      => $request->getPost('faktur'),
            'pb_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            'pb_user'       => $this->session->get('usr_id')
        ];

        $pembelianModel = new \App\Models\PembelianModel();
        $queryPembelian = $pembelianModel->update(['pb_id' => $request->getPost('id')], $dataPembelian);

        if ($queryPembelian) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => true, 'msg' => 'Data berhasil disimpan.'], true);
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => false, 'msg' => 'Data gagal disimpan.'], true);
        }
    }

    public function updateDataItemPembelian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updatePembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $pembelianModel = new \App\Models\PembelianModel();
        $itemPembelianModel = new \App\Models\PembelianItemModel();

        $total = $request->getPost('totalData');
        for ($i = 0; $i < $total; $i++) {
            $checkItem = $itemPembelianModel->find($request->getPost('piId' . $i));
            if ($checkItem) {
                $qtybeli = $request->getPost('qtybeli' . $i) ? str_replace('.', '', $request->getPost('qtybeli' . $i)) : 0;
                $dataItem = [
                    'pi_qtybeli'    => $qtybeli
                ];
                $itemPembelianModel->update(['pi_id' => $request->getPost('piId' . $i)], $dataItem);
            }
        }

        $pembelianModel = new \App\Models\PembelianModel();
        $dataPembelian = [
            'pb_status'     => $request->getPost('status'),
            'pb_user'       => $this->session->get('usr_id')
        ];

        $query = $pembelianModel->update(['pb_id' => $request->getPost('pbId')], $dataPembelian);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        }
    }

    public function editPembelian($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addPembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $pembelianModel = new \App\Models\PembelianModel();
        $pembelian = $pembelianModel->find($id);

        $data = [
            'title_bar'     => 'Perbarui Pengajuan Material',
            'pembelian'     => $pembelian,
            'validation'    => $this->validation
        ];
        if (in_array("approvalPembelian", $akses)) {
            return view('dashboard/editApprovePembelian', $data);
        } else {
            return view('dashboard/editPembelian', $data);
        }
    }

    public function detailPembelian($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("pembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $pembelianModel = new \App\Models\PembelianModel();
        $pembelian = $pembelianModel->find($id);

        $data = [
            'title_bar'     => 'Pembelian Material ' . $pembelian['pb_nomor'],
            'pembelian'     => $pembelian,
            'validation'    => $this->validation
        ];
        return view('dashboard/detailPembelian', $data);
    }

    public function itemPembelianJson($id)
    {
        $pembelianItemModel = new \App\Models\PembelianItemModel();
        $pembelian = $pembelianItemModel->where('pembelian_item.pi_id', $id)
            ->join('barang', 'barang.barang_id = pembelian_item.pi_barang', 'left')
            ->select('pembelian_item.*, barang.barang_nama')
            ->first();

        return json_encode($pembelian, true);
    }

    public function updatePembelian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updatePembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');

        // add item pembelian
        $pembelianItemModel = new \App\Models\PembelianItemModel();
        $dataRaw = json_decode($request->getPost('data'));
        foreach ($dataRaw as $row) {
            $checkItem = $pembelianItemModel->where(['pi_pembelian' => $request->getPost('id'), 'pi_barang' => $row->id])->first();
            if ($row->tempo > 0) {
                $tglTempo = date('Y-m-d H:i:s', strtotime("+" . $row->tempo . " days", strtotime($tanggal)));
            }
            if ($checkItem) {

                $harga = $checkItem['pi_harga'] ? str_replace(',', '.', $checkItem['pi_harga']) : 0;
                $qtybeli = $checkItem['pi_qtybeli'] ? str_replace(',', '.', $checkItem['pi_qtybeli']) : 0;
                $qtymasuk = $checkItem['pi_qtymasuk'] ? str_replace(',', '.', $checkItem['pi_qtymasuk']) : 0;
                $qtydatang = $checkItem['pi_qtydatang'] ? str_replace(',', '.', $checkItem['pi_qtydatang']) : 0;
                $itemPembelian = [
                    'pi_pembelian'  => $request->getPost('id'),
                    'pi_barang'     => $row->id,
                    'pi_qtybeli'    => str_replace('.', ',', round($row->quantity + $qtybeli, 2)),
                    'pi_qtymasuk'   => $request->getPost('jenisTrx') == 1 ? str_replace('.', ',', $row->quantity + $qtymasuk) : $qtymasuk,
                    'pi_qtydatang'  => $request->getPost('jenisTrx') == 1 ? str_replace('.', ',', $row->quantity + $qtydatang) : $qtydatang,
                    'pi_harga'      => str_replace('.', ',', $row->price),
                    'pi_total'      => $request->getPost('jenisTrx') == 1 ? str_replace('.', ',', ($row->quantity + $qtydatang) * $row->price) : $qtydatang * $harga,
                    'pi_jenis'      => $request->getPost('jenisTrx'),
                    'pi_suplier'    => $row->suplier ? $row->suplier : $checkItem['pi_suplier'],
                    'pi_debet'      => $row->debet ? $row->debet : $checkItem['pi_debet'],
                    'pi_kredit'     => $row->kredit ? $row->kredit : $checkItem['pi_kredit'],
                    'pi_jatuhtempo' => isset($tglTempo) ? $tglTempo : $checkItem['pi_jatuhtempo'],
                    'created_at'    => $tanggal,
                    'updated_at'    => date('Y-m-d H:i:s')
                ];
                $pembelianItemModel->update(['pi_id' => $checkItem['pi_id']], $itemPembelian);
            } else {
                $itemPembelian = [
                    'pi_pembelian'  => $request->getPost('id'),
                    'pi_barang'     => $row->id,
                    'pi_qtybeli'    => str_replace('.', ',', $row->quantity),
                    'pi_qtymasuk'   => $request->getPost('jenisTrx') == 1 ? str_replace('.', ',', $row->quantity) : 0,
                    'pi_qtydatang'  => $request->getPost('jenisTrx') == 1 ? str_replace('.', ',', $row->quantity) : 0,
                    'pi_harga'      => str_replace('.', ',', $row->price),
                    'pi_total'      => $request->getPost('jenisTrx') == 1 ? str_replace('.', ',', round($row->quantity * $row->price)) : 0,
                    'pi_jenis'      => $request->getPost('jenisTrx'),
                    'pi_suplier'    => $row->suplier ? $row->suplier : NULL,
                    'pi_debet'      => $row->debet ? $row->debet : NULL,
                    'pi_kredit'     => $row->kredit ? $row->kredit : NULL,
                    'pi_jatuhtempo' => isset($tglTempo) ? $tglTempo : NULL,
                    'created_at'    => $tanggal,
                    'updated_at'    => $tanggal
                ];
                $pembelianItemModel->save($itemPembelian);
            }

            $suplierIds[] = $row->suplier ? $row->suplier : NULL;
        }

        $itemPembelian = $pembelianItemModel->where('pi_pembelian', $request->getPost('id'))->findAll();
        $grandTotal = 0;
        foreach ($itemPembelian as $row) {
            $qtymasuk = $row['pi_qtydatang'] ? str_replace(',', '.', $row['pi_qtydatang']) : 0;
            $harga = $row['pi_harga'] ? str_replace(',', '.', $row['pi_harga']) : 0;
            if ($qtymasuk > 0) {
                $subtotal = $qtymasuk * $harga;
                $grandTotal += $subtotal;
            }
        }

        // pembelian
        if ($dataRaw[0]->tempo > 0) {
            $tglTempo = date('Y-m-d H:i:s', strtotime("+" . $dataRaw[0]->tempo . " days", strtotime($tanggal)));
        }

        $dataPembelian = [
            'pb_tanggal'    => $tanggal,
            'pb_nomor'      => $request->getPost('faktur'),
            'pb_total'      => str_replace('.', ',', round($grandTotal, 2)),
            'pb_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            'pb_jenis'      => $request->getPost('jenisTrx'),
            'pb_kaskecil'   => $request->getPost('kaskecil') ? $request->getPost('kaskecil') : NULL,
            'pb_status'     => $request->getPost('jenisTrx') == 1 ? 2 : 1,
            'pb_debet'      => $itemPembelian[0]['pi_debet'],
            'pb_kredit'     => NULL,
        ];

        $pembelianModel = new \App\Models\PembelianModel();
        $queryPembelian = $pembelianModel->update(['pb_id' => $request->getPost('id')], $dataPembelian);

        if ($queryPembelian) {
            $hsModel = new \App\Models\HutangsuplierModel();
            $hbModel = new \App\Models\HutangsuplierBayarModel();
            $itemPembelianModel = new \App\Models\PembelianItemModel();
            if ($grandTotal > 0) {
                $pembelian = $pembelianModel->find($request->getPost('id'));

                foreach (array_unique($suplierIds) as $sup) {
                    $pembelianItem = $itemPembelianModel->where(['pi_pembelian' => $request->getPost('id'), 'pi_suplier' => $sup])->findAll();

                    $gtSup = 0;
                    foreach ($pembelianItem as $item) {
                        $gtSup += $item['pi_total'];
                    }

                    $check = $hsModel->where(['hs_pembelian' => $request->getPost('id'), 'hs_suplier' => $sup])->first();
                    if ($check) {
                        // jika ada diupdate
                        $dataHs = [
                            'hs_tanggal'    => $tanggal,
                            'hs_pembelian'  => $request->getPost('id'),
                            'hs_suplier'    => $sup,
                            'hs_total'      => str_replace('.', ',', $gtSup),
                            'hs_tempo'      => isset($tglTempo) ? $tglTempo : NULL,
                            'hs_debet'      => $pembelianItem[0]['pi_debet'],
                            'hs_kredit'     => $pembelianItem[0]['pi_kredit'],
                            'hs_user'       => $this->session->get('usr_id')
                        ];
                        $queryHs = $hsModel->update(['hs_id' => $check['hs_id']], $dataHs);
                        if ($queryHs) {
                            // pembayaran hs tunai
                            if ($request->getPost('jenisTrx') == 1) {
                                $kkModel = new \App\Models\KasKecilModel();
                                $kaskecil = $kkModel->find($pembelian['pb_kaskecil']);
                                $hbCheckTunai = $hbModel->where(['hb_hutangsuplier' => $check['hs_id'], 'hb_istunai' => 1])->first();
                                if ($hbCheckTunai) {
                                    $dataHb = [
                                        'hb_tanggal'        => $pembelian['pb_tanggal'],
                                        'hb_hutangsuplier'  => $check['hs_id'],
                                        'hb_bayar'          => str_replace('.', ',', $gtSup),
                                        'hb_debet'          => $pembelianItem[0]['pi_kredit'],
                                        'hb_kredit'         => $kaskecil['kk_debet'],
                                        'hb_istunai'        => 1,
                                        'hb_user'           => $this->session->get('usr_id')
                                    ];
                                    $hbModel->update(['hb_id' => $hbCheckTunai['hb_id']], $dataHb);
                                } else {
                                    $dataHb = [
                                        'hb_tanggal'        => $pembelian['pb_tanggal'],
                                        'hb_nomor'          => $hbModel->buatNoFaktur(),
                                        'hb_hutangsuplier'  => $check['hs_id'],
                                        'hb_bayar'          => str_replace('.', ',', $gtSup),
                                        'hb_keterangan'     => 'PEMBELIAN TUNAI ' . $pembelian['pb_nomor'],
                                        'hb_debet'          => $pembelianItem[0]['pi_kredit'],
                                        'hb_kredit'         => $kaskecil['kk_debet'],
                                        'hb_istunai'        => 1,
                                        'hb_user'           => $this->session->get('usr_id')
                                    ];
                                    $hbModel->insert($dataHb);
                                }
                            }
                        }
                    } else {
                        // jika tidak ada buat faktur baru
                        $hsNomor = $hsModel->buatNoFaktur();
                        $dataHs = [
                            'hs_tanggal'    => $tanggal,
                            'hs_nomor'      => $hsNomor,
                            'hs_pembelian'  => $request->getPost('id'),
                            'hs_suplier'    => $sup,
                            'hs_total'      => str_replace('.', ',', $gtSup),
                            'hs_tempo'      => isset($tglTempo) ? $tglTempo : NULL,
                            'hs_debet'      => $pembelianItem[0]['pi_debet'],
                            'hs_kredit'     => $pembelianItem[0]['pi_kredit'],
                            'hs_user'       => $this->session->get('usr_id')
                        ];
                        $queryHs = $hsModel->insert($dataHs);
                        if ($queryHs) {
                            $idHs = $hsModel->insertID();
                            // pembayaran hs tunai
                            if ($request->getPost('jenisTrx') == 1) {
                                $kkModel = new \App\Models\KasKecilModel();
                                $kaskecil = $kkModel->find($pembelian['pb_kaskecil']);
                                $dataHb = [
                                    'hb_tanggal'        => $pembelian['pb_tanggal'],
                                    'hb_nomor'          => $hbModel->buatNoFaktur(),
                                    'hb_hutangsuplier'  => $idHs,
                                    'hb_bayar'          => str_replace('.', ',', $gtSup),
                                    'hb_keterangan'     => 'PEMBELIAN TUNAI ' . $pembelian['pb_nomor'],
                                    'hb_debet'          => $pembelianItem[0]['pi_kredit'],
                                    'hb_kredit'         => $kaskecil['kk_debet'],
                                    'hb_istunai'        => 1,
                                    'hb_user'           => $this->session->get('usr_id')
                                ];
                                $hbModel->insert($dataHb);
                            }
                        }
                    }
                }
            }

            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => true, 'pbId' => $request->getPost('id'), 'msg' => 'Data berhasil disimpan.'], true);
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => false, 'msg' => 'Data gagal disimpan.'], true);
        }
    }

    public function approvePembelian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updatePembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $pembelianModel = new \App\Models\PembelianModel();
        $itemPembelianModel = new \App\Models\PembelianItemModel();
        $suplierModel = new \App\Models\SuplierModel();
        $rekeningModel = new \App\Models\KoderekeningModel();

        $pemb = $pembelianModel->find($request->getPost('pbId'));
        $pembelianItemOld = $itemPembelianModel->where('pi_pembelian', $request->getPost('pbId'))->findAll();
        foreach ($pembelianItemOld as $row) {
            $suplierIdOld[] = $row['pi_suplier'] > 0 ? $row['pi_suplier'] : NULL;
        }

        $total = $request->getPost('totalData');
        if ($total > 0) {
            for ($i = 0; $i < $total; $i++) {
                $checkItem = $itemPembelianModel->find($request->getPost('piId' . $i));
                if ($checkItem) {
                    $suplier = $suplierModel->find($request->getPost('suplier' . $i));
                    $rekening = $rekeningModel->find($suplier['suplier_rekening']);
                    $kodeRek = $suplier && $rekening ? $rekening['rek_kode'] . '.' . $suplier['suplier_kode'] : null;
                    $rek = $kodeRek ? $rekeningModel->where(['rek_kode' => $kodeRek])->first() : null;

                    $tempo = $request->getPost('tempo' . $i);
                    if ($tempo > 0) {
                        $tglTempo = date('Y-m-d H:i:s', strtotime("+" . $tempo . " days", strtotime($pemb['pb_tanggal'])));
                    }
                    $qtybeli = $request->getPost('qtybeli' . $i) ? str_replace('.', '', $request->getPost('qtybeli' . $i)) : 0;
                    $qtysetuju = $request->getPost('qtysetuju' . $i) ? str_replace('.', '', $request->getPost('qtysetuju' . $i)) : 0;
                    $qtydatang = $request->getPost('qtydatang' . $i) ? str_replace('.', '', $request->getPost('qtydatang' . $i)) : 0;
                    $hargabrg = $request->getPost('hargabrg' . $i) ? str_replace('.', '', $request->getPost('hargabrg' . $i)) : 0;

                    $dataItem = [
                        'pi_qtybeli'    => $qtybeli,
                        'pi_qtymasuk'   => $qtysetuju,
                        'pi_qtydatang'  => $qtydatang,
                        'pi_harga'      => $hargabrg,
                        'pi_total'      => str_replace(',', '.', $qtydatang) * str_replace(',', '.', $hargabrg),
                        'pi_suplier'    => $request->getPost('suplier' . $i),
                        'pi_kredit'     => $rek ? $rek['rek_id'] : null,
                        'pi_jatuhtempo' => isset($tglTempo) ? $tglTempo : NULL,
                    ];
                    $itemPembelianModel->update(['pi_id' => $request->getPost('piId' . $i)], $dataItem);
                }
            }
        }

        $pembelianItem = $itemPembelianModel->where('pi_pembelian', $request->getPost('pbId'))->findAll();
        $grandTotal = 0;
        foreach ($pembelianItem as $row) {
            $qtydatang = $row['pi_qtydatang'] ? str_replace(',', '.', $row['pi_qtydatang']) : 0;
            $harga = $row['pi_harga'] ? str_replace(',', '.', $row['pi_harga']) : 0;
            if ($qtydatang > 0) {
                $subtotal = $qtydatang * $harga;
                $grandTotal += $subtotal;
            }

            $suplierIds[] = $row['pi_suplier'] > 0 ? $row['pi_suplier'] : NULL;
        }

        $pembelianModel = new \App\Models\PembelianModel();
        // approvalPembelian
        $dataPembelian = [
            'pb_total'      => $grandTotal,
            'pb_status'     => $request->getPost('status'),
            'pb_debet'      => $pembelianItem[0]['pi_debet'],
            'pb_kredit'     => NULL,
            'pb_approval'   => $this->session->get('usr_id')
        ];

        $query = $pembelianModel->update(['pb_id' => $request->getPost('pbId')], $dataPembelian);
        if ($query) {
            $hsModel = new \App\Models\HutangsuplierModel();
            $hsBayarModel = new \App\Models\HutangsuplierBayarModel();
            $kkModel = new \App\Models\KasKecilModel();

            $supOld = array_unique($suplierIdOld);
            $supNew = array_unique($suplierIds);
            $supDiff = array_diff($supOld, $supNew);
            foreach ($supDiff as $sd) {
                $hsDel = $hsModel->where(['hs_pembelian' => $request->getPost('pbId'), 'hs_suplier' => $sd])->first();
                if ($hsDel) {
                    $hsModel->delete($hsDel['hs_id']);
                    $hsBayarModel->where('hb_hutangsuplier', $hsDel['hs_id'])->delete();
                }
            }

            if ($request->getPost('status') == 2) {
                if ($grandTotal > 0) {
                    $pembelian = $pembelianModel->find($request->getPost('pbId'));
                    foreach (array_unique($suplierIds) as $sup) {
                        $pembelianItem2 = $itemPembelianModel->where(['pi_pembelian' => $request->getPost('pbId'), 'pi_suplier' => $sup])->findAll();

                        $gtSup = 0;
                        foreach ($pembelianItem2 as $item) {
                            $gtSup += $item['pi_total'];
                        }

                        $check = $hsModel->where(['hs_pembelian' => $request->getPost('pbId'), 'hs_suplier' => $sup])->first();
                        if ($check) {
                            $dataHs = [
                                'hs_tanggal'    => $pembelian['pb_tanggal'],
                                'hs_pembelian'  => $request->getPost('pbId'),
                                'hs_suplier'    => $sup,
                                'hs_total'      => str_replace('.', ',', $gtSup),
                                'hs_tempo'      => $pembelianItem2[0]['pi_jatuhtempo'],
                                'hs_debet'      => $pembelianItem2[0]['pi_debet'],
                                'hs_kredit'     => $pembelianItem2[0]['pi_kredit'],
                                'hs_user'       => $this->session->get('usr_id')
                            ];
                            $queryHs = $hsModel->update(['hs_id' => $check['hs_id']], $dataHs);
                            if ($queryHs) {
                                if ($request->getPost('jenisTrxId') == 1) {
                                    $kaskecil = $kkModel->find($pembelian['pb_kaskecil']);
                                    $hbCheckTunai = $hsBayarModel->where(['hb_hutangsuplier' => $check['hs_id'], 'hb_istunai' => 1])->first();
                                    if ($hbCheckTunai) {
                                        $dataHb = [
                                            'hb_tanggal'        => $pembelian['pb_tanggal'],
                                            'hb_hutangsuplier'  => $check['hs_id'],
                                            'hb_bayar'          => str_replace('.', ',', $gtSup),
                                            'hb_debet'          => $pembelianItem2[0]['pi_kredit'],
                                            'hb_kredit'         => $kaskecil['kk_debet'],
                                            'hb_istunai'        => 1,
                                            'hb_user'           => $this->session->get('usr_id')
                                        ];
                                        $hsBayarModel->update(['hb_id' => $hbCheckTunai['hb_id']], $dataHb);
                                    } else {
                                        $dataHb = [
                                            'hb_tanggal'        => $pembelian['pb_tanggal'],
                                            'hb_nomor'          => $hsBayarModel->buatNoFaktur(),
                                            'hb_hutangsuplier'  => $check['hs_id'],
                                            'hb_bayar'          => str_replace('.', ',', $gtSup),
                                            'hb_keterangan'     => 'PEMBELIAN TUNAI ' . $pembelian['pb_nomor'],
                                            'hb_debet'          => $pembelianItem2[0]['pi_kredit'],
                                            'hb_kredit'         => $kaskecil['kk_debet'],
                                            'hb_istunai'        => 1,
                                            'hb_user'           => $this->session->get('usr_id')
                                        ];
                                        $hsBayarModel->insert($dataHb);
                                    }
                                }
                            }
                        } else {
                            $dataHs = [
                                'hs_tanggal'    => $pembelian['pb_tanggal'],
                                'hs_nomor'      => $hsModel->buatNoFaktur(),
                                'hs_pembelian'  => $request->getPost('pbId'),
                                'hs_suplier'    => $sup,
                                'hs_total'      => str_replace('.', ',', $gtSup),
                                'hs_tempo'      => $pembelianItem2[0]['pi_jatuhtempo'],
                                'hs_debet'      => $pembelianItem2[0]['pi_debet'],
                                'hs_kredit'     => $pembelianItem2[0]['pi_kredit'],
                                'hs_user'       => $this->session->get('usr_id')
                            ];
                            $queryHs = $hsModel->insert($dataHs);
                            if ($queryHs) {
                                $idHs = $hsModel->insertID();
                                if ($request->getPost('jenisTrxId') == 1) {
                                    $kaskecil = $kkModel->find($pembelian['pb_kaskecil']);
                                    $dataHb = [
                                        'hb_tanggal'        => $pembelian['pb_tanggal'],
                                        'hb_nomor'          => $hsBayarModel->buatNoFaktur(),
                                        'hb_hutangsuplier'  => $idHs,
                                        'hb_bayar'          => str_replace('.', ',', $gtSup),
                                        'hb_keterangan'     => 'PEMBELIAN TUNAI ' . $pembelian['pb_nomor'],
                                        'hb_debet'          => $pembelianItem2[0]['pi_kredit'],
                                        'hb_kredit'         => $kaskecil['kk_debet'],
                                        'hb_istunai'        => 1,
                                        'hb_user'           => $this->session->get('usr_id')
                                    ];
                                    $hsBayarModel->insert($dataHb);
                                }
                            }
                        }
                    }
                }
            }

            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        }
    }

    public function updateFakturPembelian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updatePembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $pembelianModel = new \App\Models\PembelianModel();
        $itemPembelianModel = new \App\Models\PembelianItemModel();

        $validate = $this->validate([
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'faktur' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        }

        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        if (in_array("approvalPembelian", $akses)) {
            $dataFakturPembelian = [
                'pb_tanggal'    => $tanggal,
                'pb_nomor'      => $request->getPost('faktur'),
                'pb_jenis'      => $request->getPost('jenisTrx'),
                'pb_kaskecil'   => $request->getPost('kaskecil') ? $request->getPost('kaskecil') : NULL,
                'pb_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL
            ];
        } else {
            $dataFakturPembelian = [
                'pb_tanggal'    => $tanggal,
                'pb_nomor'      => $request->getPost('faktur'),
                'pb_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL
            ];
        }
        $queryPembelian = $pembelianModel->update(['pb_id' => $request->getPost('id')], $dataFakturPembelian);
        if ($queryPembelian) {
            $itemPembelian = $itemPembelianModel->where('pi_pembelian', $request->getPost('id'))->findAll();
            $rekeningModel = new \App\Models\KoderekeningModel();
            $suplierModel = new \App\Models\SuplierModel();
            foreach ($itemPembelian as $item) {
                if ($item['pi_suplier']) {
                    $suplier = $suplierModel->find($item['pi_suplier']);
                    $rekening = $rekeningModel->find($suplier['suplier_rekening']);
                    $kodeRek = $rekening['rek_kode'] . '.' . $suplier['suplier_kode'];
                    $reksup = $rekeningModel->where('rek_kode', $kodeRek)->first();
                }
                $updateTanggalItem = [
                    'pi_jenis'      => $request->getPost('jenisTrx'),
                    'pi_kredit'     => isset($reksup) ? $reksup['rek_id'] : NULL,
                    'created_at'    => $tanggal
                ];
                $itemPembelianModel->update(['pi_id' => $item['pi_id']], $updateTanggalItem);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        }
    }

    public function deleteItemPembelian($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deletePembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        if ($id) {
            $pembelianItemModel = new \App\Models\PembelianItemModel();
            $pembelian = $pembelianItemModel->find($id);
            if ($pembelian) {
                $query = $pembelianItemModel->delete($id);
                if ($query) {
                    $itemPembelian = $pembelianItemModel->where('pi_pembelian', $pembelian['pi_pembelian'])->findAll();
                    $grandTotal = 0;
                    foreach ($itemPembelian as $row) {
                        $qtymasuk = $row['pi_qtydatang'] ? str_replace(',', '.', $row['pi_qtydatang']) : 0;
                        $harga = $row['pi_harga'] ? str_replace(',', '.', $row['pi_harga']) : 0;
                        if ($qtymasuk > 0) {
                            $subtotal = $qtymasuk * $harga;
                            $grandTotal += $subtotal;
                        }
                    }

                    $dataPembelian = [
                        'pb_total'      => str_replace('.', ',', round($grandTotal, 2)),
                        'pb_user'       => $this->session->get('usr_id')
                    ];
                    $pembelianModel = new \App\Models\PembelianModel();
                    $pembelianModel->update(['pb_id' => $pembelian['pi_pembelian']], $dataPembelian);

                    $pembelian = $pembelianModel->find($pembelian['pi_pembelian']);
                    if ($grandTotal > 0) {
                        // hutang supplier
                        $hsModel = new \App\Models\HutangsuplierModel();
                        $check = $hsModel->where(['hs_pembelian' => $pembelian['pb_id'], 'hs_suplier' => $pembelian['pb_supplier']])->first();
                        if ($check) {
                            // jika ada, update
                            $data = [
                                'hs_tanggal'    => $pembelian['pb_tanggal'],
                                'hs_pembelian'  => $pembelian['pb_id'],
                                'hs_suplier'    => $pembelian['pb_supplier'],
                                'hs_total'      => str_replace('.', ',', round($grandTotal, 2)),
                                'hs_user'       => $this->session->get('usr_id')
                            ];
                            $hsModel->update(['hs_id' => $check['hs_id']], $data);
                        }
                    }

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function deletePembelian()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deletePembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $pembelianModel = new \App\Models\PembelianModel();
            $pembelian = $pembelianModel->find($id);
            if ($pembelian) {
                $query = $pembelianModel->delete($id);
                if ($query) {
                    $pembelianItemModel = new \App\Models\PembelianItemModel();
                    $pembelianItemModel->where('pi_pembelian', $id)->delete();

                    // hutang supplier
                    $hsModel = new \App\Models\HutangsuplierModel();
                    $hbModel = new \App\Models\HutangsuplierBayarModel();
                    $hs = $hsModel->where(['hs_pembelian' => $pembelian['pb_id']])->findAll();

                    $hsModel->where(['hs_pembelian' => $pembelian['pb_id']])->delete();
                    foreach ($hs as $row) {
                        $hbModel->where('hb_hutangsuplier', $row['hs_id'])->delete();
                    }

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function ongkoskirim()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("ongkoskirim", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $ongkirModel = new \App\Models\OngkirPembelianModel();
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $ongkirModel->where('ongkir_pembelian.op_tanggal >=', $start);
            $ongkirModel->where('ongkir_pembelian.op_tanggal <=', $end);
        }
        if ($request->getVar('faktur')) {
            $ongkirModel->where('ongkir_pembelian.op_nomor', $request->getVar('faktur'));
        }
        $ongkirModel->join('hutangsuplier', 'hutangsuplier.hs_id = ongkir_pembelian.op_hutangsuplier', 'left')
            ->join('suplier', 'suplier.suplier_id = ongkir_pembelian.op_suplier', 'left')
            ->join('kas_kecil', 'kas_kecil.kk_id = ongkir_pembelian.op_kaskecil', 'left')
            ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left');
        $ongkir = $ongkirModel->select('ongkir_pembelian.*, hutangsuplier.hs_nomor, hutangsuplier.hs_pembelian, hutangsuplier.hs_suplier, hutangsuplier.hs_total, kas_kecil.kk_nomor, kas_kecil.kk_nominal, kas_kecil.kk_status, kas_kecil.kk_approval, user.usr_nama, suplier.suplier_nama')
            ->orderBy('ongkir_pembelian.op_tanggal', 'DESC');

        $data = [
            'title_bar'         => 'Data Ongkos Kirim',
            'ongkir'            => $ongkir->paginate(100, 'view'),
            'ongkirModel'       => $ongkirModel,
            'pager'             => $ongkirModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($ongkir->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/ongkoskirim', $data);
    }

    public function addOngkir()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addOngkir", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Bayar Ongkos Kirim',
            'validation'    => $this->validation
        ];
        return view('dashboard/addOngkir', $data);
    }

    public function insertOngkir()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertOngkir", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'faktur' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nomor' => [
                'rules'     => 'required|is_unique[ongkir_pembelian.op_nomor]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Nomor sudah digunakan.'
                ]
            ],
            'nominal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kaskecil' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/addOngkir')->withInput();
        }

        $ongkirModel = new \App\Models\OngkirPembelianModel();
        $hsModel = new \App\Models\HutangsuplierModel();
        $hs = $hsModel->find($request->getPost('faktur'));
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $dataTrx = [
            'op_tanggal'        => $tanggal,
            'op_nomor'          => $request->getPost('nomor'),
            'op_hutangsuplier'  => $request->getPost('faktur'),
            'op_suplier'        => $hs['hs_suplier'],
            'op_bayar'          => str_replace('.', '', $request->getPost('nominal')),
            'op_kaskecil'       => $request->getPost('kaskecil'),
            'op_keterangan'     => $request->getPost('uraian') ? $request->getPost('uraian') : NULL,
            'op_debet'          => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'op_kredit'         => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'op_user'           => $this->session->get('usr_id')
        ];

        $queryTrx = $ongkirModel->insert($dataTrx);
        if ($queryTrx) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/ongkoskirim');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addOngkir')->withInput();
        }
    }

    public function editOngkir($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editOngkir", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $ongkirModel = new \App\Models\OngkirPembelianModel();
        $ongkir = $ongkirModel->find($id);
        $data = [
            'title_bar'     => 'Bayar Ongkos Kirim',
            'ongkir'        => $ongkir,
            'validation'    => $this->validation
        ];
        return view('dashboard/editOngkir', $data);
    }

    public function updateOngkir()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateOngkir", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $ongkirModel = new \App\Models\OngkirPembelianModel();
        $ongkir = $ongkirModel->find($request->getPost('id'));

        if ($ongkir['op_nomor'] == $request->getVar('nomor')) {
            $rule_nomor = 'required';
        } else {
            $rule_nomor = 'required|is_unique[ongkir_pembelian.op_nomor]';
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'faktur' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nomor' => [
                'rules'     => $rule_nomor,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Nomor sudah digunakan.'
                ]
            ],
            'nominal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kaskecil' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editOngkir/' . $request->getPost('id'))->withInput();
        }

        $ongkirModel = new \App\Models\OngkirPembelianModel();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        // $hsModel = new \App\Models\HutangsuplierModel();
        // $hs = $hsModel->find($request->getPost('faktur'));
        $dataTrx = [
            'op_tanggal'        => $tanggal,
            'op_nomor'          => $request->getPost('nomor'),
            'op_hutangsuplier'  => $request->getPost('faktur'),
            'op_suplier'        => $request->getPost('faktur'),
            'op_bayar'          => str_replace('.', '', $request->getPost('nominal')),
            'op_kaskecil'       => $request->getPost('kaskecil'),
            'op_keterangan'     => $request->getPost('uraian') ? $request->getPost('uraian') : NULL,
            'op_debet'          => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'op_kredit'         => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'op_user'           => $this->session->get('usr_id')
        ];

        $queryTrx = $ongkirModel->update(['op_id' => $request->getPost('id')], $dataTrx);
        if ($queryTrx) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/ongkoskirim');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editOngkir/' . $request->getPost('id'))->withInput();
        }
    }

    public function deleteOngkir()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteOngkir", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $ongkirModel = new \App\Models\OngkirPembelianModel();
            $ongkir = $ongkirModel->find($id);
            if ($ongkir) {
                $query = $ongkirModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function operasional()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("operasional", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $opModel = new \App\Models\OperasionalModel();
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $opModel->where('operasional.tl_tanggal >=', $start);
            $opModel->where('operasional.tl_tanggal <=', $end);
        }
        if ($request->getVar('faktur')) {
            $opModel->where('operasional.tl_nomor', $request->getVar('faktur'));
        }
        $opModel->join('kas_kecil', 'kas_kecil.kk_id = operasional.tl_kaskecil', 'left')
            ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left');
        $operasional = $opModel->select('operasional.*, kas_kecil.kk_nomor, user.usr_nama')
            ->orderBy('operasional.tl_tanggal', 'DESC');

        $data = [
            'title_bar'         => 'Data Transaksi Operasional',
            'operasional'       => $operasional->paginate(100, 'view'),
            'opModel'           => $opModel,
            'pager'             => $opModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($operasional->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/operasional', $data);
    }

    // public function generateTo()
    // {
    //     $opModel = new \App\Models\OperasionalModel();
    //     $operasional = $opModel->orderBy('tl_tanggal', 'ASC')->findAll();
    //     $data = [];
    //     foreach ($operasional as $index => $row) {
    //         $newNumb = $index + 1;
    //         $fixNumber = 'TO' . str_pad($newNumb, 4, "0", STR_PAD_LEFT);
    //         $data[] = [
    //             'tl_id' => $row['tl_id'],
    //             'tl_nomor' => $fixNumber
    //         ];
    //     }
    //     if ($data) {
    //         $opModel->updateBatch($data, 'tl_id');
    //         dd($opModel->orderBy('tl_tanggal', 'ASC')->findAll());
    //     }
    // }

    public function addOperasional()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addOperasional", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Transaksi Operasional',
            'validation'    => $this->validation
        ];
        return view('dashboard/addOperasional', $data);
    }

    public function insertOperasional()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertOperasional", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nomor' => [
                'rules'     => 'required|is_unique[operasional.tl_nomor]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Nomor sudah digunakan.'
                ]
            ],
            'nominal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'transaksi' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kaskecil' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/addOperasional')->withInput();
        }

        $opMOdel = new \App\Models\OperasionalModel();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $dataTrx = [
            'tl_tanggal'        => $tanggal,
            'tl_nomor'          => $request->getPost('nomor'),
            'tl_jenis'          => $request->getPost('transaksi'),
            'tl_nominal'        => str_replace('.', '', $request->getPost('nominal')),
            'tl_kaskecil'       => $request->getPost('kaskecil'),
            'tl_keterangan'     => $request->getPost('uraian') ? $request->getPost('uraian') : NULL,
            'tl_debet'          => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'tl_kredit'         => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'tl_user'           => $this->session->get('usr_id')
        ];

        $queryTrx = $opMOdel->insert($dataTrx);
        if ($queryTrx) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/operasional');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addOperasional')->withInput();
        }
    }

    public function editOperasional($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editOperasional", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $opMOdel = new \App\Models\OperasionalModel();
        $operasional = $opMOdel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Transaksi Operasional',
            'operasional'   => $operasional,
            'validation'    => $this->validation
        ];
        return view('dashboard/editOperasional', $data);
    }

    public function updateOperasional()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateOperasional", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $opModel = new \App\Models\OperasionalModel();
        $operasional = $opModel->find($request->getPost('id'));

        if ($operasional['tl_nomor'] == $request->getVar('nomor')) {
            $rule_nomor = 'required';
        } else {
            $rule_nomor = 'required|is_unique[operasional.tl_nomor]';
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nomor' => [
                'rules'     => $rule_nomor,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Nomor sudah digunakan.'
                ]
            ],
            'nominal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'transaksi' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kaskecil' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editOperasional/' . $request->getPost('id'))->withInput();
        }

        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $dataTrx = [
            'tl_tanggal'        => $tanggal,
            'tl_nomor'          => $request->getPost('nomor'),
            'tl_jenis'          => $request->getPost('transaksi'),
            'tl_nominal'        => str_replace('.', '', $request->getPost('nominal')),
            'tl_kaskecil'       => $request->getPost('kaskecil'),
            'tl_keterangan'     => $request->getPost('uraian') ? $request->getPost('uraian') : NULL,
            'tl_debet'          => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'tl_kredit'         => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'tl_user'           => $this->session->get('usr_id')
        ];

        $queryTrx = $opModel->update(['tl_id' => $request->getPost('id')], $dataTrx);
        if ($queryTrx) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/operasional');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editOperasional/' . $request->getPost('id'))->withInput();
        }
    }

    public function deleteOperasional()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteOperasional", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $opModel = new \App\Models\OperasionalModel();
            $operasional = $opModel->find($id);
            if ($operasional) {
                $query = $opModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function kasbon()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kasbon", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $kasbonModel = new \App\Models\KasbonModel();
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $kasbonModel->where('bon_utang.bu_tanggal >=', $start);
            $kasbonModel->where('bon_utang.bu_tanggal <=', $end);
        }
        if ($request->getVar('faktur')) {
            $kasbonModel->where('bon_utang.bu_nomor', $request->getVar('faktur'));
        }
        $kasbonModel->join('kas_kecil', 'kas_kecil.kk_id = bon_utang.bu_kaskecil', 'left')
            ->join('user', 'user.usr_id = kas_kecil.kk_user', 'left');
        $kasbon = $kasbonModel->select('bon_utang.*, kas_kecil.kk_nomor, user.usr_nama')
            ->orderBy('bon_utang.bu_tanggal', 'DESC');

        $data = [
            'title_bar'         => 'Data Transaksi Kas Bon / Utang',
            'kasbon'            => $kasbon->paginate(100, 'view'),
            'kasbonModel'       => $kasbonModel,
            'pager'             => $kasbonModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($kasbon->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/kasbon', $data);
    }

    public function addKasbon()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addKasbon", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Transaksi Kas Bon',
            'validation'    => $this->validation
        ];
        return view('dashboard/addKasbon', $data);
    }

    public function insertKasbon()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertKasbon", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nomor' => [
                'rules'     => 'required|is_unique[bon_utang.bu_nomor]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Nomor sudah digunakan.'
                ]
            ],
            'nominal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'tukang' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kaskecil' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/addKasbon')->withInput();
        }

        $kasbonModel = new \App\Models\KasbonModel();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $dataTrx = [
            'bu_tanggal'        => $tanggal,
            'bu_nomor'          => $request->getPost('nomor'),
            'bu_tukang'         => $request->getPost('tukang'),
            'bu_nominal'        => str_replace('.', '', $request->getPost('nominal')),
            'bu_kaskecil'       => $request->getPost('kaskecil'),
            'bu_keterangan'     => $request->getPost('uraian') ? $request->getPost('uraian') : NULL,
            'bu_debet'          => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'bu_kredit'         => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'bu_user'           => $this->session->get('usr_id')
        ];

        $queryTrx = $kasbonModel->insert($dataTrx);
        if ($queryTrx) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kasbon');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/addKasbon')->withInput();
        }
    }

    public function editKasbon($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editKasbon", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $kasbonModel = new \App\Models\KasbonModel();
        $kasbon = $kasbonModel->find($id);
        $data = [
            'title_bar'     => 'Perbarui Transaksi Kas Bon',
            'kasbon'        => $kasbon,
            'validation'    => $this->validation
        ];
        return view('dashboard/editKasbon', $data);
    }

    public function kasbonTukangJson($idTukang)
    {
        $kasbonModel = new \App\Models\KasbonModel();
        $kasbon = $kasbonModel->where(['bu_tukang' => $idTukang, 'bu_selected' => 0])->findAll();
        return json_encode($kasbon, true);
    }

    public function updateKasbon()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateKasbon", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $kasbonModel = new \App\Models\KasbonModel();
        $kasbon = $kasbonModel->find($request->getPost('id'));

        if ($kasbon['bu_nomor'] == $request->getVar('nomor')) {
            $rule_nomor = 'required';
        } else {
            $rule_nomor = 'required|is_unique[bon_utang.bu_nomor]';
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nomor' => [
                'rules'     => $rule_nomor,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                    'is_unique' => 'Nomor sudah digunakan.'
                ]
            ],
            'nominal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'tukang' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kaskecil' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/editKasbon/' . $request->getPost('id'))->withInput();
        }

        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $dataTrx = [
            'bu_tanggal'        => $tanggal,
            'bu_nomor'          => $request->getPost('nomor'),
            'bu_tukang'         => $request->getPost('tukang'),
            'bu_nominal'        => str_replace('.', '', $request->getPost('nominal')),
            'bu_kaskecil'       => $request->getPost('kaskecil'),
            'bu_keterangan'     => $request->getPost('uraian') ? $request->getPost('uraian') : NULL,
            'bu_debet'          => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'bu_kredit'         => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'bu_user'           => $this->session->get('usr_id')
        ];

        $queryTrx = $kasbonModel->update(['bu_id' => $request->getPost('id')], $dataTrx);
        if ($queryTrx) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/kasbon');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/editKasbon/' . $request->getPost('id'))->withInput();
        }
    }

    public function deleteKasbon()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteKasbon", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $kasbonModel = new \App\Models\KasbonModel();
            $kasbon = $kasbonModel->find($id);
            if ($kasbon) {
                $query = $kasbonModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function barangkeluar()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("barangkeluar", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $barangKeluarModel->where('barangkeluar.bk_tanggal >=', $start);
            $barangKeluarModel->where('barangkeluar.bk_tanggal <=', $end);
        }
        if ($request->getVar('faktur')) {
            $barangKeluarModel->where('barangkeluar.bk_nomor', $request->getVar('faktur'));
        }
        $barangKeluarModel->join('unit', 'unit.unit_id = barangkeluar.bk_unit', 'left')
            ->join('types', 'types.type_id = unit.unit_tipe', 'left');
        $barangkeluar = $barangKeluarModel->select('barangkeluar.*, unit.unit_nama, types.type_nama')
            ->orderBy('barangkeluar.bk_tanggal', 'DESC');

        $data = [
            'title_bar'         => 'Data Barang Keluar',
            'barangkeluar'      => $barangkeluar->paginate(100, 'view'),
            'barangKeluarModel' => $barangKeluarModel,
            'pager'             => $barangKeluarModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($barangkeluar->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/barangkeluar', $data);
    }

    public function addBarangKeluar()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addBarangKeluar", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Barang Keluar',
            'validation'    => $this->validation
        ];
        return view('dashboard/addBarangKeluar', $data);
    }

    public function getRekeningUnit($idUnit)
    {
        $unitModel = new \App\Models\UnitModel();
        $rekeningModel = new \App\Models\KoderekeningModel();
        $unit = $unitModel->find($idUnit);

        $rekeningUnit = $rekeningModel->find($unit['unit_rekening']);
        $kodeRek = $rekeningUnit['rek_kode'] . '.' . $unit['unit_kode'];

        $rekening = $rekeningModel->where(['rek_kode' => $kodeRek])->first();
        return json_encode($rekening, true);
    }

    public function insertBarangKeluar()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertBarangKeluar", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        // barang keluar
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $brgkeluar = $barangKeluarModel->where('bk_nomor', $request->getPost('faktur'))->first();
        if ($brgkeluar) {
            $nomor = $barangKeluarModel->buatNoFaktur();
        } else {
            $nomor = $request->getPost('faktur');
        }
        $dataKeluar = [
            'bk_tanggal'    => $tanggal,
            'bk_nomor'      => $nomor,
            'bk_unit'       => $request->getPost('unit'),
            'bk_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            'bk_debet'      => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'bk_kredit'     => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'bk_user'       => $this->session->get('usr_id')
        ];

        $queryKeluar = $barangKeluarModel->insert($dataKeluar);

        if ($queryKeluar) {
            $idKeluar = $barangKeluarModel->insertID();

            // item barang keluar
            $dataRaw = json_decode($request->getPost('data'));
            foreach ($dataRaw as $row) {
                $itemKeluar[] = [
                    'bki_barangkeluar'  => $idKeluar,
                    'bki_barang'        => $row->id,
                    'bki_qty'           => str_replace('.', ',', $row->quantity),
                    'bki_harga'         => str_replace('.', ',', $row->price),
                    'created_at'        => $tanggal,
                    'updated_at'        => $tanggal
                ];
            }

            if (isset($itemKeluar)) {
                $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
                $barangKeluarItemModel->insertBatch($itemKeluar);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => true, 'bkId' => $idKeluar, 'msg' => 'Data berhasil disimpan.'], true);
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => false, 'msg' => 'Data gagal disimpan.'], true);
        }
    }

    public function editBarangKeluar($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editBarangKeluar", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $barangkeluar = $barangKeluarModel->find($id);

        $data = [
            'title_bar'     => 'Perbarui Barang Keluar',
            'barangkeluar'  => $barangkeluar,
            'validation'    => $this->validation
        ];
        return view('dashboard/editBarangKeluar', $data);
    }

    public function itemBarangKeluarJson($id)
    {
        $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
        $barangkeluar = $barangKeluarItemModel->where('barangkeluar_item.bki_id', $id)
            ->join('barang', 'barang.barang_id = barangkeluar_item.bki_barang', 'left')
            ->select('barangkeluar_item.*, barang.barang_nama')
            ->first();

        return json_encode($barangkeluar, true);
    }

    public function updateItemBarangKeluar()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateBarangKeluar", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $qty = $request->getVar('qtyOut') ? str_replace('.', '', $request->getVar('qtyOut')) : 0;
        $harga = $request->getVar('hargaOut') ? str_replace('.', '', $request->getVar('hargaOut')) : 0;

        $data = [
            'bki_qty'       => $qty,
            'bki_harga'     => $harga,
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
        $query = $barangKeluarItemModel->update(['bki_id' => $request->getVar('bid')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return '<script>window.history.go(-1);</script>';
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return '<script>window.history.go(-1);</script>';
        }
    }

    public function updateKeluarStatusRek()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateBarangKeluar", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $dataKeluar = [
            'bk_tanggal'    => $tanggal,
            'bk_nomor'      => $request->getPost('faktur'),
            'bk_unit'       => $request->getPost('unit'),
            'bk_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            'bk_debet'      => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'bk_kredit'     => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'bk_user'       => $this->session->get('usr_id')
        ];
        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $query = $barangKeluarModel->update(['bk_id' => $request->getVar('id')], $dataKeluar);
        if ($query) {
            $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
            $items = $barangKeluarItemModel->where('bki_barangkeluar', $request->getVar('id'))->findAll();
            foreach ($items as $row) {
                $dataItems = [
                    'created_at' => $tanggal,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $barangKeluarItemModel->update(['bki_id' => $row['bki_id']], $dataItems);
            }
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => true], true);
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => false], true);
        }
    }

    public function updateBarangKeluar()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateBarangKeluar", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');

        // add item barang keluar
        $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
        $dataRaw = json_decode($request->getPost('data'));
        foreach ($dataRaw as $row) {
            $checkItem = $barangKeluarItemModel->where(['bki_barangkeluar' => $request->getPost('id'), 'bki_barang' => $row->id])->first();
            if ($checkItem) {
                $qty = $checkItem['bki_qty'] ? str_replace(',', '.', $checkItem['bki_qty']) : 0;
                $itemKeluar = [
                    'bki_barangkeluar'  => $request->getPost('id'),
                    'bki_barang'        => $row->id,
                    'bki_qty'           => str_replace('.', ',', round($row->quantity + $qty, 2)),
                    'bki_harga'         => str_replace('.', ',', $row->price),
                    'created_at'        => $tanggal,
                    'updated_at'        => date('Y-m-d H:i:s')
                ];
                $barangKeluarItemModel->update(['bki_id' => $checkItem['bki_id']], $itemKeluar);
            } else {
                $itemKeluar = [
                    'bki_barangkeluar'  => $request->getPost('id'),
                    'bki_barang'        => $row->id,
                    'bki_qty'           => str_replace('.', ',', $row->quantity),
                    'bki_harga'         => str_replace('.', ',', $row->price),
                    'created_at'        => $tanggal,
                    'updated_at'        => $tanggal
                ];
                $barangKeluarItemModel->save($itemKeluar);
            }
        }

        // barang keluar
        $dataBarangKeluar = [
            'bk_tanggal'    => $tanggal,
            'bk_nomor'      => $request->getPost('faktur'),
            'bk_unit'       => $request->getPost('unit'),
            'bk_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            'bk_debet'      => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'bk_kredit'     => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'bk_user'       => $this->session->get('usr_id')
        ];

        $barangKeluarModel = new \App\Models\BarangKeluarModel();
        $queryKeluar = $barangKeluarModel->update(['bk_id' => $request->getPost('id')], $dataBarangKeluar);

        if ($queryKeluar) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => true, 'bkId' => $request->getPost('id'), 'msg' => 'Data berhasil disimpan.'], true);
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => false, 'msg' => 'Data gagal disimpan.'], true);
        }
    }

    public function deleteItemBarangKeluar()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteBarangKeluar", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
            $barangkeluar = $barangKeluarItemModel->find($id);
            if ($barangkeluar) {
                $query = $barangKeluarItemModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function deleteBarangKeluar()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteBarangKeluar", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $barangKeluarModel = new \App\Models\BarangKeluarModel();
            $barangkeluar = $barangKeluarModel->find($id);
            if ($barangkeluar) {
                $query = $barangKeluarModel->delete($id);
                if ($query) {
                    $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
                    $barangKeluarItemModel->where('bki_barangkeluar', $id)->delete();
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function laporanbarang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("laporanbarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();

        $barangModel = new \App\Models\BarangModel();
        if ($request->getVar('kategori') && $request->getVar('kategori') != '' && $request->getVar('kategori') != NULL) {
            $barangModel->where('barang.barang_kategori', $request->getVar('kategori'));
        }
        $barang = $barangModel->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
            ->join('kategori_barang', 'kategori_barang.kabar_id = barang.barang_kategori', 'left')
            ->join('user', 'user.usr_id = barang.barang_user', 'left')
            ->select('barang.*, satuan.satuan_id, satuan.satuan_nama, kategori_barang.kabar_id, kategori_barang.kabar_nama, user.usr_id, user.usr_nama')
            ->orderBy('barang.barang_nama', 'ASC')->findAll();
        $data = [
            'title_bar'     => 'Laporan Persediaan Material Gudang',
            'barang'        => $barang,
            'barangModel'   => $barangModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/laporanbarang', $data);
    }

    public function printXlsLaporanBarang($inStok = null)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("laporanbarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();

        $barangModel = new \App\Models\BarangModel();
        if ($request->getVar('kategori') && $request->getVar('kategori') != '' && $request->getVar('kategori') != NULL) {
            $barangModel->where('barang.barang_kategori', $request->getVar('kategori'));
        }
        $barang = $barangModel->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
            ->join('kategori_barang', 'kategori_barang.kabar_id = barang.barang_kategori', 'left')
            ->join('user', 'user.usr_id = barang.barang_user', 'left')
            ->select('barang.*, satuan.satuan_id, satuan.satuan_nama, kategori_barang.kabar_id, kategori_barang.kabar_nama, user.usr_id, user.usr_nama')
            ->orderBy('barang.barang_nama', 'ASC')->findAll();
        $data = [
            'title_bar'     => 'Laporan Persediaan Material Gudang',
            'barang'        => $barang,
            'inStok'        => $inStok,
            'barangModel'   => $barangModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/printXlsLaporanBarang', $data);
    }

    public function rekapmaterial()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("laporanbarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;
        if ($request->getVar('jenis') == 'masuk') {
            $pembelianItemModel = new \App\Models\PembelianItemModel();
            $startDate = $request->getVar('startDate');
            $endDate = $request->getVar('endDate');
            if ($startDate && $endDate) {
                $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
                $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
                $pembelianItemModel->where('pembelian_item.created_at >=', $start);
                $pembelianItemModel->where('pembelian_item.created_at <=', $end);
            }
            $barang = $pembelianItemModel->where('pembelian_item.pi_qtymasuk >', 0)
                ->join('barang', 'barang.barang_id = pembelian_item.pi_barang', 'left')
                ->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
                ->select('pembelian_item.*, barang.barang_nama, satuan.satuan_nama')
                ->orderBy('pembelian_item.created_at', 'DESC');
        }
        if ($request->getVar('jenis') == 'keluar') {
            $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
            $startDate = $request->getVar('startDate');
            $endDate = $request->getVar('endDate');
            if ($startDate && $endDate) {
                $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
                $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
                $barangKeluarItemModel->where('barangkeluar_item.created_at >=', $start);
                $barangKeluarItemModel->where('barangkeluar_item.created_at <=', $end);
            }
            $barangKeluarItemModel->join('barang', 'barang.barang_id = barangkeluar_item.bki_barang', 'left')
                ->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
                ->join('barangkeluar', 'barangkeluar.bk_id = barangkeluar_item.bki_barangkeluar', 'left')
                ->join('unit', 'unit.unit_id = barangkeluar.bk_unit', 'left');

            if ($request->getVar('unit')) {
                $barangKeluarItemModel->where('barangkeluar.bk_unit', $request->getVar('unit'));
            }

            $barang = $barangKeluarItemModel->select('barangkeluar_item.*, barang.barang_nama, satuan.satuan_nama, barangkeluar.bk_unit, unit.unit_nama')
                ->orderBy('barangkeluar_item.created_at', 'DESC');
        }

        $data = [
            'title_bar'     => 'Rekap Persediaan Material Gudang',
            'barang'        => $request->getVar('jenis') ? $barang->paginate(100, 'view') : [],
            'totalRows'     => $request->getVar('jenis') ? count($barang->findAll()) : 0,
            'currentPage'   => $currentPage,
            'validation'    => $this->validation
        ];
        return view('dashboard/rekapmaterial', $data);
    }

    public function printPdfRekapmaterial()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("laporanbarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        if ($request->getVar('jenis') == 'masuk') {
            $pembelianItemModel = new \App\Models\PembelianItemModel();
            $startDate = $request->getVar('startDate');
            $endDate = $request->getVar('endDate');
            if ($startDate && $endDate) {
                $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
                $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
                $pembelianItemModel->where('pembelian_item.created_at >=', $start);
                $pembelianItemModel->where('pembelian_item.created_at <=', $end);
            }
            $barang = $pembelianItemModel->where('pembelian_item.pi_qtymasuk >', 0)
                ->join('barang', 'barang.barang_id = pembelian_item.pi_barang', 'left')
                ->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
                ->select('pembelian_item.*, barang.barang_nama, satuan.satuan_nama')
                ->orderBy('pembelian_item.created_at', 'DESC');
        }
        if ($request->getVar('jenis') == 'keluar') {
            $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
            $startDate = $request->getVar('startDate');
            $endDate = $request->getVar('endDate');
            if ($startDate && $endDate) {
                $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
                $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
                $barangKeluarItemModel->where('barangkeluar_item.created_at >=', $start);
                $barangKeluarItemModel->where('barangkeluar_item.created_at <=', $end);
            }
            $barangKeluarItemModel->join('barang', 'barang.barang_id = barangkeluar_item.bki_barang', 'left')
                ->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
                ->join('barangkeluar', 'barangkeluar.bk_id = barangkeluar_item.bki_barangkeluar', 'left')
                ->join('unit', 'unit.unit_id = barangkeluar.bk_unit', 'left');

            if ($request->getVar('unit')) {
                $barangKeluarItemModel->where('barangkeluar.bk_unit', $request->getVar('unit'));
            }

            $barang = $barangKeluarItemModel->select('barangkeluar_item.*, barang.barang_nama, satuan.satuan_nama, barangkeluar.bk_unit, unit.unit_nama')
                ->orderBy('barangkeluar_item.created_at', 'DESC');
        }

        $data = [
            'title_bar'     => 'REKAP PERSEDIAAN BARANG ' . strtoupper($request->getVar('jenis')),
            'barang'        => $request->getVar('jenis') ? $barang->findAll() : []
        ];

        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new Dompdf();
        $dompdf->setOptions($options);
        $dompdf->set_base_path(realpath(ROOTPATH . '/public/'));
        $dompdf->setPaper('A4', 'potrait');
        $dompdf->loadHtml(view('dashboard/printPdfRekapmaterial', $data));
        $dompdf->render();
        return $dompdf->stream($data['title_bar'] . ".pdf", array("Attachment" => false));
    }

    public function printXlsRekapmaterial()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("laporanbarang", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        if ($request->getVar('jenis') == 'masuk') {
            $pembelianItemModel = new \App\Models\PembelianItemModel();
            $startDate = $request->getVar('startDate');
            $endDate = $request->getVar('endDate');
            if ($startDate && $endDate) {
                $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
                $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
                $pembelianItemModel->where('pembelian_item.created_at >=', $start);
                $pembelianItemModel->where('pembelian_item.created_at <=', $end);
            }
            $barang = $pembelianItemModel->where('pembelian_item.pi_qtymasuk >', 0)
                ->join('barang', 'barang.barang_id = pembelian_item.pi_barang', 'left')
                ->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
                ->select('pembelian_item.*, barang.barang_nama, satuan.satuan_nama')
                ->orderBy('pembelian_item.created_at', 'DESC');
        }
        if ($request->getVar('jenis') == 'keluar') {
            $barangKeluarItemModel = new \App\Models\BarangKeluarItemModel();
            $startDate = $request->getVar('startDate');
            $endDate = $request->getVar('endDate');
            if ($startDate && $endDate) {
                $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
                $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
                $barangKeluarItemModel->where('barangkeluar_item.created_at >=', $start);
                $barangKeluarItemModel->where('barangkeluar_item.created_at <=', $end);
            }
            $barangKeluarItemModel->join('barang', 'barang.barang_id = barangkeluar_item.bki_barang', 'left')
                ->join('satuan', 'satuan.satuan_id = barang.barang_satuan', 'left')
                ->join('barangkeluar', 'barangkeluar.bk_id = barangkeluar_item.bki_barangkeluar', 'left')
                ->join('unit', 'unit.unit_id = barangkeluar.bk_unit', 'left');

            if ($request->getVar('unit')) {
                $barangKeluarItemModel->where('barangkeluar.bk_unit', $request->getVar('unit'));
            }

            $barang = $barangKeluarItemModel->select('barangkeluar_item.*, barang.barang_nama, satuan.satuan_nama, barangkeluar.bk_unit, unit.unit_nama')
                ->orderBy('barangkeluar_item.created_at', 'DESC');
        }

        $data = [
            'title_bar'     => 'REKAP PERSEDIAAN BARANG ' . strtoupper($request->getVar('jenis')),
            'barang'        => $request->getVar('jenis') ? $barang->findAll() : []
        ];

        return view('dashboard/printXlsRekapmaterial', $data);
    }

    public function ubk()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("ubk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $trxupahModel = new \App\Models\TrxupahModel();
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $trxupahModel->where('trxupah.tu_tanggal >=', $start);
            $trxupahModel->where('trxupah.tu_tanggal <=', $end);
        }

        if ($request->getVar('tukang')) {
            $trxupahModel->where('trxupah.tu_tukang', $request->getVar('tukang'));
        }

        if ($request->getVar('faktur')) {
            $trxupahModel->where('trxupah.tu_nomor', $request->getVar('faktur'));
        }
        $trxupahModel->join('tukang', 'tukang.tk_id = trxupah.tu_tukang', 'left')
            ->join('user', 'user.usr_id = trxupah.tu_user', 'left')
            ->select('trxupah.*, tukang.tk_nama, user.usr_nama');
        $trxupah = $trxupahModel->orderBy('trxupah.tu_tanggal', 'DESC');

        $data = [
            'title_bar'         => 'Data Transaksi Upah',
            'trxupah'           => $trxupah->paginate(100, 'view'),
            'trxupahModel'      => $trxupahModel,
            'pager'             => $trxupahModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($trxupah->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/ubk', $data);
    }

    public function printupah()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("ubk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $trxupahModel = new \App\Models\TrxupahModel();
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');

        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $trxupahModel->where('trxupah.tu_tanggal >=', $start);
            $trxupahModel->where('trxupah.tu_tanggal <=', $end);

            if ($request->getVar('faktur')) {
                $trxupahModel->where('trxupah.tu_nomor', $request->getVar('faktur'));
            }

            if ($request->getVar('tukang')) {
                $trxupahModel->where('trxupah.tu_tukang', $request->getVar('tukang'));
            }

            $trxupahModel->join('tukang', 'tukang.tk_id = trxupah.tu_tukang', 'left')
                ->join('user', 'user.usr_id = trxupah.tu_user', 'left')
                ->select('trxupah.*, tukang.tk_nama, user.usr_nama');
            $trxupah = $trxupahModel->orderBy('trxupah.tu_tanggal', 'DESC');

            $data = [
                'title_bar'         => 'UPAH TUKANG ' . date('d-m-Y', strtotime($startDate)) . ' SAMPAI ' . date('d-m-Y', strtotime($endDate)),
                'trxupah'           => $trxupah->findAll()
            ];

            $options = new Options();
            $options->set('isRemoteEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $dompdf = new Dompdf();
            $dompdf->setOptions($options);
            $dompdf->set_base_path(realpath(ROOTPATH . '/public/'));
            $dompdf->setPaper('A4', 'potrait');
            $dompdf->loadHtml(view('dashboard/printupah', $data));
            $dompdf->render();
            return $dompdf->stream($data['title_bar'] . ".pdf", array("Attachment" => false));
        } else {
            return redirect()->to('dashboard/ubk');
        }
    }

    public function printdetailupah($idUpah)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("ubk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $trxupahModel = new \App\Models\TrxupahModel();
        $trxupahItemModel = new \App\Models\TrxupahItemModel();
        $trxupahLainModel = new \App\Models\TrxupahLainModel();

        $trxupah = $trxupahModel->find($idUpah);
        $trxupahItem = $trxupahItemModel->where('tui_trxupah', $idUpah)
            ->join('unit', 'unit.unit_id = trxupah_item.tui_unit', 'left')
            ->join('upah', 'upah.up_id = trxupah_item.tui_upah', 'left')
            ->select('trxupah_item.*, unit.unit_nama, upah.up_nama')
            ->findAll();
        $trxupahLain = $trxupahLainModel->where('tul_trxupah', $idUpah)->findAll();

        $data = [
            'title_bar'         => 'UPAH TUKANG ' . $trxupah['tu_nomor'],
            'trxupah'           => $trxupah,
            'trxupahItem'       => $trxupahItem,
            'trxupahLain'       => $trxupahLain
        ];

        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new Dompdf();
        $dompdf->setOptions($options);
        $dompdf->setPaper('A4', 'potrait');
        $dompdf->loadHtml(view('dashboard/printdetailupah', $data));
        $dompdf->render();
        return $dompdf->stream($data['title_bar'] . ".pdf", array("Attachment" => false));
    }

    public function printkasbon($idTrl)
    {
        $trxupahLainModel = new \App\Models\TrxupahLainModel();
        $trxupahModel = new \App\Models\TrxupahModel();
        $tukangModel = new \App\Models\TukangModel();

        $kasbon = $trxupahLainModel->find($idTrl);
        $trxupah = $trxupahModel->find($kasbon['tul_trxupah']);
        $tukang = $tukangModel->find($trxupah['tu_tukang']);

        $data = [
            'title_bar'     => 'KAS BON ' . strtoupper($tukang['tk_nama']) . ' TGL. ' . date('d-m-Y', strtotime($kasbon['tul_tanggal'])),
            'kasbon'        => $kasbon,
            'trxupah'       => $trxupah,
            'tukang'        => $tukang
        ];

        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new Dompdf();
        $dompdf->setOptions($options);
        $customPaper = array(0, 0, 164.409, 283.465); // 5,8 cm x 10 cm
        $dompdf->setPaper($customPaper, 'potrait');
        $dompdf->loadHtml(view('dashboard/printkasbon', $data));
        $dompdf->render();
        return $dompdf->stream($data['title_bar'] . ".pdf", array("Attachment" => false));
    }

    public function addUbk()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Buat Transaksi Upah',
            'validation'    => $this->validation
        ];
        return view('dashboard/addUbk', $data);
    }

    public function insertUbk()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $trxupahModel = new \App\Models\TrxupahModel();
        $trxupah = $trxupahModel->where('tu_nomor', $request->getPost('faktur'))->first();
        if ($trxupah) {
            $nomor = $trxupahModel->buatNoFaktur();
        } else {
            $nomor = $request->getPost('faktur');
        }

        $dataTrxupah = [
            'tu_tanggal'    => $tanggal,
            'tu_nomor'      => $nomor,
            'tu_tukang'     => $request->getPost('tukang'),
            'tu_totalupah'  => 0,
            'tu_lembur'     => 0,
            'tu_bon'        => 0,
            'tu_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            // 'tu_debet'      => $request->getPost('debet'),
            // 'tu_kredit'     => $request->getPost('kredit'),
            'tu_user'       => $this->session->get('usr_id')
        ];

        $queryUpah = $trxupahModel->insert($dataTrxupah);

        if ($queryUpah) {
            $idTrxUpah = $trxupahModel->insertID();

            // item pembelian
            $dataRaw = json_decode($request->getPost('data'));
            $totalUpah = 0;
            foreach ($dataRaw as $row) {
                $tanggalItem = date('Y-m-d', strtotime($row->tanggal)) . ' ' . date('H:i:s');
                $itemUpah[] = [
                    'tui_trxupah'   => $idTrxUpah,
                    'tui_tanggal'   => $tanggalItem,
                    'tui_unit'      => $row->unit,
                    'tui_upah'      => $row->idUpah,
                    'tui_jumlah'    => str_replace('.', ',', $row->quantity),
                    'tui_nilai'     => str_replace('.', ',', $row->price),
                    'tui_total'     => $row->quantity * $row->price,
                    'tui_debet'     => $row->debet,
                    'tui_kredit'    => $row->kredit,
                    'created_at'    => $tanggal,
                    'updated_at'    => $tanggal
                ];

                $totalUpah += ($row->quantity * $row->price);
            }

            $idupah = $idTrxUpah;
            if (isset($itemUpah)) {
                $trxupahItemModel = new \App\Models\TrxupahItemModel();
                $trxupahItemModel->insertBatch($itemUpah);

                $trxupahModel->update(['tu_id' => $idupah], ['tu_totalupah' => $totalUpah]);
            }

            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => true, 'msg' => 'Data berhasil disimpan.'], true);
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => false, 'msg' => 'Data gagal disimpan.'], true);
        }
    }

    public function editUbk($idUpah)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $trxupahModel = new \App\Models\TrxupahModel();
        $trxupahItemModel = new \App\Models\TrxupahItemModel();
        $trxupahLainModel = new \App\Models\TrxupahLainModel();

        $trxupah = $trxupahModel->find($idUpah);
        $trxupahItem = $trxupahItemModel->where('tui_trxupah', $idUpah)
            ->join('unit', 'unit.unit_id = trxupah_item.tui_unit', 'left')
            ->join('upah', 'upah.up_id = trxupah_item.tui_upah', 'left')
            ->select('trxupah_item.*, unit.unit_nama, upah.up_nama')
            ->orderBy('trxupah_item.tui_tanggal', 'DESC')
            ->findAll();
        $trxupahLain = $trxupahLainModel->where('tul_trxupah', $idUpah)->orderBy('tul_tanggal', 'DESC')->findAll();

        $data = [
            'title_bar'     => 'Perbarui Transaksi Upah',
            'trxupah'       => $trxupah,
            'trxupahItem'   => $trxupahItem,
            'trxupahLain'   => $trxupahLain,
            'validation'    => $this->validation
        ];
        return view('dashboard/editUbk', $data);
    }

    public function inserTrxupahLain()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("ubk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $dataRaw = json_decode($request->getPost('data'));
        foreach ($dataRaw as $row) {
            $tanggalItem = date('Y-m-d', strtotime($row->tanggal)) . ' ' . date('H:i:s');
            $itemUpah[] = [
                'tul_trxupah'   => $request->getPost('idUpah'),
                'tul_tanggal'   => $tanggalItem,
                'tul_jenis'     => $row->jenistrx,
                'tul_kaskecil'  => $row->kaskecil,
                'tul_nominal'   => str_replace('.', ',', $row->price),
                'tul_keterangan'  => $row->keteranganLain,
                'tul_debet'     => $row->debetLain,
                'tul_kredit'    => $row->kreditLain,
                'tul_user'      => $this->session->get('usr_id'),
                'created_at'    => $tanggalItem,
                'updated_at'    => $tanggalItem
            ];
        }

        if (isset($itemUpah)) {
            $trxupahLainModel = new \App\Models\TrxupahLainModel();
            $trxupahLainModel->insertBatch($itemUpah);

            $itemLainUpah = $trxupahLainModel->where('tul_trxupah', $request->getPost('idUpah'))->findAll();
            $totalKasbon = 0;
            $totalLembur = 0;
            foreach ($itemLainUpah as $row) {
                if ($row['tul_jenis'] == 1) {
                    $totalKasbon += str_replace(',', '.', $row['tul_nominal']);
                }
                if ($row['tul_jenis'] == 2) {
                    $totalLembur += str_replace(',', '.', $row['tul_nominal']);
                }
            }

            $trxupahModel = new \App\Models\TrxupahModel();
            $trxupahModel->update(['tu_id' => $request->getPost('idUpah')], ['tu_lembur' => $totalLembur, 'tu_bon' => $totalKasbon]);

            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => true, 'msg' => 'Data berhasil disimpan.'], true);
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => false, 'msg' => 'Data gagal disimpan.'], true);
        }
    }

    public function updateUbk()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $trxupahModel = new \App\Models\TrxupahModel();

        $dataTrxupah = [
            'tu_tanggal'    => $tanggal,
            'tu_tukang'     => $request->getPost('tukang'),
            'tu_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            // 'tu_debet'      => $request->getPost('debet'),
            // 'tu_kredit'     => $request->getPost('kredit'),
            'tu_user'       => $this->session->get('usr_id')
        ];
        $queryUpah = $trxupahModel->update(['tu_id' => $request->getPost('id')], $dataTrxupah);

        if ($queryUpah) {
            // item ubk
            $trxupahItemModel = new \App\Models\TrxupahItemModel();
            $dataRaw = json_decode($request->getPost('data'));
            foreach ($dataRaw as $row) {
                $tanggalItem = date('Y-m-d', strtotime($row->tanggal)) . ' ' . date('H:i:s');

                $checkItem = $trxupahItemModel->where(['tui_trxupah' => $request->getPost('id'), 'tui_unit' => $row->unit, 'tui_upah' => $row->idUpah])->first();
                if (date('Y-m-d', strtotime($checkItem['tui_tanggal'])) == date('Y-m-d', strtotime($tanggalItem))) {
                    $oldqty = str_replace(',', '.', $checkItem['tui_jumlah']);
                    $itemUpah = [
                        'tui_trxupah'   => $request->getPost('id'),
                        'tui_tanggal'   => $tanggalItem,
                        'tui_unit'      => $row->unit,
                        'tui_upah'      => $row->idUpah,
                        'tui_jumlah'    => str_replace('.', ',', $oldqty + $row->quantity),
                        'tui_nilai'     => str_replace('.', ',', $row->price),
                        'tui_total'     => ($oldqty + $row->quantity) * $row->price,
                        'tui_debet'     => $row->debet,
                        'tui_kredit'    => $row->kredit,
                        'updated_at'    => $tanggal
                    ];
                    $trxupahItemModel->update(['tui_id' => $checkItem['tui_id']], $itemUpah);
                } else {
                    $itemUpah = [
                        'tui_trxupah'   => $request->getPost('id'),
                        'tui_tanggal'   => $tanggalItem,
                        'tui_unit'      => $row->unit,
                        'tui_upah'      => $row->idUpah,
                        'tui_jumlah'    => str_replace('.', ',', $row->quantity),
                        'tui_nilai'     => str_replace('.', ',', $row->price),
                        'tui_total'     => $row->quantity * $row->price,
                        'tui_debet'     => $row->debet,
                        'tui_kredit'    => $row->kredit,
                        'created_at'    => $tanggal,
                        'updated_at'    => $tanggal
                    ];
                    $trxupahItemModel->insert($itemUpah);
                }
            }

            $upahItem = $trxupahItemModel->where(['tui_trxupah' => $request->getPost('id')])->findAll();
            $totalUpah = 0;
            foreach ($upahItem as $row) {
                $totalUpah += $row['tui_total'];
            }
            $trxupahModel->update(['tu_id' => $request->getPost('id')], ['tu_totalupah' => $totalUpah]);

            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => true, 'msg' => 'Data berhasil disimpan.'], true);
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return json_encode(['status' => false, 'msg' => 'Data gagal disimpan.'], true);
        }
    }

    public function deleteItemUbk()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $trxupahItemModel = new \App\Models\TrxupahItemModel();
            $data = $trxupahItemModel->find($id);
            if ($data) {
                $query = $trxupahItemModel->delete($id);
                if ($query) {
                    $upahItem = $trxupahItemModel->where(['tui_trxupah' => $data['tui_trxupah']])->findAll();
                    $totalUpah = 0;
                    foreach ($upahItem as $row) {
                        $totalUpah += $row['tui_total'];
                    }
                    $trxupahModel = new \App\Models\TrxupahModel();
                    $trxupahModel->update(['tu_id' => $data['tui_trxupah']], ['tu_totalupah' => $totalUpah]);

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function deleteItemUpahLain()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $trxupahLainModel = new \App\Models\TrxupahLainModel();
            $data = $trxupahLainModel->find($id);
            if ($data) {
                $query = $trxupahLainModel->delete($id);
                if ($query) {
                    $itemLainUpah = $trxupahLainModel->where('tul_trxupah', $data['tul_trxupah'])->findAll();
                    $totalKasbon = 0;
                    $totalLembur = 0;
                    foreach ($itemLainUpah as $row) {
                        if ($row['tul_jenis'] == 1) {
                            $totalKasbon += str_replace(',', '.', $row['tul_nominal']);
                        }
                        if ($row['tul_jenis'] == 2) {
                            $totalLembur += str_replace(',', '.', $row['tul_nominal']);
                        }
                    }

                    $trxupahModel = new \App\Models\TrxupahModel();
                    $trxupahModel->update(['tu_id' => $data['tul_trxupah']], ['tu_lembur' => $totalLembur, 'tu_bon' => $totalKasbon]);

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function deleteUbk()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $trxupahModel = new \App\Models\TrxupahModel();
            $data = $trxupahModel->find($id);
            if ($data) {
                $query = $trxupahModel->delete($id);
                if ($query) {
                    $trxupahItemModel = new \App\Models\TrxupahItemModel();
                    $trxupahItemModel->where('tui_trxupah', $id)->delete();
                    $trxupahLainModel = new \App\Models\TrxupahLainModel();
                    $trxupahLainModel->where('tul_trxupah', $id)->delete();
                    $trxupahBayarModel = new \App\Models\TrxupahBayarModel();
                    $trxupahBayarModel->where('tub_trxupah', $id)->delete();

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function bayarubk($idUpah)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("ubk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $trxupahModel = new \App\Models\TrxupahModel();
        $trxupahItemModel = new \App\Models\TrxupahItemModel();
        $trxupahLainModel = new \App\Models\TrxupahLainModel();

        $trxupah = $trxupahModel->where(['tu_id' => $idUpah])
            ->join('tukang', 'tukang.tk_id = trxupah.tu_tukang', 'left')
            ->select('trxupah.*, tukang.tk_nama')->first();

        $trxupahItem = $trxupahItemModel->where('tui_trxupah', $idUpah)
            ->join('unit', 'unit.unit_id = trxupah_item.tui_unit', 'left')
            ->join('upah', 'upah.up_id = trxupah_item.tui_upah', 'left')
            ->select('trxupah_item.*, unit.unit_nama, upah.up_nama')
            ->findAll();

        $trxupahLain = $trxupahLainModel->where('tul_trxupah', $idUpah)->findAll();

        $data = [
            'title_bar'     => 'Bayar Upah',
            'trxupah'       => $trxupah,
            'trxupahItem'   => $trxupahItem,
            'trxupahLain'   => $trxupahLain,
            'validation'    => $this->validation
        ];
        return view('dashboard/bayarubk', $data);
    }

    public function prosesbayarupah()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'id' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'bayar' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/bayarubk/' . $request->getPost('id'))->withInput();
        }

        $trxupahBayarModel = new \App\Models\TrxupahBayarModel();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $query = $trxupahBayarModel->insert([
            'tub_tanggal'       => $tanggal,
            'tub_nomor'         => $trxupahBayarModel->buatNoFaktur(),
            'tub_trxupah'       => $request->getPost('id'),

            'tub_tupah'         => $request->getPost('tupah') ? str_replace('.', '', $request->getPost('tupah')) : 0,
            'tub_tupahdebet'    => $request->getPost('debetTupah') ? $request->getPost('debetTupah') : NULL,
            'tub_tbon'          => $request->getPost('tbon') ? str_replace('.', '', $request->getPost('tbon')) : 0,
            'tub_tbonkredit'    => $request->getPost('kreditBon') ? $request->getPost('kreditBon') : NULL,
            'tub_tsupah'        => $request->getPost('supah') ? str_replace('.', '', $request->getPost('supah')) : 0,
            'tub_tsupahkredit'  => $request->getPost('kreditSupah') ? $request->getPost('kreditSupah') : NULL,

            'tub_bayar'         => $request->getPost('bayar') ? str_replace('.', '', $request->getPost('bayar')) : 0,
            'tub_keterangan'    => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            'tub_debet'         => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'tub_kredit'        => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'tub_user'          => $this->session->get('usr_id')
        ]);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/bayarubk/' . $request->getPost('id'));
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/bayarubk/' . $request->getPost('id'))->withInput();
        }
    }

    public function bayarUpahJson($id)
    {
        $trxupahBayarModel = new \App\Models\TrxupahBayarModel();
        $trxbayar = $trxupahBayarModel->find($id);
        return json_encode([
            'tub_id' => $trxbayar['tub_id'],
            'tub_trxupah' => $trxbayar['tub_trxupah'],
            'tub_tanggal' => date('d-m-Y', strtotime($trxbayar['tub_tanggal'])),
            'tub_nomor' => $trxbayar['tub_nomor'],
            'tub_bayar' => $trxbayar['tub_bayar'],
            'tub_debet' => $trxbayar['tub_debet'],
            'tub_kredit' => $trxbayar['tub_kredit'],
            'tub_keterangan' => $trxbayar['tub_keterangan'],
            'tub_user' => $trxbayar['tub_user'],
        ], true);
    }

    public function updatePembayaranItemUpah()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'idUpah' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'bayarEdit' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'tanggalTrxEdit' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/bayarubk/' . $request->getPost('idUpah'))->withInput();
        }

        $trxupahBayarModel = new \App\Models\TrxupahBayarModel();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggalTrxEdit'))) . ' ' . date('H:i:s');
        $query = $trxupahBayarModel->update(['tub_id' => $request->getPost('idTrx')], [
            'tub_tanggal'       => $tanggal,
            'tub_trxupah'       => $request->getPost('idUpah'),
            'tub_bayar'         => $request->getPost('bayarEdit') ? str_replace('.', '', $request->getPost('bayarEdit')) : 0,
            'tub_keterangan'    => $request->getPost('keteranganEdit') ? $request->getPost('keteranganEdit') : NULL,
            'tub_debet'         => $request->getPost('debetEdit') ? $request->getPost('debetEdit') : NULL,
            'tub_kredit'        => $request->getPost('kreditEdit') ? $request->getPost('kreditEdit') : NULL,
            'tub_user'          => $this->session->get('usr_id')
        ]);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil diperbarui.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/bayarubk/' . $request->getPost('idUpah'));
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal diperbarui.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/bayarubk/' . $request->getPost('idUpah'))->withInput();
        }
    }

    public function deleteitembayarupah()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $trxupahBayarModel = new \App\Models\TrxupahBayarModel();
            $data = $trxupahBayarModel->find($id);
            if ($data) {
                $query = $trxupahBayarModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function savefakturubk()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateUbk", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $trxupahModel = new \App\Models\TrxupahModel();

        $dataTrxupah = [
            'tu_tanggal'    => $tanggal,
            'tu_tukang'     => $request->getPost('tukang'),
            'tu_keterangan' => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            // 'tu_debet'      => $request->getPost('debet'),
            // 'tu_kredit'     => $request->getPost('kredit'),
            'tu_user'       => $this->session->get('usr_id')
        ];
        $queryUpah = $trxupahModel->update(['tu_id' => $request->getPost('id')], $dataTrxupah);

        if ($queryUpah) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return '<script>window.history.go(-1);</script>';
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return '<script>window.history.go(-1);</script>';
        }
    }

    public function hutangsuplier()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("hutangsuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $hsModel = new \App\Models\HutangsuplierModel();
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $hsModel->where('hutangsuplier.hs_tanggal >=', $start);
            $hsModel->where('hutangsuplier.hs_tanggal <=', $end);
        }
        if ($request->getVar('supplier')) {
            $hsModel->where('hutangsuplier.hs_suplier', $request->getVar('supplier'));
        }
        $hsModel->join('pembelian', 'pembelian.pb_id = hutangsuplier.hs_pembelian', 'left');
        if ($request->getVar('faktur')) {
            $hsModel->where('pembelian.pb_nomor', $request->getVar('faktur'))
                ->orWhere('hutangsuplier.hs_nomor', $request->getVar('faktur'));
        }
        $hsModel->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left');
        $hutangsuplier = $hsModel->select('hutangsuplier.*, suplier.suplier_nama, pembelian.pb_nomor')
            ->orderBy('hutangsuplier.hs_tanggal', 'DESC');

        $data = [
            'title_bar'         => 'Data Hutang Suplier',
            'hutangsuplier'     => $hutangsuplier->paginate(100, 'view'),
            'hsModel'           => $hsModel,
            'pager'             => $hsModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($hutangsuplier->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/hutangsuplier', $data);
    }

    public function printPdfHutangsuplier()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("hutangsuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();

        $hsModel = new \App\Models\HutangsuplierModel();
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $hsModel->where('hutangsuplier.hs_tanggal >=', $start);
            $hsModel->where('hutangsuplier.hs_tanggal <=', $end);
        }
        if ($request->getVar('supplier')) {
            $hsModel->where('hutangsuplier.hs_suplier', $request->getVar('supplier'));
        }
        if ($request->getVar('faktur')) {
            $hsModel->where('hutangsuplier.hs_nomor', $request->getVar('faktur'));
        }
        $hsModel->join('pembelian', 'pembelian.pb_id = hutangsuplier.hs_pembelian', 'left');
        $hsModel->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left');
        $hutangsuplier = $hsModel->select('hutangsuplier.*, suplier.suplier_nama, pembelian.pb_nomor')
            ->orderBy('hutangsuplier.hs_tanggal', 'DESC')->findAll();

        $data = [
            'title_bar'         => 'HUTANG SUPLIER',
            'hutangsuplier'     => $hutangsuplier
        ];

        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new Dompdf();
        $dompdf->setOptions($options);
        $dompdf->set_base_path(realpath(ROOTPATH . '/public/'));
        $dompdf->setPaper('A4', 'potrait');
        $dompdf->loadHtml(view('dashboard/printPdfHutangsuplier', $data));
        $dompdf->render();
        return $dompdf->stream($data['title_bar'] . ".pdf", array("Attachment" => false));
    }

    public function printXlsHutangsuplier()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("hutangsuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();

        $hsModel = new \App\Models\HutangsuplierModel();
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $hsModel->where('hutangsuplier.hs_tanggal >=', $start);
            $hsModel->where('hutangsuplier.hs_tanggal <=', $end);
        }
        if ($request->getVar('supplier')) {
            $hsModel->where('hutangsuplier.hs_suplier', $request->getVar('supplier'));
        }
        if ($request->getVar('faktur')) {
            $hsModel->where('hutangsuplier.hs_nomor', $request->getVar('faktur'));
        }
        $hsModel->join('pembelian', 'pembelian.pb_id = hutangsuplier.hs_pembelian', 'left');
        $hsModel->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left');
        $hutangsuplier = $hsModel->select('hutangsuplier.*, suplier.suplier_nama, pembelian.pb_nomor')
            ->orderBy('hutangsuplier.hs_tanggal', 'DESC')->findAll();

        $data = [
            'title_bar'     => 'HUTANG SUPLIER',
            'hutangsuplier' => $hutangsuplier,
            'hsModel'       => $hsModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/printXlsHutangsuplier', $data);
    }

    public function jatuhtempo()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("hutangsuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $hsModel = new \App\Models\HutangsuplierModel();
        $hsModel->where('hutangsuplier.hs_tempo !=', NULL);
        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $hsModel->where('hutangsuplier.hs_tempo >=', $start);
            $hsModel->where('hutangsuplier.hs_tempo <=', $end);
        }
        if ($request->getVar('supplier')) {
            $hsModel->where('hutangsuplier.hs_suplier', $request->getVar('supplier'));
        }
        if ($request->getVar('faktur')) {
            $hsModel->where('hutangsuplier.hs_nomor', $request->getVar('faktur'));
        }
        $hsModel->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left');
        $hutangsuplier = $hsModel->select('hutangsuplier.*, suplier.suplier_nama')
            ->orderBy('hutangsuplier.hs_tempo', 'ASC');

        $data = [
            'title_bar'         => 'Data Hutang Suplier (Jatuh Tempo Terdekat)',
            'hutangsuplier'     => $hutangsuplier->paginate(100, 'view'),
            'hsModel'           => $hsModel,
            'pager'             => $hsModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($hutangsuplier->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/jatuhtempo', $data);
    }

    public function bayarhutangsuplier($idHs)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("hutangsuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $hsModel = new \App\Models\HutangsuplierModel();
        $hutangsupplier = $hsModel->where('hs_id', $idHs)
            ->join('suplier', 'suplier.suplier_id = hutangsuplier.hs_suplier', 'left')
            ->select('hutangsuplier.*, suplier.suplier_nama, suplier.suplier_alamat, suplier.suplier_telp')
            ->first();

        $data = [
            'title_bar'         => 'Bayar Hutang Suplier',
            'hutangsuplier'     => $hutangsupplier,
            'hsModel'           => $hsModel,
            'validation'        => $this->validation
        ];
        return view('dashboard/bayarhutangsuplier', $data);
    }

    public function deleteHutangsuplier()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("hutangsuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $hsModel = new \App\Models\HutangsuplierModel();
            $hs = $hsModel->find($id);
            if ($hs) {
                $query = $hsModel->delete($id);
                if ($query) {
                    $hbModel = new \App\Models\HutangsuplierBayarModel();
                    $hbModel->where('hb_hutangsuplier', $id)->delete();

                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function prosesbayarhs()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("hutangsuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'id' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'bayar' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->to('/dashboard/bayarhutangsuplier/' . $request->getPost('id'))->withInput();
        }

        $hbModel = new \App\Models\HutangsuplierBayarModel();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggal'))) . ' ' . date('H:i:s');
        $query = $hbModel->insert([
            'hb_tanggal'        => $tanggal,
            'hb_nomor'          => $hbModel->buatNoFaktur(),
            'hb_hutangsuplier'  => $request->getPost('id'),
            'hb_bayar'          => $request->getPost('bayar') ? str_replace('.', '', $request->getPost('bayar')) : 0,
            'hb_keterangan'     => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            'hb_debet'          => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'hb_kredit'         => $request->getPost('kredit') ? $request->getPost('kredit') : NULL,
            'hb_user'           => $this->session->get('usr_id')
        ]);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/bayarhutangsuplier/' . $request->getPost('id'));
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/bayarhutangsuplier/' . $request->getPost('id'))->withInput();
        }
    }

    public function updatePembayaranItemHs()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("hutangsuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();

        $hbModel = new \App\Models\HutangsuplierBayarModel();
        $tanggal = date('Y-m-d', strtotime($request->getPost('tanggalTrxEdit'))) . ' ' . date('H:i:s');
        $query = $hbModel->update(['hb_id' => $request->getPost('idHb')], [
            'hb_tanggal'        => $tanggal,
            'hb_hutangsuplier'  => $request->getPost('idHs'),
            'hb_bayar'          => $request->getPost('bayarEdit') ? str_replace('.', '', $request->getPost('bayarEdit')) : 0,
            'hb_keterangan'     => $request->getPost('keteranganEdit') ? $request->getPost('keteranganEdit') : NULL,
            'hb_debet'          => $request->getPost('debetEdit') ? $request->getPost('debetEdit') : NULL,
            'hb_kredit'         => $request->getPost('kreditEdit') ? $request->getPost('kreditEdit') : NULL,
            'hb_user'           => $this->session->get('usr_id')
        ]);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/bayarhutangsuplier/' . $request->getPost('idHs'));
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/bayarhutangsuplier/' . $request->getPost('idHs'))->withInput();
        }
    }

    public function bayarHsJson($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("hutangsuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $hbModel = new \App\Models\HutangsuplierBayarModel();
        $hb = $hbModel->find($id);
        return json_encode([
            'hb_id'             => $hb['hb_id'],
            'hb_hutangsuplier'  => $hb['hb_hutangsuplier'],
            'hb_tanggal'    => date('d-m-Y', strtotime($hb['hb_tanggal'])),
            'hb_bayar'      => $hb['hb_bayar'],
            'hb_keterangan' => $hb['hb_keterangan'],
            'hb_debet'      => $hb['hb_debet'],
            'hb_kredit'     => $hb['hb_kredit']
        ], true);
    }

    public function deleteitembayarhs()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("hutangsuplier", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $hbModel = new \App\Models\HutangsuplierBayarModel();
            $hb = $hbModel->find($id);
            if ($hb) {
                $query = $hbModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function laporanprogresunit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("laporanprogresunit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $unitModel = new \App\Models\UnitModel();
        if ($request->getVar('unit')) {
            return redirect()->to('/dashboard/laporandetailprogres/'.$request->getVar('unit'));
            // $unitModel->where('unit_id', $request->getVar('unit'));
            // $units = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
            // ->select('unit.*, types.type_nama')
            // ->orderBy('types.type_nama', 'ASC')->findAll();
        } else {
            $units = [];
        }
        $data = [
            'title_bar'     => 'Laporan Progres Unit',
            'units'         => $units,
            'unitModel'     => $unitModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/laporanprogresunit', $data);
    }

    public function laporandetailprogres($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("laporanprogresunit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $unitModel = new \App\Models\UnitModel();
        $unit = $unitModel->where('unit.unit_id', $id)
            ->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->select('unit.*, types.type_nama')->first();

        $data = [
            'title_bar'     => 'Laporan Detail Progres Unit ' . $unit['unit_nama'] . ' (' . $unit['type_nama'] . ')',
            'unit'          => $unit,
            'unitModel'     => $unitModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/laporandetailprogres', $data);
    }

    public function rewards()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("pembelian", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $pembelianModel = new \App\Models\PembelianModel();
        $rewards = $pembelianModel->rewards(100);

        $data = [
            'title_bar'         => 'Reward Pengajuang Material',
            'rewards'           => $rewards,
            'pembelianModel'    => $pembelianModel,
            'pager'             => $pembelianModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($pembelianModel->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/rewards', $data);
    }

    public function ledger()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("ledger", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;
        $ledgerModel = new \App\Models\LedgerModel();

        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        $search = $request->getVar('search');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $ledgerModel->where('ledger.created_at >=', $start);
            $ledgerModel->where('ledger.created_at <=', $end);
        }

        if ($search) {
            $ledgerModel->groupStart();
            $ledgerModel->like('ledger.gl_nomor', $search)->orLike('ledger.gl_uraian', $search);
            $ledgerModel->groupEnd();
        }

        $ledgers = $ledgerModel->join('user', 'user.usr_id = ledger.gl_user', 'left')
            ->select('ledger.*, user.usr_nama')
            ->orderBy('ledger.gl_id', 'DESC')
            ->groupBy('gl_nomor');

        $data = [
            'title_bar'     => 'General Ledger',
            'ledgers'       => $ledgers->paginate(100, 'view'),
            'ledgerModel'   => $ledgerModel,
            'pager'         => $ledgerModel->pager,
            'current'       => $currentPage,
            'totalRows'        => count($ledgers->findAll()),
            'validation'    => $this->validation,
        ];
        return view('dashboard/ledger', $data);
    }

    public function addLedger()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addLedger", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Tambah Data General Ledger',
            'validation'    => $this->validation
        ];
        return view('dashboard/addLedger', $data);
    }

    public function insertLedger()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertLedger", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $ledgerModel = new \App\Models\LedgerModel();
        $dataRaw = json_decode($request->getPost('data'));

        foreach ($dataRaw as $row) {
            $tanggal = date('Y-m-d H:i:s', strtotime($row->tanggal . ' ' . date('H:i:s')));
            $dataOrder[] = [
                'gl_nomor'             => $row->name,
                'gl_uraian'         => $row->uraian,
                'gl_debet'             => $row->debetId,
                'gl_kredit'         => $row->kreditId,
                'gl_nominalDebet'     => $row->nominalDebet,
                'gl_nominalKredit'     => $row->nominalKredit,
                'gl_user'             => $this->session->get('usr_id'),
                'gl_trx'             => $row->ket_trx,
                'created_at'        => $tanggal,
                'updated_at'        => $tanggal
            ];
        }

        if (isset($dataOrder)) {
            $query = $ledgerModel->insertBatch($dataOrder);
            if ($query) {
                return json_encode(['status' => true], true);
            } else {
                return json_encode(['status' => false], true);
            }
        } else {
            return json_encode(['status' => false], true);
        }
    }

    public function updateLedger()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertLedger", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $ledgerModel = new \App\Models\LedgerModel();

        $ledger = $ledgerModel->where(['gl_nomor' => $request->getPost('nomorLedger')])->findAll();
        $tanggalLedger = date('Y-m-d H:i:s', strtotime($request->getPost('tanggalLedger') . ' ' . date('H:i:s')));
        foreach ($ledger as $row) {
            $dataLedgerUpdate[] = [
                'gl_id'                => $row['gl_id'],
                'gl_nomor'             => $request->getPost('nomorLedger'),
                'gl_uraian'         => $request->getPost('uraian'),
                'gl_user'             => $this->session->get('usr_id'),
                'gl_trx'             => $request->getPost('ket_trx'),
                'updated_at'        => $tanggalLedger
            ];
        }

        $dataRaw = json_decode($request->getPost('data'));
        foreach ($dataRaw as $row) {
            $tanggal = date('Y-m-d H:i:s', strtotime($row->tanggal . ' ' . date('H:i:s')));
            $dataLedgerInsert[] = [
                'gl_nomor'             => $row->name,
                'gl_uraian'         => $row->uraian,
                'gl_debet'             => $row->debetId,
                'gl_kredit'         => $row->kreditId,
                'gl_nominalDebet'     => $row->nominalDebet,
                'gl_nominalKredit'     => $row->nominalKredit,
                'gl_user'             => $this->session->get('usr_id'),
                'gl_trx'             => $row->ket_trx,
                'created_at'        => $tanggal,
                'updated_at'        => $tanggal
            ];
        }

        if (isset($dataLedgerInsert)) {
            $query = $ledgerModel->insertBatch($dataLedgerInsert);
        }

        if (isset($dataLedgerUpdate)) {
            $query = $ledgerModel->updateBatch($dataLedgerUpdate, 'gl_id');
        }

        if ($query) {
            return json_encode(['status' => true], true);
        } else {
            return json_encode(['status' => false], true);
        }
    }

    public function editLedger()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editLedger", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $ledgerModel = new \App\Models\LedgerModel();
        $ledger = $ledgerModel->where('gl_nomor', urldecode($request->getVar('nomor')))->findAll();

        $data = [
            'title_bar'     => 'Perbarui Ledger',
            'ledger'        => $ledger,
            'validation'    => $this->validation
        ];
        return view('dashboard/editLedger', $data);
    }

    public function deleteLedger()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteLedger", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $ledgerModel = new \App\Models\LedgerModel();
            $ledgers = $ledgerModel->where('gl_nomor', $id)->findAll();
            if ($ledgers) {
                $query = $ledgerModel->where('gl_nomor', $id)->delete();
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/ledger');
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/ledger');
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/ledger');
            }
        }
    }

    public function deleteItemLedger()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteLedger", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $ledgerModel = new \App\Models\LedgerModel();
            $ledgers = $ledgerModel->find($id);
            if ($ledgers) {
                $query = $ledgerModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/editLedger?nomor=' . $request->getVar('nomor'));
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/editLedger?nomor=' . $request->getVar('nomor'));
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/editLedger?nomor=' . $request->getVar('nomor'));
            }
        }
    }

    private function makePagination(array $input, $pageNum, $perPage)
    {
        $start = ($pageNum - 1) * $perPage;
        $end = $start + $perPage;
        $count = count($input);
        if ($start < 0 || $count <= $start) {
            return array();
        } else if ($count <= $end) {
            return array_slice($input, $start);
        } else {
            return array_slice($input, $start, $end - $start);
        }
    }

    public function bukubesar()
    {
        $bukubesarModel = new \App\Models\BukuBesarModel();
        $rekeningModel = new \App\Models\KoderekeningModel();

        $request = \Config\Services::request();
        $currentPage = $request->getVar('pageNum') ? $request->getVar('pageNum') : 1;
        $idRek = $request->getVar('rekening');
        if ($idRek) {
            $rekening = $rekeningModel->find($idRek);
            $bukubesar = $bukubesarModel->getBukuBesar($idRek);
            $dataBukuBesar = $this->makePagination(($bukubesar ? $bukubesar : []), $currentPage, 50);
        } else {
            $rekening = [];
            $bukubesar = [];
            $dataBukuBesar = [];
        }
        $data = [
            'title_bar'     => 'Buku Besar',
            'bukubesar'     => $dataBukuBesar,
            'idRek'         => $idRek,
            'rekening'      => $rekening,
            'totalRows'     => count($bukubesar ? $bukubesar : []),
            'validation'    => $this->validation
        ];
        return view('dashboard/bukubesar', $data);
    }

    public function neracaSaldo()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("neracaSaldo", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;
        $rekeningModel = new \App\Models\KoderekeningModel();

        $rekening = $rekeningModel->orderBy('rek_kode', 'ASC');
        $data = [
            'title_bar'         => 'Neraca Saldo',
            'rekening'          => $rekening->findAll(),
            'rekeningModel'     => $rekeningModel,
            'pager'             => $rekeningModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($rekening->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/neracaSaldo', $data);
    }

    public function penjualanunit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("penjualanunit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $penjualanModel = new \App\Models\PenjualanunitModel();

        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $penjualanModel->where('penjualan_unit.created_at >=', $start);
            $penjualanModel->where('penjualan_unit.created_at <=', $end);
        }


        if ($request->getVar('nomor')) {
            $penjualanModel->groupStart();
            $penjualanModel->like('penjualan_unit.pu_nomor', $request->getVar('nomor'));
            $penjualanModel->groupEnd();
        }

        if ($request->getVar('jenis')) {
            $penjualanModel->where('penjualan_unit.pu_jenis', $request->getVar('jenis'));
        }

        if ($request->getVar('customer')) {
            $penjualanModel->where('penjualan_unit.pu_cust', $request->getVar('customer'));
        }

        if ($request->getVar('kpr')) {
            $penjualanModel->where('penjualan_unit.pu_kpr', $request->getVar('kpr'));
        }

        if ($request->getVar('unit')) {
            $penjualanModel->where('penjualan_unit.pu_unit', $request->getVar('unit'));
        }

        $penjualans = $penjualanModel
            ->join('unit', 'unit.unit_id = penjualan_unit.pu_unit', 'left')
            ->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
            ->join('kpr', 'kpr.kpr_id = penjualan_unit.pu_kpr', 'left')
            ->join('user', 'user.usr_id = penjualan_unit.pu_user', 'left')
            ->select('penjualan_unit.*, unit.unit_nama, customers.cust_nama, customers.cust_alamat, user.usr_nama, types.type_nama, kpr.kpr_nama')
            ->orderBy('penjualan_unit.pu_id', 'DESC');

        $data = [
            'title_bar'         => 'Penjualan Unit',
            'penjualans'         => $penjualans->paginate(100, 'view'),
            'penjualanModel'    => $penjualanModel,
            'pager'             => $penjualanModel->pager,
            'current'           => $currentPage,
            'totalRows'           => count($penjualans->findAll()),
            'validation'        => $this->validation
        ];

        return view('dashboard/penjualanunit', $data);
    }

    public function addPenjualan()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addPenjualan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $unitModel = new \App\Models\UnitModel();
        $customerModel = new \App\Models\CustomerModel();

        $units = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->select('unit.*, types.type_nama')
            ->orderBy('unit.unit_nama', 'ASC')
            ->findAll();
        $customers = $customerModel->orderBy('cust_nama', 'ASC')->findAll();

        $data = [
            'title_bar'         => 'Buat Penjualan Unit',
            'units'                => $units,
            'customers'            => $customers,
            'unitModel'            => $unitModel,
            'customerModel'        => $customerModel,
            'validation'        => $this->validation
        ];
        return view('dashboard/addPenjualan', $data);
    }

    public function editPenjualan($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editPenjualan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $puModel = new \App\Models\PenjualanunitModel();
        $unitModel = new \App\Models\UnitModel();
        $customerModel = new \App\Models\CustomerModel();

        $units = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->select('unit.*, types.type_nama')
            ->orderBy('unit.unit_nama', 'ASC')
            ->findAll();
        $customers = $customerModel->orderBy('cust_nama', 'ASC')->findAll();
        $penjualan = $puModel->find($id);

        $data = [
            'title_bar'         => 'Buat Penjualan Unit',
            'penjualan'         => $penjualan,
            'units'             => $units,
            'customers'         => $customers,
            'unitModel'         => $unitModel,
            'customerModel'     => $customerModel,
            'validation'        => $this->validation
        ];
        return view('dashboard/editPenjualan', $data);
    }

    public function insertPenjualan()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertPenjualan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tanggal = date('Y-m-d H:i:s', strtotime($request->getPost('tanggal') . ' ' . date('H:i:s')));
        $tglPengajuanKpr = $request->getPost('tglPengajuanKpr') ? date('Y-m-d H:i:s', strtotime($request->getPost('tglPengajuanKpr') . ' ' . date('H:i:s'))) : NULL;
        $tglAccKpr = $request->getPost('tglAccKpr') ? date('Y-m-d H:i:s', strtotime($request->getPost('tglAccKpr') . ' ' . date('H:i:s'))) : NULL;
        $tglRealisasiKpr = $request->getPost('tglRealisasiKpr') ? date('Y-m-d H:i:s', strtotime($request->getPost('tglRealisasiKpr') . ' ' . date('H:i:s'))) : NULL;

        $hargariil = $request->getPost('hargariil') ? str_replace('.', '', $request->getPost('hargariil')) : 0;
        $nup = $request->getPost('nup') ? str_replace('.', '', $request->getPost('nup')) : 0;
        $mutu = $request->getPost('mutu') ? str_replace('.', '', $request->getPost('mutu')) : 0;
        $tanahLebih = $request->getPost('tanahLebih') ? str_replace('.', '', $request->getPost('tanahLebih')) : 0;
        $sbum = $request->getPost('sbum') ? str_replace('.', '', $request->getPost('sbum')) : 0;
        $ajbn = $request->getPost('ajbn') ? str_replace('.', '', $request->getPost('ajbn')) : 0;
        $pph = $request->getPost('pph') ? str_replace('.', '', $request->getPost('pph')) : 0;
        $bphtb = $request->getPost('bphtb') ? str_replace('.', '', $request->getPost('bphtb')) : 0;
        $realisasi = $request->getPost('realisasi') ? str_replace('.', '', $request->getPost('realisasi')) : 0;
        $shm = $request->getPost('shm') ? str_replace('.', '', $request->getPost('shm')) : 0;
        $kanopi = $request->getPost('kanopi') ? str_replace('.', '', $request->getPost('kanopi')) : 0;
        $tandon = $request->getPost('tandon') ? str_replace('.', '', $request->getPost('tandon')) : 0;
        $pompair = $request->getPost('pompair') ? str_replace('.', '', $request->getPost('pompair')) : 0;
        $teralis = $request->getPost('teralis') ? str_replace('.', '', $request->getPost('teralis')) : 0;
        $tembok = $request->getPost('tembok') ? str_replace('.', '', $request->getPost('tembok')) : 0;
        $pondasi = $request->getPost('pondasi') ? str_replace('.', '', $request->getPost('pondasi')) : 0;
        $pijb = $request->getPost('pijb') ? str_replace('.', '', $request->getPost('pijb')) : 0;
        $ppn = $request->getPost('ppn') ? str_replace('.', '', $request->getPost('ppn')) : 0;
        $fee = $request->getPost('fee') ? str_replace('.', '', $request->getPost('fee')) : 0;

        $harga = $request->getPost('harga') ? str_replace('.', '', $request->getPost('harga')) : 0;
        $bayar = $request->getPost('bayar') ? str_replace('.', '', $request->getPost('bayar')) : 0;

        $data = [
            'pu_jenis'          => $request->getPost('jenis'),
            'pu_nomor'          => $request->getPost('nomor'),
            'pu_kaliangsur'     => $request->getPost('kaliangsur') ? $request->getPost('kaliangsur') : 0,
            'pu_tglTrx'         => $tanggal,
            'pu_marketing'      => $request->getPost('marketing'),
            'pu_cust'           => $request->getPost('customer'),
            'pu_unit'           => $request->getPost('unit'),

            'pu_hrgriil'        => $hargariil,
            'pu_nup'            => $nup,
            'pu_mutu'           => $mutu,
            'pu_tanahlebih'     => $tanahLebih,
            'pu_sbum'           => $sbum,
            'pu_ajbn'           => $ajbn,
            'pu_pph'            => $pph,
            'pu_bphtb'          => $bphtb,
            'pu_realisasi'      => $realisasi,
            'pu_shm'            => $shm,
            'pu_kanopi'         => $kanopi,
            'pu_tandon'         => $tandon,
            'pu_pompair'        => $pompair,
            'pu_teralis'        => $teralis,
            'pu_tembok'         => $tembok,
            'pu_pondasi'        => $pondasi,
            'pu_pijb'           => $pijb,
            'pu_ppn'            => $ppn,
            'pu_fee'            => $fee,

            'pu_harga'          => $harga,
            'pu_hargaKredit'    => $request->getPost('kreditHarga'),

            'pu_kpr'                => $request->getPost('kpr') ? $request->getPost('kpr') : NULL,
            'pu_tglPengajuanKpr'    => $tglPengajuanKpr,
            'pu_nilaiPengajuanKpr'  => $request->getPost('nilaiPengajuanKpr'),
            'pu_tglAccKpr'          => $tglAccKpr,
            'pu_nilaiAccKpr'        => $request->getPost('nilaiAccKpr'),
            'pu_tglRealisasiKpr'    => $tglRealisasiKpr,
            'pu_debetKpr'       => $request->getPost('debetKpr') ? $request->getPost('debetKpr') : NULL,

            'pu_sisa'           => $request->getPost('sisaBayar'),
            'pu_sisaDebet'      => $request->getPost('debetSisa') ? $request->getPost('debetSisa') : NULL,
            'pu_keterangan'     => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            'pu_user'           => $this->session->get('usr_id')
        ];

        $puModel = new \App\Models\PenjualanunitModel();
        $query = $puModel->insert($data);
        if ($query) {
            $idPu = $puModel->insertID();
            if ($bayar > 0) {
                $tagModel = new \App\Models\TagihanPuModel();
                $noInv = $tagModel->buatNoFaktur();
                $dataTag = [
                    'tp_pu'         => $idPu,
                    'tp_jenis'      => 1,
                    'tp_nomor'      => $noInv,
                    'tp_keterangan' => 'UANG MUKA/DP',
                    'tp_tgltrx'     => $tanggal,
                    'tp_nilai'      => $bayar,
                    'tp_jthtempo'   => $tanggal,
                    'tp_tglbayar'   => $tanggal,
                    'tp_nominal'    => $bayar,
                    'tp_debet'      => $request->getPost('debetBayar'),
                    'tp_user'       => $this->session->get('usr_id')
                ];
                $tagModel->insert($dataTag);
            }

            $bpModel = new \App\Models\BiayapenjualanModel();
            $dataRaw = json_decode($request->getPost('data'));
            foreach ($dataRaw as $row) {
                $tanggalNup = date('Y-m-d H:i:s', strtotime($row->tanggal . ' ' . date('H:i:s')));
                $biayaLain[] = [
                    'bp_tanggal'    => $tanggalNup,
                    'bp_penjualan'  => $idPu,
                    'bp_biayalain'  => $row->biayalain,
                    'bp_uraian'     => $row->name,
                    'bp_nominal'    => $row->price ? str_replace('.', '', $row->price) : 0,
                    'bp_debet'      => $row->debetId ? $row->debetId : NULL,
                    'bp_kredit'     => $row->kreditId ? $row->kreditId : NULL,
                    'bp_user'       => $this->session->get('usr_id'),
                    'bp_kembali'    => $row->kembali
                ];
            }
            if (isset($biayaLain)) {
                $bpModel->insertBatch($biayaLain);
            }

            return json_encode(['status' => true], true);
        } else {
            return json_encode(['status' => false], true);
        }
    }

    public function updatePenjualan()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updatePenjualan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tanggal = date('Y-m-d H:i:s', strtotime($request->getPost('tanggal') . ' ' . date('H:i:s')));
        $tglPengajuanKpr = $request->getPost('tglPengajuanKpr') ? date('Y-m-d H:i:s', strtotime($request->getPost('tglPengajuanKpr') . ' ' . date('H:i:s'))) : NULL;
        $tglAccKpr = $request->getPost('tglAccKpr') ? date('Y-m-d H:i:s', strtotime($request->getPost('tglAccKpr') . ' ' . date('H:i:s'))) : NULL;
        $tglRealisasiKpr = $request->getPost('tglRealisasiKpr') ? date('Y-m-d H:i:s', strtotime($request->getPost('tglRealisasiKpr') . ' ' . date('H:i:s'))) : NULL;

        $hargariil = $request->getPost('hargariil') ? str_replace('.', '', $request->getPost('hargariil')) : 0;
        $nup = $request->getPost('nup') ? str_replace('.', '', $request->getPost('nup')) : 0;
        $mutu = $request->getPost('mutu') ? str_replace('.', '', $request->getPost('mutu')) : 0;
        $tanahLebih = $request->getPost('tanahLebih') ? str_replace('.', '', $request->getPost('tanahLebih')) : 0;
        $sbum = $request->getPost('sbum') ? str_replace('.', '', $request->getPost('sbum')) : 0;
        $ajbn = $request->getPost('ajbn') ? str_replace('.', '', $request->getPost('ajbn')) : 0;
        $pph = $request->getPost('pph') ? str_replace('.', '', $request->getPost('pph')) : 0;
        $bphtb = $request->getPost('bphtb') ? str_replace('.', '', $request->getPost('bphtb')) : 0;
        $realisasi = $request->getPost('realisasi') ? str_replace('.', '', $request->getPost('realisasi')) : 0;
        $shm = $request->getPost('shm') ? str_replace('.', '', $request->getPost('shm')) : 0;
        $kanopi = $request->getPost('kanopi') ? str_replace('.', '', $request->getPost('kanopi')) : 0;
        $tandon = $request->getPost('tandon') ? str_replace('.', '', $request->getPost('tandon')) : 0;
        $pompair = $request->getPost('pompair') ? str_replace('.', '', $request->getPost('pompair')) : 0;
        $teralis = $request->getPost('teralis') ? str_replace('.', '', $request->getPost('teralis')) : 0;
        $tembok = $request->getPost('tembok') ? str_replace('.', '', $request->getPost('tembok')) : 0;
        $pondasi = $request->getPost('pondasi') ? str_replace('.', '', $request->getPost('pondasi')) : 0;
        $pijb = $request->getPost('pijb') ? str_replace('.', '', $request->getPost('pijb')) : 0;
        $ppn = $request->getPost('ppn') ? str_replace('.', '', $request->getPost('ppn')) : 0;
        $fee = $request->getPost('fee') ? str_replace('.', '', $request->getPost('fee')) : 0;

        $harga = $request->getPost('harga') ? str_replace('.', '', $request->getPost('harga')) : 0;
        $bayar = $request->getPost('bayar') ? str_replace('.', '', $request->getPost('bayar')) : 0;

        $data = [
            'pu_jenis'          => $request->getPost('jenis'),
            'pu_nomor'          => $request->getPost('nomor'),
            'pu_kaliangsur'     => $request->getPost('kaliangsur') ? $request->getPost('kaliangsur') : 0,
            'pu_tglTrx'         => $tanggal,
            'pu_marketing'      => $request->getPost('marketing'),
            'pu_cust'           => $request->getPost('customer'),
            'pu_unit'           => $request->getPost('unit'),

            'pu_hrgriil'        => $hargariil,
            'pu_nup'            => $nup,
            'pu_mutu'           => $mutu,
            'pu_tanahlebih'     => $tanahLebih,
            'pu_sbum'           => $sbum,
            'pu_ajbn'           => $ajbn,
            'pu_pph'            => $pph,
            'pu_bphtb'          => $bphtb,
            'pu_realisasi'      => $realisasi,
            'pu_shm'            => $shm,
            'pu_kanopi'         => $kanopi,
            'pu_tandon'         => $tandon,
            'pu_pompair'        => $pompair,
            'pu_teralis'        => $teralis,
            'pu_tembok'         => $tembok,
            'pu_pondasi'        => $pondasi,
            'pu_pijb'           => $pijb,
            'pu_ppn'            => $ppn,
            'pu_fee'            => $fee,

            'pu_harga'          => $harga,
            'pu_hargaKredit'    => $request->getPost('kreditHarga'),

            'pu_kpr'                => $request->getPost('kpr') ? $request->getPost('kpr') : NULL,
            'pu_tglPengajuanKpr'    => $tglPengajuanKpr,
            'pu_nilaiPengajuanKpr'  => $request->getPost('nilaiPengajuanKpr'),
            'pu_tglAccKpr'          => $tglAccKpr,
            'pu_nilaiAccKpr'        => $request->getPost('nilaiAccKpr'),
            'pu_tglRealisasiKpr'    => $tglRealisasiKpr,
            'pu_debetKpr'       => $request->getPost('debetKpr') ? $request->getPost('debetKpr') : NULL,

            'pu_sisa'           => $request->getPost('sisaBayar'),
            'pu_sisaDebet'      => $request->getPost('debetSisa') ? $request->getPost('debetSisa') : NULL,
            'pu_keterangan'     => $request->getPost('catatan') ? $request->getPost('catatan') : NULL,
            'pu_user'           => $this->session->get('usr_id')
        ];

        $puModel = new \App\Models\PenjualanunitModel();
        $query = $puModel->update(['pu_id' => $request->getPost('puId')], $data);
        if ($query) {
            $idPu = $request->getPost('puId');
            if ($bayar > 0) {
                $tagModel = new \App\Models\TagihanPuModel();
                $dataTag = [
                    'tp_jenis'      => 1,
                    'tp_keterangan' => 'UANG MUKA/DP',
                    'tp_tgltrx'     => $tanggal,
                    'tp_nilai'      => $bayar,
                    'tp_jthtempo'   => $tanggal,
                    'tp_tglbayar'   => $tanggal,
                    'tp_nominal'    => $bayar,
                    'tp_debet'      => $request->getPost('debetBayar'),
                    'tp_user'       => $this->session->get('usr_id')
                ];
                $tagModel->update(['tp_id' => $request->getPost('tagId')], $dataTag);
            }

            $bpModel = new \App\Models\BiayapenjualanModel();
            $dataRaw = json_decode($request->getPost('data'));
            foreach ($dataRaw as $row) {
                $tanggalNup = date('Y-m-d H:i:s', strtotime($row->tanggal . ' ' . date('H:i:s')));
                $biayaLain[] = [
                    'bp_tanggal'    => $tanggalNup,
                    'bp_penjualan'  => $idPu,
                    'bp_biayalain'  => $row->biayalain,
                    'bp_uraian'     => $row->name,
                    'bp_nominal'    => $row->price ? str_replace('.', '', $row->price) : 0,
                    'bp_debet'      => $row->debetId ? $row->debetId : NULL,
                    'bp_kredit'     => $row->kreditId ? $row->kreditId : NULL,
                    'bp_user'       => $this->session->get('usr_id'),
                    'bp_kembali'    => $row->kembali
                ];
            }
            if (isset($biayaLain)) {
                $bpModel->insertBatch($biayaLain);
            }

            return json_encode(['status' => true], true);
        } else {
            return json_encode(['status' => false], true);
        }
    }

    public function deletePenjualan()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deletePenjualan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $penjualanModel = new \App\Models\PenjualanunitModel();
            $penjualan = $penjualanModel->find($id);
            if ($penjualan) {
                $query = $penjualanModel->delete($id);
                if ($query) {
                    $bpModel = new \App\Models\BiayapenjualanModel();
                    $tagModel = new \App\Models\TagihanPuModel();
                    $tagModel->where('tp_pu', $penjualan['pu_id'])->delete();
                    $bpModel->where('bp_penjualan', $penjualan['pu_id'])->delete();
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/penjualanunit');
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/penjualanunit');
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/penjualanunit');
            }
        }
    }

    public function deleteItemBiayaLain()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deletePenjualan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $bpModel = new \App\Models\BiayapenjualanModel();
            $bp = $bpModel->find($id);
            if ($bp) {
                $query = $bpModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->back();
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->back();
            }
        }
    }

    public function piutangpenjualan($puId)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("penjualanunit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $unitModel = new \App\Models\UnitModel();
        $customerModel = new \App\Models\CustomerModel();
        $penjualanModel = new \App\Models\PenjualanunitModel();

        $units = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->select('unit.*, types.type_nama')
            ->orderBy('unit.unit_nama', 'ASC')
            ->findAll();
        $customers = $customerModel->orderBy('cust_nama', 'ASC')->findAll();
        $penjualan = $penjualanModel->find($puId);

        $data = [
            'title_bar'     => 'Piutang Penjualan Unit',
            'units'         => $units,
            'penjualan'     => $penjualan,
            'customers'     => $customers,
            'unitModel'     => $unitModel,
            'customerModel' => $customerModel,
            'validation'    => $this->validation
        ];
        return view('dashboard/piutangpenjualan', $data);
    }

    public function insertPiutang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertPenjualan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $tagModel = new \App\Models\TagihanPuModel();
        $dataRaw = json_decode($request->getPost('data'));

        foreach ($dataRaw as $row) {
            $noInv = $tagModel->buatNoFaktur();
            $dataTag = [
                'tp_pu'         => $request->getPost('puId'),
                'tp_jenis'      => $row->jenis,
                'tp_nomor'      => $noInv,
                'tp_angsuran'   => $row->name,
                'tp_keterangan' => $row->keterangan,
                'tp_tgltrx'     => date('Y-m-d H:i:s'),
                'tp_nilai'      => $row->price,
                'tp_jthtempo'   => date('Y-m-d H:i:s', strtotime($row->tanggal . ' ' . date('H:i:s'))),
                'tp_tglbayar'   => NULL,
                'tp_nominal'    => 0,
                'tp_debet'      => NULL,
                'tp_kredit'     => NULL,
                'tp_user'       => $this->session->get('usr_id')
            ];
            $tagModel->insert($dataTag);
        }

        return json_encode(['status' => true], true);
    }

    public function tagihanJson($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("penjualanunit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $tagModel = new \App\Models\TagihanPuModel();
        $tagihan = $tagModel->find($id);
        $dataTagihan = [
            'tp_id'         => $tagihan['tp_id'],
            'tp_jenis'      => $tagihan['tp_jenis'],
            'tp_pu'         => $tagihan['tp_pu'],
            'tp_nomor'      => $tagihan['tp_nomor'],
            'tp_angsuran'   => $tagihan['tp_angsuran'],
            'tp_keterangan' => $tagihan['tp_keterangan'],
            'tp_jthtempo'   => $tagihan['tp_jthtempo'] ? date('d-m-Y', strtotime($tagihan['tp_jthtempo'])) : '',
            'tp_nilai'      => $tagihan['tp_nilai'],
            'tp_tglbayar'   => $tagihan['tp_tglbayar'] ? date('d-m-Y', strtotime($tagihan['tp_tglbayar'])) : '',
            'tp_nominal'    => $tagihan['tp_nominal'],
            'tp_debet'      => $tagihan['tp_debet'],
            'tp_kredit'     => $tagihan['tp_kredit']
        ];

        return json_encode($dataTagihan, true);
    }

    public function updatePiutang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updatePenjualan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $jthtempo = $request->getPost('tgljthtempo') ? date('Y-m-d H:i:s', strtotime($request->getPost('tgljthtempo') . ' ' . date('H:i:s'))) : NULL;
        $tglbayar = $request->getPost('tglbayar') ? date('Y-m-d H:i:s', strtotime($request->getPost('tglbayar') . ' ' . date('H:i:s'))) : NULL;
        $dataTagihan = [
            'tp_jenis'      => $request->getPost('jenis'),
            'tp_angsuran'   => $request->getPost('angsuran'),
            'tp_keterangan' => $request->getPost('uraian'),
            'tp_jthtempo'   => $jthtempo,
            'tp_nilai'      => $request->getPost('nilai') ? str_replace('.', '', $request->getPost('nilai')) : 0,
            'tp_tglbayar'   => $tglbayar,
            'tp_nominal'    => $request->getPost('bayar') ? str_replace('.', '', $request->getPost('bayar')) : 0,
            'tp_debet'      => $request->getPost('debet') ? $request->getPost('debet') : NULL,
            'tp_kredit'     => $request->getPost('kredit') ? $request->getPost('kredit') : NULL
        ];

        $tagModel = new \App\Models\TagihanPuModel();
        $query = $tagModel->update(['tp_id' => $request->getPost('idTp')], $dataTagihan);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        }
    }

    public function deletePiutang()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deletePenjualan", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $tagModel = new \App\Models\TagihanPuModel();
            $tag = $tagModel->find($id);
            if ($tag) {
                $query = $tagModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->back();
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->back();
            }
        }
    }

    public function batalkanPenjualanUnit($idPenjualan)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("penjualanunit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $unitModel = new \App\Models\UnitModel();
        $customerModel = new \App\Models\CustomerModel();
        $penjualanModel = new \App\Models\PenjualanunitModel();

        $units = $unitModel->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->select('unit.*, types.type_nama')
            ->orderBy('unit.unit_nama', 'ASC')
            ->findAll();
        $customers = $customerModel->orderBy('cust_nama', 'ASC')->findAll();
        $penjualan = $penjualanModel->find($idPenjualan);

        $data = [
            'title_bar'         => 'Pembatalan Penjualan Unit',
            'units'                => $units,
            'penjualan'            => $penjualan,
            'customers'            => $customers,
            'unitModel'            => $unitModel,
            'customerModel'        => $customerModel,
            'validation'        => $this->validation
        ];
        return view('dashboard/batalkanPenjualanUnit', $data);
    }

    public function pembatalanPenjualanUnit()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("penjualanunit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $penjualanModel = new \App\Models\PenjualanunitModel();
        $data = [
            'pu_status' => 2
        ];
        $query = $penjualanModel->update(['pu_id' => $request->getPost('puId')], $data);
        if ($query) {

            $bpModel = new \App\Models\BiayapenjualanModel();
            $dataRaw = json_decode($request->getPost('data'));
            foreach ($dataRaw as $row) {
                $tanggalNup = date('Y-m-d H:i:s', strtotime($row->tanggal . ' ' . date('H:i:s')));
                $biayaLain[] = [
                    'bp_tanggal'    => $tanggalNup,
                    'bp_penjualan'  => $request->getPost('puId'),
                    'bp_biayalain'  => $row->biayalain,
                    'bp_uraian'     => $row->name,
                    'bp_nominal'    => $row->price ? str_replace('.', '', $row->price) : 0,
                    'bp_debet'      => $row->debetId ? $row->debetId : NULL,
                    'bp_kredit'     => $row->kreditId ? $row->kreditId : NULL,
                    'bp_user'       => $this->session->get('usr_id'),
                    'bp_kembali'    => $row->kembali
                ];
            }
            if (isset($biayaLain)) {
                $bpModel->insertBatch($biayaLain);
            }

            return json_encode(['status' => true], true);
        } else {
            return json_encode(['status' => false], true);
        }
    }

    public function detailUnitJson($idUnit)
    {
        $unitModel = new \App\Models\UnitModel();

        $unit = $unitModel->where(['unit.unit_id' => $idUnit])
            ->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->select('unit.*, types.type_nama')->first();

        return json_encode($unit, true);
    }

    public function hppUnitJson($idUnit)
    {
        $progresModel = new \App\Models\ProgresModel();
        $nilai = $progresModel->getProgresUnit($idUnit);
        return json_encode(['nilaiTanah' => str_replace('.', ',', ceil($nilai['nilaiHpp']))], true);
    }

    public function estatem()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("estatem", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $emModel = new \App\Models\EmModel();

        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $emModel->where('e_management.em_tanggal >=', $start);
            $emModel->where('e_management.em_tanggal <=', $end);
        }
        if ($request->getVar('jenis')) {
            $emModel->where('e_management.em_jenis', $request->getVar('jenis'));
        }
        if ($request->getVar('unit')) {
            $emModel->where('e_management.em_unit', $request->getVar('unit'));
        }
        if ($request->getVar('nomor')) {
            $emModel->where('e_management.em_nomor', $request->getVar('nomor'));
        }

        $em = $emModel->join('unit', 'unit.unit_id = e_management.em_unit', 'left')
            ->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->join('penjualan_unit', 'penjualan_unit.pu_unit = e_management.em_unit', 'left')
            ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
            ->select('e_management.*, unit.unit_nama, types.type_nama, customers.cust_nama')
            ->orderBy('e_management.em_tanggal', 'DESC');

        $data = [
            'title_bar'     => 'Estate Management',
            'em'            => $em->findAll(),
            'emModel'       => $emModel,
            'pager'         => $emModel->pager,
            'current'       => $currentPage,
            'totalRows'     => $em->countAll(),
            'validation'    => $this->validation
        ];
        return view('dashboard/estatem', $data);
    }

    public function addEstatem()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("addEstatem", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Tambah Data',
            'validation'    => $this->validation
        ];
        return view('dashboard/addEstatem', $data);
    }

    public function insertEstatem()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertEstatem", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $validate = $this->validate([
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nomor' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'nama' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'jenis' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nominal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'debet' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kredit' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->back()->withInput();
        }

        $request = \Config\Services::request();
        $emModel = new \App\Models\EmModel();

        $nomor = $request->getPost('nomor');
        $checkIsExist = $emModel->where('em_nomor', $nomor)->first();
        if ($checkIsExist) {
            $exNumb = str_replace('EM', '', $nomor);
            $newNumb = intval($exNumb) + 1;
            $fixNumber = 'EM' . str_pad($newNumb, 4, "0", STR_PAD_LEFT);
        } else {
            $fixNumber = $nomor;
        }

        $data = [
            'em_tanggal'        => date('Y-m-d H:i:s', strtotime($request->getPost('tanggal') . ' ' . date('H:i:s'))),
            'em_nomor'          => $fixNumber,
            'em_nama'           => $request->getPost('nama'),
            'em_unit'           => $request->getPost('unit') ? $request->getPost('unit') : NULL,
            'em_jenis'          => $request->getPost('jenis'),
            'em_nominal'        => $request->getPost('nominal') ? str_replace('.', '', $request->getPost('nominal')) : 0,
            'em_debet'          => $request->getPost('debet'),
            'em_kredit'         => $request->getPost('kredit'),
            'em_keterangan'     => $request->getPost('keterangan'),
            'em_user'           => $this->session->get('usr_id')
        ];

        $query = $emModel->insert($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            if ($request->getPost('jenis') == 1) {
                return redirect()->to('dashboard/printem/' . $request->getPost('jenis') . '/' . $emModel->insertID());
            } else {
                return redirect()->back();
            }
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back()->withInput();
        }
    }

    public function editEstatem($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("editEstatem", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }
        $emModel = new \App\Models\EmModel();
        $em = $emModel->join('unit', 'unit.unit_id = e_management.em_unit', 'left')
            ->join('types', 'types.type_id = unit.unit_tipe', 'left')
            ->join('penjualan_unit', 'penjualan_unit.pu_unit = e_management.em_unit', 'left')
            ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
            ->select('e_management.*, unit.unit_nama, types.type_nama, customers.cust_nama')
            ->where('e_management.em_id', $id)->first();

        $data = [
            'title_bar'     => 'Perbarui Data',
            'em'            => $em,
            'validation'    => $this->validation
        ];
        return view('dashboard/editEstatem', $data);
    }

    public function updateEstatem()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateEstatem", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $validate = $this->validate([
            'tanggal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nomor' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'nama' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'jenis' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'nominal' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'debet' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'kredit' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            return redirect()->back()->withInput();
        }

        $request = \Config\Services::request();
        $emModel = new \App\Models\EmModel();

        $data = [
            'em_tanggal'        => date('Y-m-d H:i:s', strtotime($request->getPost('tanggal') . ' ' . date('H:i:s'))),
            'em_nomor'          => $request->getPost('nomor'),
            'em_nama'           => $request->getPost('nama'),
            'em_unit'           => $request->getPost('unit') ? $request->getPost('unit') : NULL,
            'em_jenis'          => $request->getPost('jenis'),
            'em_nominal'        => $request->getPost('nominal') ? str_replace('.', '', $request->getPost('nominal')) : 0,
            'em_debet'          => $request->getPost('debet'),
            'em_kredit'         => $request->getPost('kredit'),
            'em_keterangan'     => $request->getPost('keterangan'),
            'em_user'           => $this->session->get('usr_id')
        ];

        $query = $emModel->update(['em_id' => $request->getPost('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil diperbarui.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal diperbarui.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back()->withInput();
        }
    }

    public function deleteEstatem()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteEstatem", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $emModel = new \App\Models\EmModel();
            $em = $emModel->find($id);
            if ($em) {
                $query = $emModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function printem($jenis, $id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("estatem", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        if ($jenis == 1) {
            $emModel = new \App\Models\EmModel();
            $em = $emModel->where(['e_management.em_id' => $id])
                ->join('unit', 'unit.unit_id = e_management.em_unit', 'left')
                ->select('e_management.*, unit.unit_nama')->first();

            $data = [
                'title_bar' => 'PEMBAYARAN IURAN ESTATE MANAGEMENT - ' . strtoupper($em['em_nama']) . ' - ' . date('m', strtotime($em['em_tanggal'])) . '/' . date('Y', strtotime($em['em_tanggal'])),
                'em'        => $em
            ];

            $options = new Options();
            $options->set('isRemoteEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $dompdf = new Dompdf();
            $dompdf->setOptions($options);
            $dompdf->set_base_path(realpath(ROOTPATH . '/public/'));
            $customPaper = array(0, 0, 164.409, 283.465); // 5,8 cm x 10 cm
            $dompdf->setPaper('A4', 'potrait');
            $dompdf->loadHtml(view('dashboard/printem', $data));
            $dompdf->render();
            return $dompdf->stream($data['title_bar'] . ".pdf", array("Attachment" => false));
        } else {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }
    }

    public function marketing()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("marketing", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $marketingModel = new \App\Models\MarketingModel();
        $marketing = $marketingModel->orderBy('m_id', 'DESC');
        $data = [
            'title_bar'         => 'Data Marketing',
            'marketing'         => $marketing->paginate(25, 'view'),
            'marketingModel'    => $marketingModel,
            'pager'             => $marketingModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($marketing->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/marketing', $data);
    }

    public function insertMarketing()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertMarketing", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'nama' => [
                'rules'     => 'required|is_unique[marketing.m_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama marketing harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/marketing')->withInput();
        }
        $data = [
            'm_nama'    => $request->getVar('nama'),
            'm_telp'    => $request->getVar('telp'),
            'm_alamat'  => $request->getVar('alamat'),
        ];
        $marketingModel = new \App\Models\MarketingModel();
        $query = $marketingModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/marketing');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/marketing')->withInput();
        }
    }

    public function marketingJson($id)
    {
        $marketingModel = new \App\Models\MarketingModel();
        $marketing = $marketingModel->find($id);
        return json_encode($marketing, true);
    }

    public function updateMarketing()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateMarketing", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $marketingModel = new \App\Models\MarketingModel();
        $old = $marketingModel->find($request->getVar('id'));

        if ($old['m_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[marketing.m_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama marketing harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/marketing')->withInput();
        }

        $data = [
            'm_nama'    => $request->getVar('nama'),
            'm_telp'    => $request->getVar('telp'),
            'm_alamat'  => $request->getVar('alamat'),
        ];

        $query = $marketingModel->update(['m_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/marketing');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/marketing')->withInput();
        }
    }

    public function deleteMarketing()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteMarketing", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $marketingModel = new \App\Models\MarketingModel();
            $marketing = $marketingModel->find($id);
            if ($marketing) {
                $query = $marketingModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function biayalain()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("biayalain", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $biayaLainModel = new \App\Models\BiayaLainModel();
        $biayalain = $biayaLainModel->orderBy('bl_id', 'DESC');
        $data = [
            'title_bar'         => 'Data Biaya Lain',
            'biayalain'         => $biayalain->paginate(25, 'view'),
            'biayaLainModel'    => $biayaLainModel,
            'pager'             => $biayaLainModel->pager,
            'current'           => $currentPage,
            'totalRows'         => count($biayalain->findAll()),
            'validation'        => $this->validation
        ];
        return view('dashboard/biayalain', $data);
    }

    public function insertBiayalain()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("insertBiayalain", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $validate = $this->validate([
            'nama' => [
                'rules'     => 'required|is_unique[biayalain.bl_nama]',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama biaya lain harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/biayalain')->withInput();
        }
        $data = [
            'bl_nama'    => $request->getVar('nama')
        ];
        $biayaLainModel = new \App\Models\BiayaLainModel();
        $query = $biayaLainModel->save($data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/biayalain');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/biayalain')->withInput();
        }
    }

    public function biayalainJson($id)
    {
        $biayaLainModel = new \App\Models\BiayaLainModel();
        $biayalain = $biayaLainModel->find($id);
        return json_encode($biayalain, true);
    }

    public function updateBiayalain()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("updateBiayalain", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $biayaLainModel = new \App\Models\BiayaLainModel();
        $old = $biayaLainModel->find($request->getVar('id'));

        if ($old['bl_nama'] == $request->getVar('nama')) {
            $rule_nama = 'required';
        } else {
            $rule_nama = 'required|is_unique[biayalain.bl_nama]';
        }

        $validate = $this->validate([
            'nama' => [
                'rules'     => $rule_nama,
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
        ]);

        if (!$validate) {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Nama biaya lain harus diisi dan tidak boleh sama.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/biayalain')->withInput();
        }

        $data = [
            'bl_nama'    => $request->getVar('nama')
        ];

        $query = $biayaLainModel->update(['bl_id' => $request->getVar('id')], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/biayalain');
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/biayalain')->withInput();
        }
    }

    public function deleteBiayalain()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("deleteBiayalain", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $id = $request->getVar('id');
        if ($id) {
            $biayaLainModel = new \App\Models\BiayaLainModel();
            $biayalain = $biayaLainModel->find($id);
            if ($biayalain) {
                $query = $biayaLainModel->delete($id);
                if ($query) {
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal dihapus.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return '<script>window.history.go(-1);</script>';
                }
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return '<script>window.history.go(-1);</script>';
            }
        }
    }

    public function piutanglist()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("penjualanunit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;

        $tagModel = new \App\Models\TagihanPuModel();
        $tagModel->join('penjualan_unit', 'penjualan_unit.pu_id = tagihanpu.tp_pu', 'left')
            ->join('customers', 'customers.cust_id = penjualan_unit.pu_cust', 'left')
            ->join('unit', 'unit.unit_id = penjualan_unit.pu_unit', 'left');

        $startDate = $request->getVar('startDate');
        $endDate = $request->getVar('endDate');
        if ($startDate && $endDate) {
            $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
            $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';
            $tagModel->where('tagihanpu.tp_jthtempo >=', $start);
            $tagModel->where('tagihanpu.tp_jthtempo <=', $end);
        }

        if ($request->getVar('nomor')) {
            $tagModel->groupStart();
            $tagModel->like('penjualan_unit.pu_nomor', $request->getVar('nomor'));
            $tagModel->groupEnd();
        }

        // if ($request->getVar('jenis')) {
        //     $tagModel->where('penjualan_unit.pu_jenis', $request->getVar('jenis'));
        // }

        if ($request->getVar('customer')) {
            $tagModel->where('customers.cust_id', $request->getVar('customer'));
        }

        // if ($request->getVar('kpr')) {
        //     $tagModel->where('penjualan_unit.pu_kpr', $request->getVar('kpr'));
        // }

        if ($request->getVar('unit')) {
            $tagModel->where('unit.unit_id', $request->getVar('unit'));
        }

        $tagihan = $tagModel->select('tagihanpu.*, unit.unit_nama, customers.cust_nama')->orderBy('tagihanpu.tp_id', 'DESC');

        $data = [
            'title_bar'     => 'Piutang Penjualan Unit',
            'tagihan'       => $tagihan->paginate(250, 'view'),
            'tagModel'      => $tagModel,
            'pager'         => $tagModel->pager,
            'current'       => $currentPage,
            'totalRows'     => count($tagihan->findAll()),
            'validation'    => $this->validation
        ];

        return view('dashboard/piutanglist', $data);
    }

    public function bayarpiutang($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("penjualanunit", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $tagModel = new \App\Models\TagihanPuModel();
        $tagihan = $tagModel->find($id);

        $data = [
            'title_bar'     => 'Bayar Piutang Penjualan',
            'tagihan'       => $tagihan,
            'validation'    => $this->validation
        ];
        return view('dashboard/bayarpiutang', $data);
    }

    // check in di radius kantor
    public function checkin()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kehadiran", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Check In (Masuk)',
            'validation'    => $this->validation
        ];
        return view('dashboard/checkin', $data);
    }

    // check out di radius kantor
    public function checkout()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kehadiran", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Check Out (Keluar)',
            'validation'    => $this->validation
        ];
        return view('dashboard/checkout', $data);
    }

    // check in/out di radius kantor
    public function checkInsert()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kehadiran", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $userModel = new \App\Models\UserModel();

        $checkinoutModel = new \App\Models\CheckInOutModel();
        $validate = $this->validate([
            'tanggal' => [
                'rules'        => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'waktu' => [
                'rules'        => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'latitude' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'longitude' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'type' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
        ]);
        if (!$validate) {
            return redirect()->back()->withInput();
        }

        $timeworkModel = new \App\Models\JamkerjaModel();
        $usr = $userModel->find($this->session->get('usr_id'));

        $thisDay = date('l');
        if ($thisDay == 'Saturday' && $usr['usr_jamkerja2']) {
            $usrJamKerja = $timeworkModel->find($usr['usr_jamkerja2']);
            $jamKerja = date_create(date('Y-m-d H:i', strtotime(date('Y-m-d') . ' ' . $usrJamKerja['jk_mulai'])));
        } else {
            $usrJamKerja = $timeworkModel->find($usr['usr_jamkerja']);
            $jamKerja = date_create(date('Y-m-d H:i', strtotime(date('Y-m-d') . ' ' . $usrJamKerja['jk_mulai'])));
        }

        if ($this->request->getPost('type') == 'in') {
            $mulaiCheckin = date_add($jamKerja, date_interval_create_from_date_string('-240 minutes'));

            $bisaCheckin = strtotime(date_format($mulaiCheckin, "Y-m-d H:i"));
            $dateNow = strtotime(date('Y-m-d') . ' ' . $usrJamKerja['jk_mulai']);
            $dateCheckIn = strtotime(date('Y-m-d H:i'));

            $checkTime = ($dateCheckIn >= $bisaCheckin) ? true : false;
            if ($checkTime == false) {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Tidak dapat melakukan check-in.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->back()->withInput();
                die;
            }

            if ($dateNow < $dateCheckIn) {
                $telat = 'yes';

                $diff   = $dateCheckIn - $dateNow;
                $jam    = floor($diff / (60 * 60));
                $menit  = $diff - $jam * (60 * 60);
                $terlambat = ($jam * 60) + floor($menit / 60);

                $punishmentModel =  new \App\Models\PunishmentModel();
                $punishment = $punishmentModel->where('pun_user', $usr['usr_id'])
                    ->orderBy('pun_waktu', 'DESC')->findAll();

                $punResult = [];
                if ($punishment) {
                    array_multisort(array_map(function ($element) {
                        return $element['pun_waktu'];
                    }, $punishment), SORT_DESC, $punishment);

                    foreach ($punishment as $row) {
                        if ($terlambat >= $row['pun_waktu']) {
                            $punResult[] = $row;
                        }
                    }
                }
            } else {
                $telat = 'no';
            }
        }

        if ($this->request->getPost('type') == 'out') {
            // $jamKerja = date_create(date('Y-m-d H:i', strtotime(date('Y-m-d') . ' ' . $usrJamKerja['jk_selesai'])));
            // $mulaiCheckin = date_add($jamKerja, date_interval_create_from_date_string('120 minutes'));
            // $bisaCheckin = strtotime(date_format($mulaiCheckin, "Y-m-d H:i"));

            $dateNow = strtotime(date('Y-m-d') . ' ' . $usrJamKerja['jk_selesai']);
            $dateCheckIn = strtotime(date('Y-m-d H:i'));
            $checkTime = ($dateCheckIn >= $dateNow) ? true : false;
            if ($checkTime == false) {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Tidak dapat melakukan check-out.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->back()->withInput();
                die;
            }
        }

        $timeStamps = time();
        $data = [
            'ci_user'       => $this->session->get('usr_id'),
            'ci_latitude'   => $this->request->getPost('latitude'),
            'ci_longitude'  => $this->request->getPost('longitude'),
            'ci_time'       => $timeStamps,
            'ci_type'       => $this->request->getPost('type'),
            'ci_deskripsi'  => $this->request->getPost('deskripsi'),
            'ci_telat'      => isset($telat) ? $telat : NULL,
            'ci_punishment' => isset($punResult) ? $punResult[0]['pun_id'] : NULL,
        ];
        $query = $checkinoutModel->insert($data);
        if ($query) {

            $sendWa = new \App\Libraries\Sendwa();
            $sendWa->sendNotifAbsen($this->session->get('usr_id'), date('Y-m-d H:i:s'), $this->request->getPost('type'), $this->request->getPost('deskripsi'));

            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Data berhasil disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data gagal disimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back()->withInput();
        }
    }

    public function absen()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kehadiran", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Absen (Tidak Hadir)',
            'validation'    => $this->validation
        ];
        return view('dashboard/absen', $data);
    }

    public function absenInsert()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kehadiran", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $absenModel = new \App\Models\AbsenModel();
        $validate = $this->validate([
            'tanggal' => [
                'rules'        => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'waktu' => [
                'rules'        => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'latitude' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'longitude' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'absen' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'deskripsi' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
        ]);
        if (!$validate) {
            return redirect()->back()->withInput();
        }
        $data = [
            'ab_user'       => $this->session->get('usr_id'),
            'ab_latitude'   => $this->request->getPost('latitude'),
            'ab_longitude'  => $this->request->getPost('longitude'),
            'ab_jenis'      => $this->request->getPost('absen'),
            'ab_deskripsi'  => $this->request->getPost('deskripsi')
        ];
        $query = $absenModel->save($data);
        if ($query) {

            $sendWa = new \App\Libraries\Sendwa();
            $sendWa->sendNotifTdkHadir($this->session->get('usr_id'), date('Y-m-d H:i:s'), $this->request->getPost('absen'), $this->request->getPost('deskripsi'));

            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show small" role="alert">Data telah tersimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-warning alert-dismissible fade show small" role="alert">Oops... Terjadi kesalahan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back()->withInput();
        }
    }

    public function getAbsen()
    {
        // $lat = $this->request->getPost('lat');
        // $lng = $this->request->getPost('lng');

        $absenModel = new \App\Models\AbsenModel();
        $absen = $absenModel->getAbsen(session()->get('usr_id'), date('Y-m-d'));
        if ($absen) {
            return json_encode($absen, true);
        } else {
            return json_encode(['status' => false], true);
        }
    }

    public function absenUpdate()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kehadiran", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $validate = $this->validate([
            'tanggal' => [
                'rules'        => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'waktu' => [
                'rules'        => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.',
                ]
            ],
            'latitude' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'longitude' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'absen' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ],
            'deskripsi' => [
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'Kolom ini harus diisi.'
                ]
            ]
        ]);
        if (!$validate) {
            return redirect()->back()->withInput();
        }
        $data = [
            'ab_user'       => $this->session->get('usr_id'),
            'ab_latitude'   => $this->request->getPost('latitude'),
            'ab_longitude'  => $this->request->getPost('longitude'),
            'ab_jenis'      => $this->request->getPost('absen'),
            'ab_deskripsi'  => $this->request->getPost('deskripsi')
        ];
        $absenModel = new \App\Models\AbsenModel();
        $query = $absenModel->update([
            'ab_id' => $this->request->getPost('id'),
        ], $data);
        if ($query) {
            session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show small" role="alert">Data telah tersimpan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back();
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-warning alert-dismissible fade show small" role="alert">Oops... Terjadi kesalahan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->back()->withInput();
        }
    }

    public function kehadiran()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kehadiran", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'Rekapan Kehadiran',
        ];
        return view('dashboard/kehadiran', $data);
    }

    public function rekapkehadiran()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("kehadiran", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $data = [
            'title_bar'     => 'REKAP KEHADIARAN TANGGAL ' . date('d/m/Y', strtotime($this->request->getVar('startDate'))) . ' - ' . date('d/m/Y', strtotime($this->request->getVar('endDate'))),
        ];
        return view('dashboard/rekapkehadiran', $data);
    }

    public function getDetailCheckInOut($id, $tgl)
    {
        if ($id && $tgl) {
            $checkInOutModel = new \App\Models\CheckInOutModel();
            $checkin = $checkInOutModel
                ->where(['check_inout.ci_user' => $id])
                ->join('user', 'user.usr_id = check_inout.ci_user', 'left')
                ->join('punishment', 'punishment.pun_id = check_inout.ci_punishment AND punishment.pun_user = check_inout.ci_user ', 'left')
                ->groupStart()->like('check_inout.created_at', $tgl)->groupEnd()
                ->select('check_inout.*, user.usr_id, user.usr_nama, punishment.pun_id, punishment.pun_nama,punishment.pun_waktu, punishment.pun_potongan')
                ->findAll();
            if ($checkin) {
                return json_encode($checkin, true);
            } else {
                return json_encode(['status' => false], true);
            }
        }
    }

    public function whatsapp()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("masterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $whatsApp = new \App\Libraries\WhatsApp();
        $data['title_bar'] = 'Status & Scan QR-Code WhatsApp';
        $data['wa'] = $whatsApp->auth();
        return view('dashboard/scan', $data);
    }

    public function waHistory()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("masterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $request = \Config\Services::request();
        $currentPage = $request->getVar('page_view') ? $request->getVar('page_view') : 1;
        $walogs = new \App\Models\Walogs();

        $data = [
            'title_bar'     => 'Histori Pesan WhatsApp',
            'results'       => $walogs->orderBy('wa_id', 'DESC')->paginate(50, 'view'),
            'pager'         => $walogs->pager,
            'current'       => $currentPage,
            'validation'    => $this->validation
        ];
        return view('dashboard/waHistory', $data);
    }

    public function resendwa($id)
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("masterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $walogs = new \App\Models\Walogs();
        $log = $walogs->find($id);
        if ($log) {
            $whatsApp =  new \App\Libraries\WhatsApp();
            $response = $whatsApp->groupsendmessage($log['wa_number'], $log['wa_message']);
            if ($response) {
                $walogs->update(['wa_id', $id], ['wa_status' => 1]);
                session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Pesan berhasil dikirim.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/waHistory');
            } else {
                session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Pesan gagal dikirim.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                return redirect()->to('/dashboard/waHistory');
            }
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/waHistory');
        }
    }

    public function resendall()
    {
        $sesiBagian = $this->session->get('usr_bagian');
        $bagianModel = new \App\Models\BagianModel();
        $bagian = $bagianModel->find($sesiBagian);
        $akses = explode(',', $bagian['bagian_akses']);
        if (!in_array("masterAbsensi", $akses)) {
            return view('404', ['title_bar' => '404 | Page Not Found']);
        }

        $walogs = new \App\Models\Walogs();
        $logs = $walogs->where('wa_status', 0)->orderBy('created_at', 'ASC')->findAll();
        if ($logs) {
            $whatsApp =  new \App\Libraries\WhatsApp();
            foreach ($logs as $row) {
                $response = $whatsApp->groupsendmessage($row['wa_number'], $row['wa_message']);
                if ($response) {
                    $walogs->update(['wa_id', $row['wa_id']], ['wa_status' => 1]);
                    session()->setFlashdata('pesan', '<div class="alert alert-success alert-dismissible fade show" role="alert">Pesan berhasil dikirim.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/waHistory');
                } else {
                    session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Pesan gagal dikirim.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                    return redirect()->to('/dashboard/waHistory');
                }
            }
        } else {
            session()->setFlashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">Data tidak ditemukan.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            return redirect()->to('/dashboard/waHistory');
        }
    }

    public function whatsappAuth()
    {
        $whatsApp = new \App\Libraries\WhatsApp();
        $response = $whatsApp->auth();
        return json_encode($response, true);
    }

    public function whatsappLogout()
    {
        $whatsApp = new \App\Libraries\WhatsApp();
        $response = $whatsApp->logout();
        return json_encode($response, true);
    }

    public function contacts()
    {
        $whatsApp = new \App\Libraries\WhatsApp();
        $contacts = $whatsApp->contacts();
        return json_encode($contacts, true);
    }

    // public function test()
    // {
    //     $diff   = strtotime(date('Y-m-d H:i')) - strtotime(date('Y-m-d') . ' 10:00');
    //     $jam    = floor($diff / (60 * 60));
    //     $menit  = $diff - $jam * (60 * 60);
    //     $terlambat = ($jam * 60) + floor($menit / 60);

    //     $punishmentModel =  new \App\Models\PunishmentModel();
    //     $punishment = $punishmentModel
    //         ->orderBy('pun_waktu', 'DESC')->findAll();
    //     $punResult = [];

    //     array_multisort(array_map(function ($element) {
    //         return $element['pun_waktu'];
    //     }, $punishment), SORT_DESC, $punishment);

    //     foreach ($punishment as $row) {
    //         if ($terlambat >= $row['pun_waktu']) {
    //             $punResult[] = $row;
    //         }
    //     }

    //     dd($punResult[0]);
    // }
}
