<?php

namespace src\teste\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Session{

    /**
     * @MongoDB\Id(strategy="auto")
     */
    private $id;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $browser;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $uid;
    
    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @MongoDB\Date
     */
    private $data;

    public function __construct(){
        $this->data = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getBrowser(){
        return $this->browser;
    }

    /**
     * @param $browser
     * @return $this
     */
    public function setBrowser($browser){
        $this->browser = $browser;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUid(){
        return $this->uid;
    }

    /**
     * @param $uid
     * @return $this
     */
    public function setUid($uid){
        $this->uid = $uid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $user
     * @return $this
     */
    public function setUser($user){
        $this->user = $user;
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
    public function setData($data){
        $this->data = $data;
        return $this;
    }

}