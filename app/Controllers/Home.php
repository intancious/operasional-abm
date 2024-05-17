<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function checkwa()
    {
        $request = \Config\Services::request();
        $number = $request->getVar('number');
        $message = $request->getVar('message');

        $whatsApp =  new \App\Libraries\WhatsApp();
        $response = $whatsApp->groupsendmessage($number, $message);
        
        if ($response) {
            return json_encode(['status' => true, 'msg' => 'Berhasil terkirim.']);
        } else {
            return json_encode(['status' => false, 'msg' => 'Gagal terkirim.']);
        }
    }
}
