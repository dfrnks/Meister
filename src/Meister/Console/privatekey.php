<?php

namespace  Meister\Meister\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class privatekey extends Command{

    protected function configure(){
        $this
            ->setName('Meister:generator:privatekey')
            ->setDescription('Gera a chave privada e publica do site')
            ->setHelp('
                Apenas execute esse comando, 
                caso não tenha sido geradas as chaves, 
                elas serão geradas.
            ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){

        $init = $this->getHelper('init')->getInit();

        $basedir = $init->getBaseDir();
        
        $fprikey = $basedir.'/app/config/key/';
        $fpubkey = $basedir.'/app/config/key/';

        $namePri = "private.pkey";
        $namePub = "public.pkey";

        if(!file_exists($fprikey.$namePri) || !file_exists($fpubkey.$namePub)){

            if(!file_exists($fprikey)){
                mkdir($fprikey,0777,true);
            }

            if(!file_exists($fpubkey)){
                mkdir($fpubkey,0777,true);
            }

            $key = openssl_pkey_new();

            openssl_pkey_export($key, $privatekey);

            $publickey = openssl_pkey_get_details($key);
            $publickey = $publickey["key"];

            file_put_contents($fprikey.$namePri,$privatekey);
            file_put_contents($fpubkey.$namePub,$publickey);

            $output->writeln("Chaves geradas com sucesso!");
        }else{
            $output->writeln("Chaves já existem!");
        }
    }
}