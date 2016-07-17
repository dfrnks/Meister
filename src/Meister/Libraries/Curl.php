<?php

namespace Meister\Meister\Libraries;

class Curl{

    public function post($URL,array $data,$opt = []){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen(json_encode($data))));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if(array_key_exists('CURLOPT_FOLLOWLOCATION',$opt) && $opt['CURLOPT_FOLLOWLOCATION']){
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        $server_output = curl_exec($ch);

        curl_close($ch);
        
        return json_decode($server_output);
    }

    public function redirect($URL, $data){

        $uid = Data::uid();

        $cdata = Crypt::open_encrypt($data);

        setcookie($uid,$cdata);

        header('Location: '. $URL . "?noid=" . $uid);
    }

    public function getRedirect(){

        $uid = $_REQUEST['noid'];

        $cdata = $_COOKIE[$uid];
        unset($_COOKIE[$uid]);

        return Crypt::decryptOpenssl($cdata);
    }
}