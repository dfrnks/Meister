<?php

/**
 * @link http://symfony.com/doc/current/components/console/introduction.html
 */
namespace Meister\Meister\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class hello extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('hello')
            ->setDescription('Exemplo de utilização')
            ->setHelp('Aqui vai o help')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Seu nome caso deseje saber'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'Se setar isso, a saida vai ser em caixa alta, tente'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

        $output->writeln($text);
    }
}

//$app
//    ->register('hello')
//    ->setDefinition(array(
//        new InputArgument('nome',InputArgument::REQUIRED,'Nome de usuário.')
//    ))
//    ->setDescription('Função que mostra um Hello Wordl para um usuário.')
//    ->setHelp('
//        O comando <info>hello</info> exige o argumento <info>nome</info>.
//        Exemplos:
//        <comment>php app.php hello Lukas</comment>
//    ')
//    ->setCode(function (InputInterface $input, OutputInterface $output){
//        $nome = $input->getArgument('nome');
//        $output->writeln('Hello '.$nome.'.');
//    });