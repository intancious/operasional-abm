<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsenModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'absen';
    protected $primaryKey           = 'ab_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDelete        = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['ab_user', 'ab_latitude', 'ab_longitude', 'ab_jenis', 'ab_deskripsi'];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function getLocation($lat, $lng)
    {
        $this->where(['ab_latitude' => $lat]);
        $this->where(['ab_longitude' => $lng]);
        $this->where(['ab_user' => session()->get('usr_id')]);
        $this->groupStart();
        $this->like('created_at', date('Y-m-d'));
        $this->groupEnd();
        return $this->first();
    }

    public function getAbsen($user, $tgl)
    {
        $this->where(['ab_user' => $user]);
        $this->like('created_at', $tgl);
        return $this->first();
    }

    public function countAbsen($user, $jenis, $startDate, $endDate)
    {
        $start = date('Y-m-d', strtotime($startDate));
        $end = date('Y-m-d', strtotime($endDate));
        $this->where(['ab_user' => $user, 'ab_jenis' => $jenis]);
        $this->where('created_at >=', $start . ' 00:00:00');
        $this->where('created_at <=', $end . ' 23:59:00');
        $query = $this->findAll();
        return $query ? count($query) : 0;
    }
}
