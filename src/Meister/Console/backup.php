<?php

/**
 * @link http://symfony.com/doc/current/components/console/introduction.html
 */
namespace  Meister\Meister\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class backup extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('Meister:mongo:backup')
            ->setDescription('Backup do Mongo DB')
            ->setHelp('Faz backup e compacta backups antigos os bancos do MongoDB')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('Iniciando backup');

        $bkp_dir = "/var/doucks/backup/" . date('Y') . "/" . date('m') . "/";
        $tmp_dir = "/tmp/backup_doucks_temp";
        $bkp_name = "mongo_bkp_" . date('d_H_i') . ".tar";

        exec("mongodump -o {$tmp_dir}");

        if (!file_exists($bkp_dir)) {
            mkdir($bkp_dir, 0777, true);
        }

        exec("cd {$tmp_dir}; tar -czf {$bkp_dir}{$bkp_name} * 2> /dev/null");
        exec("rm -rf {$tmp_dir}");

        $output->writeln("Backup finalizado");

        /****************************************/
        /** Compactando backups do mes passado **/
        /****************************************/
        $output->writeln('Compactando backups');

        $mes_before = str_pad(date('m') - 1, 2 , "0", STR_PAD_LEFT);
        $bkp_dir = "/var/doucks/backup/" . date('Y') . "/" . $mes_before . "/";

        if(file_exists($bkp_dir)){
            $bkps_dir = "/var/doucks/backup/" . date('Y') . "/month/";
            $bkp_name = "mongo_bkp_month" . $mes_before . ".tar";

            if (!file_exists($bkps_dir)) {
                mkdir($bkps_dir, 0777, true);
            }

            exec("cd {$bkp_dir}; tar -czf {$bkps_dir}{$bkp_name} * 2> /dev/null");
            exec("rm -rf {$bkp_dir}");
        }

        $output->writeln("Compactação finalizado");
    }
}