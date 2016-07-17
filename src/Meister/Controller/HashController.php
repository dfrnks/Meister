<?php

namespace Meister\Meister\Controller;

use Meister\Meister\Controller;
use Meister\Meister\Libraries\Curl;

/**
 * @notauthenticated
 * @api
 */
class HashController extends Controller {

    public function check(){
        if(!array_key_exists('token',$this->app['params'])){
            throw new \Exception(_('meister_hash_token_invalido'));
        }

        /**
         * @var $token \Meister\Meister\Document\Hash()
         */
        $token = $this->app['hash']->validaToken($this->app['params']['token']);

        $rota = $token->getRota();

        if(empty($rota)){
            throw new \Exception(_('meister_hash_token_invalido'));
        }

        (new Curl())->post($rota,$token->getJson(),[
            "CURLOPT_FOLLOWLOCATION" => true
        ]);
    }
}