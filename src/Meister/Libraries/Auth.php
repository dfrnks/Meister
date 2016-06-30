<?php

namespace Meister\Meister\Libraries;

use app\libs\Email;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha512;
use Lcobucci\JWT\ValidationData;
use Main\app\Controller\hashController;
use Main\app\Document\Sessoes;
use Main\app\Document\Users;
use Pimple\Container;

class Auth {

    private $entity = "Main\\app\\Document\\Users";

    private $private = "5n)PE`X6@,2=EUZ{b(YF~IqV?w/+Yc btcm{nsvF`xpkf~JsISit]=4?Xl#1oT}F";

    /**
     * @var Mongo
     */
    private $db;

    private $app;

    public function __construct($app){
        $this->db  = $app["db"];
        $this->app = $app;
    }

    /**
     * @param $_username
     * @param $_password
     * @return bool
     * @throws \Exception
     */
    public function login($_username,$_password) {

        $fiedlUser = $this->app["config"]["field_user"];

        if(empty($fiedlUser)){
            $fiedlUser = "username";
        }

        $pes = $this->db->db()->getRepository($this->entity)->findAll();

        if(empty($pes)){
            $this->db->insert(new Users(),[
                "nome" => "Admin",
                $fiedlUser => "admin@mail.com",
                "password" => Crypt::gpass("admin"),
                "ativo" => true,
                "rules" => ["DEV","ADM"]
            ]);

            $pessoa = $this->db->db()->getRepository($this->entity)->findOneBy([
                $fiedlUser => "admin@mail.com"
            ]);
        }else{
            $pessoa = $this->db->db()->getRepository($this->entity)->findOneBy([
                $fiedlUser => $_username
            ]);
        }

        if(!$pessoa) {
            sleep(5);

            throw new \Exception("Credentials incorrect",403);
        }

        if(!Crypt::cpass($_password,$pessoa->getPassword())) {
            sleep(5);

            throw new \Exception("Credentials incorrect",403);
        }

        $this->setSession($pessoa);

        $pass = Crypt::getUniqueKey(Crypt::gpass($_password,$pessoa->getId()),$this->private);

        return [
            "token" => $this->getToken($pessoa->getId(),$pass),
            "cod"   => $pass
        ];
    }

    public function setSession($pessoa){

        $fiedlUser = 'get'.ucfirst($this->app["config"]["field_user"]);

        $pes = Data::serialize($pessoa);

        Session::set('idusuario', $pessoa->getId());
        Session::set('username', $pessoa->$fiedlUser());
        Session::set('User',$pes);
        Session::set('Permission',$pessoa->getRules());
    }

    public static function logout() {
        Session::destroy();
    }

    public static function isLogged() {
//        if(Session::exist('uid')) {
//            return true;
//        }

        return false;
    }

    public static function checkRules($permission){
        $rules = [];

        if(Session::exist('Permission')){
            $rules = Session::get('Permission');
        }

        if(array_search("DEV", $rules) === 0 || array_search("DEV", $rules)){
            return true;
        }

        if(array_search("ADM", $permission) === 0 || array_search("ADM", $permission)){
            if(array_search("DEV", $rules) === 0 || array_search("DEV", $rules) ||
                array_search("ADM", $rules) === 0 || array_search("ADM", $rules)){
                return true;
            }
        }

        foreach ($rules as $rule){
            if(array_search($rule, $permission) === 0 || array_search($rule, $permission)){
                return true;
            }
        }

        return false;
    }

    public static function getUser($fild=null) {
        $user = Session::get('User');

        if($fild){
            return array_key_exists($fild, $user) ? $user[$fild] : null;
        }

        return $user;
    }

    public function getToken($id,$pass) {
        $fprikey = $this->app['BASE_DIR']."/app/config/key/private.pkey";
        $fpubkey = $this->app['BASE_DIR']."/app/config/key/public.pkey";

        if(!file_exists($fpubkey) || !file_exists($fprikey)){
            throw new \Exception('Chaves não configuradas!!!', 500);
        }

        $sign = new Sha512();
        $pkey = new Key("file://".$fprikey);

        $uid = Data::uid();

        $token = (new Builder())
            ->setIssuer($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'])
            ->setIssuedAt(time())
            ->setNotBefore(time() + 1)
            ->setExpiration(time() + $this->app['config']['time_session'])
            ->setId($uid,true)
            ->set('id'  ,$id)
            ->set('sys' ,md5($_SERVER['HTTP_USER_AGENT']))
            ->set('cod' ,Crypt::mycrypt_encrypt(md5(file_get_contents($fprikey)),base64_encode($pass)))
            ->sign($sign,$pkey)
            ->getToken();

        ob_start();
        echo $token;
        $tkn = ob_get_contents();
        ob_end_clean();

        $this->db->insert(new Sessoes(),[
            "user"      => $id,
            "uid"       => $uid,
            "browser"   => $_SERVER['HTTP_USER_AGENT']
        ]);

        Session::set('uid',$uid);

        return $tkn;
    }

    public function checkToken($token){
        $fprikey = $this->app['BASE_DIR']."/app/config/key/private.pkey";
        $fpubkey = $this->app['BASE_DIR']."/app/config/key/public.pkey";

        if(!file_exists($fpubkey) || !file_exists($fprikey)){
            throw new \Exception('Chaves não configuradas!!!', 500);
        }

        $tkon = (new Parser())->parse((string) $token);

        $uid = $tkon->getClaim('jti');

        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST']);
        $data->setId($uid);

        if(!$tkon->validate($data)){
            $this->logout();
            throw new \Exception("Credentials incorrect",403);
        }

        $sign = new Sha512();
        $publicKey = new Key("file://".$fpubkey);

        if(!$tkon->verify($sign, $publicKey)){
            sleep(5);

            throw new \Exception("Credentials incorrect",403);
        }

        $sessao = $this->db->db()->getRepository('Main\app\Document\Sessoes')->findOneBy(['uid' => $uid]);

        if(empty($sessao)){
            throw new \Exception("Session not found",403);
        }

        if($tkon->getClaim('sys') != md5($sessao->getBrowser())){
            sleep(5);

            throw new \Exception("Credentials incorrect",403);
        }

        return [
            "cod"   => base64_decode(Crypt::mycrypt_decrypt(md5(file_get_contents($fprikey)),$tkon->getClaim('cod'))),
            "id"    => $tkon->getClaim('id'),
            "uid"   => $uid
        ];
    }

    public function forgotPass($_username){

        // Envia email com alguma coisa para ele.

        $fiedlUser = $this->app["config"]["field_user"];
        $fiedlUser = $this->app["config"]["field_user"];

        if(empty($fiedlUser)){
            $fiedlUser = "username";
        }

        $pessoa = $this->db->db()->getRepository($this->entity)->findOneBy([
            $fiedlUser => $_username
        ]);

        $pessoa = Data::serialize($pessoa);

        $validade = (new \DateTime())->add(new \DateInterval('PT24H'));

        $token = hashController::getSToken([
            "id" => $pessoa['id']
        ],$this->app['WEB_LINK'].'api/auth/recover',$validade,true,$pessoa);

        $e = new Email($this->app);

        $e->sendMail($pessoa['email'],'Token',$this->app['WEB_LINK'].'api/hash/check/token/'.$token,true);

        return true;
    }

    public function recoverPass($_username,$_password){
        // pegas a senha nova e envia um email com um codigo de segurança para validar.
    }
}