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

    public function redirect($URL, $data, Session $session){

        $uid = Data::uid();

        $cdata = Crypt::open_encrypt($data);

        $session->set($uid,$cdata);

        header('Location: '. $URL . "/" . $uid);
    }

    public function getRedirect($app,Session $session){

        $cdata = $session->get($app['params']['uid']);

        $session->remove($app['params']['uid']);

        return Crypt::decryptOpenssl($cdata);
    }
}