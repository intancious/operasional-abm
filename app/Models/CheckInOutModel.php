<?php

namespace App\Models;

use CodeIgniter\Model;

class CheckInOutModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'check_inout';
    protected $primaryKey           = 'ci_id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDelete        = false;
    protected $protectFields        = true;
    protected $allowedFields        = ['ci_user', 'ci_latitude', 'ci_longitude', 'ci_time', 'ci_type', 'ci_deskripsi', 'ci_telat', 'ci_punishment'];

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

    public function checkJikaSudahAbsen($type)
    {
        $this->where(['ci_user' => session()->get('usr_id'), 'ci_type' => $type]);
        $this->like('created_at', date('Y-m-d'));
        return $this->first();
    }

    public function getKehadiran($type, $user, $tgl)
    {
        $this->where(['ci_user' => $user, 'ci_type' => $type]);
        $this->like('created_at', $tgl);
        return $this->first();
    }

    public function countHadir($user, $startDate, $endDate, $telat = false)
    {
        $start = date('Y-m-d', strtotime($startDate)) . ' 00:00:00';
        $end = date('Y-m-d', strtotime($endDate)) . ' 23:59:59';

        if ($telat) {
            $this->where(['ci_telat' => $telat]);
        }
        $this->where(['ci_user' => $user, 'ci_type' => 'in', 'created_at >=' => $start, 'created_at <=' => $end]);
        $this->groupBy('SUBSTR(created_at, 1, 10)');
        $query = $this->countAllResults();
        return $query;
    }
}
