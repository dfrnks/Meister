<?php

namespace src\teste\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Users
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    private $id;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $nome;

    /**
     * @MongoDB\String
     * @MongoDB\Index(unique=true)
     * @Assert\NotBlank()
     * @Assert\Email(
            message="email {{ value }} no valido"
     * )
     */
    private $email;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @MongoDB\Boolean
     * @Assert\NotBlank()
     */
    private $ativo;

    /**
     * @MongoDB\Collection
     * @Assert\NotBlank()
     * [ 0 => DEV, 1 => ADM, 2 => WS, 3 => USR, 4 => WEB ]
     */
    private $rules = array();

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
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param $nome
     * @return $this
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param mixed $roles
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
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