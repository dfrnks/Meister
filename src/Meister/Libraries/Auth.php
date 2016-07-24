<?php

namespace Meister\Meister\Libraries;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha512;
use Lcobucci\JWT\ValidationData;
use Meister\Meister\Document\Sessoes;
use Meister\Meister\Interfaces\DatabaseInterface;
use Pimple\Container;

class Auth {

    private $private = "5n)PE`X6@,2=EUZ{b(YF~IqV?w/+Yc btcm{nsvF`xpkf~JsISit]=4?Xl#1oT}F";

    private $app;

    private $db;

    private $config;

    private $session;

    private $entity;

    private $SessionEntity;

    public function __construct(Container $app, DatabaseInterface $db, array $config, Session $session){
        $this->app     = $app;
        $this->db      = $db;
        $this->config  = $config;
        $this->session = $session;
        $this->entity  = $this->config['auth']['entity'];

        $this->SessionEntity = new Sessoes();
    }

    /**
     * @param $_username
     * @param $_password
     * @return bool
     * @throws \Exception
     */
    public function login($_username,$_password) {

        $fiedlUser = $this->config["auth"]["field"];

        if(empty($fiedlUser)){
            $fiedlUser = "username";
        }

        $pes = $this->db->doc()->getRepository($this->entity)->findAll();

        if(empty($pes)){
            $this->db->insert(new $this->entity(),[
                "nome"     => "Admin",
                $fiedlUser => "admin@mail.com",
                "password" => Crypt::gpass("admin"),
                "ativo"    => true,
                "rules"    => ["DEV","ADM"]
            ]);

            $pessoa = $this->db->doc()->getRepository($this->entity)->findOneBy([
                $fiedlUser => "admin@mail.com"
            ]);
        }else{
            $pessoa = $this->db->doc()->getRepository($this->entity)->findOneBy([
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

        $fiedlUser = 'get'.ucfirst($this->config["auth"]["field"]);

        $pes = Data::serialize($pessoa);

        $this->session->set('idusuario', $pessoa->getId());
        $this->session->set('username',  $pessoa->$fiedlUser());
        $this->session->set('User',      $pes);
        $this->session->set('Permission',$pessoa->getRules());
    }

    public function logout() {
        $this->session->destroy();
    }

    public function isLogged() {
        if($this->session->exist('uid')) {
            return true;
        }

        return false;
    }

    public function checkRules($permission){
        $rules = [];

        if($this->session->exist('Permission')){
            $rules = $this->session->get('Permission');
            if(empty($rules)){
                $rules = [];
            }
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

    public function getUser($fild=null) {
        $user = $this->session->get('User');

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
            ->setExpiration(time() + $this->config['session']['timetoken'])
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

        $this->db->insert(new $this->SessionEntity(),[
            "user"      => $id,
            "uid"       => $uid,
            "browser"   => $_SERVER['HTTP_USER_AGENT']
        ]);

        $this->session->set('uid',$uid);

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

        $sessao = $this->db->doc()->getRepository($this->SessionEntity)->findOneBy(['uid' => $uid]);

        if(empty($sessao)){
            throw new \Exception("Session not found",403);
        }

        if($tkon->getClaim('sys') != md5($sessao->getBrowser())){
            sleep(5);

            throw new \Exception("Credentials incorrect",403);
        }

        $this->session->set('uid',$uid);

        return [
            "cod"   => base64_decode(Crypt::mycrypt_decrypt(md5(file_get_contents($fprikey)),$tkon->getClaim('cod'))),
            "id"    => $tkon->getClaim('id'),
            "uid"   => $uid
        ];
    }

    public function forgotPass($_username){

        // Envia email com alguma coisa para ele.

        $fiedlUser = $this->config["auth"]["field"];

        if(empty($fiedlUser)){
            $fiedlUser = "username";
        }

        $pessoa = $this->db->doc()->getRepository($this->entity)->findOneBy([
            $fiedlUser => $_username
        ]);

        if(!$pessoa){
            throw new \Exception(_('meister_auth_pessoa_nao_encontrada'));
        }

        $pessoa = Data::serialize($pessoa);

        $validade = (new \DateTime())->add(new \DateInterval('PT24H'));

        $token = $this->app['hash']->getToken([
            "id" => $pessoa['id']
        ],$this->app['WEB_LINK'].$this->config['auth']['timetoken'], $validade, true, $pessoa);

        return $this->app['mail']->sendMail($pessoa['email'],'Token',$_SERVER['HTTP_HOST'].$this->app['WEB_LINK'].'hash/'.$token,true);

    }

    public function recoverPass($_username,$_password){
        // Versão dois
        // pegas a senha nova e envia um email com um codigo de segurança para validar.

        $fiedlUser = $this->config["auth"]["field"];

        if(empty($fiedlUser)){
            $fiedlUser = "username";
        }

        $pessoa = $this->db->doc()->getRepository($this->entity)->findOneBy([
            $fiedlUser => $_username
        ]);

        if(!$pessoa){
            throw new \Exception(_('meister_auth_pessoa_nao_encontrada'));
        }

        $this->db->update($pessoa,[
            "password" => Crypt::gpass($_password)
        ]);

        return true;
    }
}