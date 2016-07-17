<?php

namespace Meister\Meister\Libraries;

use Meister\Meister\Interfaces\DatabaseInterface;
use Pimple\Container;

class Hash{

    private $db;

    private $app;

    public function __construct(Container $app, DatabaseInterface $db){
        $this->app = $app;
        $this->db  = $db;
    }

    public function getToken(array $json, $rota, $validade, $acessounico = false, $pessoa = []){
        $token = Data::uid();

        if($pessoa){
            $token = strstr($pessoa['email'], '@', true) . "@" .  $token;
        }

        $this->db->insert(new \Meister\Meister\Document\Hash(),[
            "token"         => $token,
            "json"          => json_encode($json),
            "rota"          => $rota,
            "datavalidade"  => $validade,
            "acessounico"   => $acessounico,
            "ativo"         => true
        ]);

        return $token;
    }

    public function validaToken($h){
        /**
         * @var $token \Meister\Meister\Document\Hash()
         */
        $token = $this->db->doc()->getRepository(get_class(new \Meister\Meister\Document\Hash()))->findOneBy([
            "token" => $h
        ]);

        if(empty($token)){
            throw new \Exception(_('meister_hash_token_nao_encontrado'));
        }

        if($token->getDatavalidade() < (new \DateTime())){
            throw new \Exception(_('meister_hash_token_expirado'));
        }

        if(!$token->getAtivo()){
            throw new \Exception(_('meister_hash_token_inativo'));
        }

        if($token->getAcessounico()){
            $this->db->update($token,[
                "ativo" => false,
                "acesso" => $token->getAcesso() + 1
            ]);
        }else{
            $this->db->update($token,[
                "acesso" => $token->getAcesso() + 1
            ]);
        }

        return $token;
    }
}