<?php

namespace Meister\Meister\Libraries;

use app\Kernel;
use Main\app\Document\Emails;

class Email extends Kernel{

    public function sendMail($to, $subject, $message, $now = false, $from = null){
        
        $enviado = false;

        if($now){
            $enviado = $this->send($to, $subject, $message, $from);
        }

        $mail["to"]         = $to;
        $mail["from"]       = $from;
        $mail["subject"]    = $subject;
        $mail["message"]    = $message;
        $mail["enviado"]    = $enviado;

        if($enviado){
            $mail["dataenvio"] = new \DateTime();
        }

        $this->db()->insert(new Emails(),$mail);

    }

    public function send($to, $subject, $message, $from = null){

        $conf = $this->app['config']['mail'];
        
        if(!$from){
            $from = $conf['from'];
        }

        if(!$conf['smtp']){

        }else {
            $transport = \Swift_SmtpTransport::newInstance($conf['smtp'], $conf['port'])
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