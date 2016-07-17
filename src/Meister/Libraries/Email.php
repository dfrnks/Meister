<?php

namespace Meister\Meister\Libraries;

use Meister\Meister\Interfaces\DatabaseInterface;
use Pimple\Container;

class Email{

    private $db;

    private $app;

    public function __construct(Container $app, DatabaseInterface $db){
        $this->app = $app;
        $this->db  = $db;
    }

    public function sendMail($to, $subject, $message, $now = false, $from = null){
        
        $enviado = false;

        if($now){
            $enviado = $this->send($to, $subject, $message, $from);
        }

        if(!$from){
            $conf = $this->app['config']['mail'];

            if(!$from){
                $from = $conf['from'];
            }
        }

        $mail["to"]         = $to;
        $mail["from"]       = $from;
        $mail["subject"]    = $subject;
        $mail["message"]    = $message;
        $mail["enviado"]    = $enviado;

        if($enviado){
            $mail["dataenvio"] = new \DateTime();
        }

        $this->db->insert(new \Meister\Meister\Document\Emails(),$mail);

        return true;

    }

    public function send($to, $subject, $message, $from = null){

        $conf = $this->app['config']['mail'];
        
        if(!$from){
            $from = $conf['from'];
        }

        if(!$conf['smtp']){
            return true;
        }else {
            $transport = \Swift_SmtpTransport::newInstance($conf['host'], $conf['port'])
                ->setUsername($conf['user'])
                ->setPassword($conf['pass']);

            $mailer = \Swift_Mailer::newInstance($transport);

            $mail = \Swift_Message::newInstance()
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setBody(
//                    $this->renderView(
//                        'AppBundle:emails:register.html.twig', [ 'hash' => $hash ]
//                    ),
                    $message, 'text/html'
                );

            return $mailer->send($mail);
        }
    }
}