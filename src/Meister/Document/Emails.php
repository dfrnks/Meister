<?php

namespace Meister\Meister\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Emails{

    /**
     * @MongoDB\Id(strategy="auto")
     */
    private $id;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $to;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $from;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $subject;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $message;
    
    /**
     * @MongoDB\Boolean
     * @Assert\NotBlank()
     */
    private $enviado;

    /**
     * @MongoDB\Date
     * @Assert\NotBlank()
     */
    private $dataenvio;

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
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnviado()
    {
        return $this->enviado;
    }

    /**
     * @param $enviado
     * @return $this
     */
    public function setEnviado($enviado)
    {
        $this->enviado = $enviado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataenvio()
    {
        return $this->dataenvio;
    }

    /**
     * @param $dataenviado
     * @return $this
     */
    public function setDataenvio($dataenvio)
    {
        $this->dataenvio = $dataenvio;
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