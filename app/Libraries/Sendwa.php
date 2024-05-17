<?php

namespace App\Libraries;

class Sendwa
{
    protected $groupName = 'PT. ALVIN BHAKTI MANDIRI';

    public function sendNotifAbsen($usrId, $time, $check, $desk)
    {
        $userModel = new \App\Models\UserModel();
        $usr = $userModel->find($usrId);
        if ($usr) {

            if ($check == 'in') {
                $message = "*" . $usr['usr_nama'] . "* hadir pada *" . date('d/m/Y H:i:s', strtotime($time)) . "*.";
            }

            if ($check == 'out') {
                $message = "Terimakasih kepada *" . $usr['usr_nama'] . "* hari ini telah bekerja secara profesional hingga Pukul *" . date('H:i:s', strtotime($time)) . "*.";
            }

            if ($check == 'visit') {
                $message = "";
            }

            $whatsApp =  new \App\Libraries\WhatsApp();
            $response = $whatsApp->groupsendmessage($this->groupName, $message);
            return $response;
        } else {
            return false;
        }
    }

    public function sendNotifTdkHadir($usrId, $time, $absen, $desk)
    {
        $userModel =  new \App\Models\UserModel();
        $usr = $userModel->find($usrId);
        if ($usr) {
            $message = "Hallo semua, kita doakan *" . $usr['usr_nama'] . "*, semoga besok bisa kerja kembali bersama kita semua karena hari ini tidak bisa bekerja dikarenakan _" . $absen . "_. Hal ini telah disampaikan melalui aplikasi pada *" . date('d/m/Y H:i:s', strtotime($time)) . "*.

Yang lainnya tetap semangat ya.. !";

            $whatsApp =  new \App\Libraries\WhatsApp();
            $response = $whatsApp->groupsendmessage($this->groupName, $message);
            return $response;
        } else {
            return false;
        }
    }
}
