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

class twig_compile extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('Meister:twig:compile')
            ->setDescription('Compila templates do twig em php')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('<comment>Iniciando busca de templates</comment>');

        /**
         * @var $app \app\AppInit
         */
        $app = $this->getHelper('init')->getInit();

        $cache = $app->getCache();

        $src = $app->getBaseDir() . "/src";

        $loader = new \Twig_Loader_Filesystem($src);

        $twig = new \Twig_Environment($loader, array(
            'cache' => $cache['twig'],
            'auto_reload' => true
        ));

        $config = $app->config();
        $container = $app->container();

        foreach($config['modules'] as $ap) {
            if(file_exists($container['Modules'].$ap.'/Views'))
                $loader->addPath($container['Modules'].$ap.'/Views', $ap);

            if(file_exists($container['Modules'].$ap.'/Templates'))
                $loader->addPath($container['Modules'].$ap.'/Templates', $ap);
        }

        $twig->addExtension(new \Twig_Extensions_Extension_I18n());

        $d = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src), \RecursiveIteratorIterator::LEAVES_ONLY);

        $Regex2 = new \RegexIterator($d,'/\.html.twig$/i');

        foreach($Regex2 as $file){
            if ($file->isFile()) {
                $twig->loadTemplate(str_replace($src.'/', '', $file));
            }
        }
        #$output->writeln("<comment>Finalizando</comment>");
    }
}