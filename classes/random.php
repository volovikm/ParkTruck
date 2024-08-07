<?php

class Random 
{
    public function randomString($length) 
    {
        $chars = "ABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
                    $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
    }

    public function randomNumberString($length) 
    {
        $chars = "0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
                    $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
    }

    public function randomLetterString($length) 
    {
        $chars = "ABCDEFGHIJKLMNOPRQSTUVWXYZ";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
                    $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
    }
}

?>