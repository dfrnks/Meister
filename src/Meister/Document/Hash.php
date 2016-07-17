<?php

namespace Meister\Meister\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Hash{

    /**
     * @MongoDB\Id(strategy="auto")
     */
    private $id;

    /**
     * @MongoDB\String
     * @MongoDB\Index(unique=true)
     * @Assert\NotBlank()
     */
    private $token;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $json;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $rota;

    /**
     * @MongoDB\Date
     */
    private $datavalidade;

    /**
     * @MongoDB\Boolean
     * @Assert\NotBlank()
     */
    private $acessounico;

    /**
     * @MongoDB\Boolean
     * @Assert\NotBlank()
     */
    private $ativo;

    /**
     * @MongoDB\Int
     */
    private $acesso;

    /**
     * @MongoDB\Date
     */
    private $data;

    public function __construct()
    {
        $this->data = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param $json
     * @return $this
     */
    public function setJson($json)
    {
        $this->json = $json;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRota()
    {
        return $this->rota;
    }

    /**
     * @param $rota
     * @return $this
     */
    public function setRota($rota)
    {
        $this->rota = $rota;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDatavalidade()
    {
        return $this->datavalidade;
    }

    /**
     * @param $datavalidade
     * @return $this
     */
    public function setDatavalidade($datavalidade)
    {
        $this->datavalidade = $datavalidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAcessounico()
    {
        return $this->acessounico;
    }

    /**
     * @param $acessounico
     * @return $this,
     */
    public function setAcessounico($acessounico)
    {
        $this->acessounico = $acessounico;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param $ativo
     * @return $this
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAcesso()
    {
        return $this->acesso;
    }

    /**
     * @param $acesso
     * @return $this
     */
    public function setAcesso($acesso)
    {
        $this->acesso = $acesso;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getData(){
        return $this->data;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

}