<?php

namespace App\Libraries;

class WhatsApp
{

    protected $_server = 'https://wa.operasionalabm.site'; // IP SERVER WHATSAPP WEB

    public function auth()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->_server . '/auth/getqr',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    public function logout()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->_server . '/auth/logout',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }


    public function isregistered($number)
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/contact/isregistereduser/' . $number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }

    // contact
    public function contacts()
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/contact/getcontacts',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }

    public function contact($number)
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/contact/getcontact/' . $number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }

    public function profilepicture($number)
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/contact/getprofilepic/' . $number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }

    // chat
    public function sendmessage($number, $message)
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/chat/sendmessage/' . $number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'message=' . $message,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }

    public function sendmedia($number, $fileUrl, $caption = '')
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/chat/sendmedia/' . $number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'file=' . $fileUrl . '&caption=' . $caption,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }

    public function sendlocation($number, $latitude, $longitude, $description = '')
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/chat/sendlocation/' . $number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'latitude=' . $latitude . '&longitude=' . $longitude . '&description=' . $description,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }

    // group
    public function groupsendmessage($groupName, $message)
    {
        $walogs = new \App\Models\Walogs();

        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/group/sendmessage/' . urlencode($groupName),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'message=' . $message,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            $walogs->insert(['wa_number' => $groupName, 'wa_message' => $message, 'wa_status' => 1]);
            return json_decode($response, true);
        } else {
            $walogs->insert(['wa_number' => $groupName, 'wa_message' => $message, 'wa_status' => 0]);
            return $online;
        }
    }

    public function groupsendmedia($groupName, $fileUrl, $caption = '')
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/group/sendmedia/' . urlencode($groupName),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'file=' . $fileUrl . '&caption=' . $caption,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }

    public function groupsendlocation($groupName, $latitude, $longitude, $description = '')
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/group/sendlocation/' . urlencode($groupName),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'latitude=' . $latitude . '&longitude=' . $longitude . '&description=' . $description,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }

    // optional
    public function chats()
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/chat/getchats',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }

    public function chat($number)
    {
        $online = $this->auth();
        if ($online['authenticated']) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->_server . '/chat/getchatbyid/' . $number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } else {
            return $online;
        }
    }
}
