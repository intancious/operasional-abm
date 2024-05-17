<?php

namespace App\Libraries;

class CaptchaLib
{

    public function load()
    {
        $min_number = 1;
        $max_number = 20;
        $min_number1 = 1;
        $max_number1 = 10;
        $number1 = mt_rand($min_number, $max_number);
        $number2 = mt_rand($min_number1, $max_number1);
        $data = [
            'number1'   => $number1,
            'number2'   => $number2
        ];
        return $data;
    }

    public function solve($number1, $number2, $result)
    {
        $total = $number1 + $number2;
        if ($result == $total) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
