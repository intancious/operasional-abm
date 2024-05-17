<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SendMail
{

    protected $email;

    public function __construct()
    {
        $this->email = new PHPMailer();
    }

    public function reset($query)
    {
        $settingModel = new \App\Models\SettingModel();
        $setting = $settingModel->find(1);

        try {
            $this->email->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->email->isSMTP();
            $this->email->Host       = 'mail.richvillagejember.com';
            $this->email->SMTPAuth   = true;
            $this->email->Username   = 'noreply@richvillagejember.com';
            $this->email->Password   = '2TbT!z[L]5{[';
            $this->email->SMTPSecure = 'ssl';
            $this->email->Port       = 465;

            $this->email->setFrom('noreply@richvillagejember.com', $setting['setting_nama']);
            $this->email->addAddress($query['usr_email']);
            $this->email->addReplyTo('admin@richvillagejember.com', $setting['setting_nama']);

            $this->email->isHTML(true);
            $this->email->Subject = 'Konfirmasi Lupa Password';
            $body = view('mail/reset', ['data' => $query]);
            $this->email->Body = $body;

            return $this->email->send();
        } catch (Exception $e) {
            return $this->email->ErrorInfo;
        }
    }
}
