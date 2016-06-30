<?php

namespace Meister\Meister\Libraries;

/**
 * Class Crypt
 * @package libs
 */
class Crypt
{
    /**
     * @var int
     */
    protected static $_saltLength = 22;


//
//$key = getKey(gpass('minha senha','a6ec59589472398a9aaa520eab3b3332'),'a9e01sdf85c4210362b35b436034be0207e5');
//
//$payload = "h";
//
//// ENCRYPTION
//$garble = mycrypt_encrypt($key, $payload);
//
//// DECRYPTION
//$end_result = mycrypt_decrypt($key, $garble);

    /**
     * gera hash SHA-512
     * @param $string
     * @param int $cost
     * @return string
     */
    public static function gpass($string,$salt = null,$cost = 640000){
        if(empty($salt)){
            $salt = self::generateRandomSalt();
        }
        return crypt($string,'$6$rounds='.$cost . '$'. $salt);
    }

    /**
     * Check hahs com string
     * @param $string
     * @param $hash
     * @return bool
     */
    public static function cpass($string, $hash) {
        return (crypt($string, $hash) === $hash);
    }

    /**
     * @return string
     */
    public static function generateRandomSalt() {
        $seed = uniqid(mt_rand(), true);

        $salt = base64_encode($seed);
        $salt = str_replace('+', '.', $salt);
        return substr($salt, 0, self::$_saltLength);
    }

    /**
     * @param $key
     * @param $payload
     * @return string
     */
    public static function mycrypt_encrypt ($key, $payload) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM);
        $crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, base64_encode($payload), MCRYPT_MODE_CBC, $iv);
        $garble = base64_encode($iv . $crypt);
        return $garble;
    }

    /**
     * @param $key
     * @param $garble
     * @return string
     */
    public static function mycrypt_decrypt ($key, $garble) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $combo = base64_decode($garble);
        $iv = substr($combo, 0, $iv_size);
        $crypt = substr($combo, $iv_size, strlen($combo));
        $payload = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypt, MCRYPT_MODE_CBC, $iv);
        return base64_decode($payload);
    }

    /**
     * @param $d
     * @param string $keySecret
     * @param string $ivSecret
     * @return string
     */
    public static function open_encrypt($d,$keySecret="",$ivSecret=""){

        if(empty($keySecret)){
            $keySecret = '6,sy~n.T!d;/ s[P`d03\sDQ+pxgA\?:?I%q}\Ssn\|_\_li-{{7Zn|u!9<6;3%=GGl](m)E,L3r"=(rs;F1u^RU~]0fqgkK3G8eyi>%B_1/O/C+*0`S`{>`shC1scbQ';
        }

        if(empty($ivSecret)){
            $ivSecret = '2^>E 8.!\im7zXp;< Bt[28\F_Z>5\"4yWe\yVB9m6R,oyG;&(953CLGk]Z\<4U=zc}`^})C.N7]368mk}2_qSa/m,z5{wE?tj4AK*EBbeUnlD`er]}Nte"bcfg1wf50w';
        }

        $key = hash('sha512', $keySecret );
        $iv = substr(hash('sha512', $ivSecret ), 0, 16);

        return base64_encode(openssl_encrypt(json_encode($d), "AES-256-CTR", $key, 0, $iv));
    }

    /**
     * @param $d
     * @param string $keySecret
     * @param string $ivSecret
     * @return array
     */
    public static function decryptOpenssl($d,$keySecret="",$ivSecret=""){
        if(empty($keySecret)){
            $keySecret = '6,sy~n.T!d;/ s[P`d03\sDQ+pxgA\?:?I%q}\Ssn\|_\_li-{{7Zn|u!9<6;3%=GGl](m)E,L3r"=(rs;F1u^RU~]0fqgkK3G8eyi>%B_1/O/C+*0`S`{>`shC1scbQ';
        }

        if(empty($ivSecret)){
            $ivSecret = '2^>E 8.!\im7zXp;< Bt[28\F_Z>5\"4yWe\yVB9m6R,oyG;&(953CLGk]Z\<4U=zc}`^})C.N7]368mk}2_qSa/m,z5{wE?tj4AK*EBbeUnlD`er]}Nte"bcfg1wf50w';
        }

        $key = hash('sha512', $keySecret );
        $iv = substr(hash('sha512', $ivSecret ), 0, 16);

        return (array) json_decode(openssl_decrypt(base64_decode($d), "AES-256-CTR", $key, 0, $iv));
    }

    /**
     * @param $publicKey
     * @param $privateKey
     * @return string
     */
    public static function getUniqueKey($publicKey,$privateKey) {

        $privateKey = hash('sha512', self::open_encrypt($privateKey,$publicKey));
        $publicKey  = hash('sha512', self::open_encrypt($publicKey,$privateKey));

        $seed = hash('sha512',$privateKey . $publicKey);

        return substr(str_replace('+', '.', $seed), 0, 32);
    }

}