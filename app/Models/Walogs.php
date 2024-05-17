<?php

namespace App\Models;

use CodeIgniter\Model;

class Walogs extends Model
{
    protected $table         = 'walogs';
    protected $primaryKey    = 'wa_id';
    protected $allowedFields = ['wa_number', 'wa_message', 'wa_file', 'wa_latitude', 'wa_longitude', 'wa_status'];
    protected $useTimestamps = true;
}
