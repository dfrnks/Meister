<?php

/**
 * @link http://symfony.com/doc/current/components/console/introduction.html
 */
namespace  Meister\Meister\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class translate extends Command
{
    
    protected function configure()
    {
        $this
            ->setName('Meister:translate')
            ->setDescription('Mapear as traduções do arquivos PHP e Templates Twig')
            ->setHelp('')
            ->addOption(
                'mapear',
                null,
                InputOption::VALUE_NONE,
                'Ira mapear todas as traduções'
            )
            ->addOption(
                'compile',
                null,
                InputOption::VALUE_NONE,
                'Ira compilar todas as traduções'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if ($input->getOption('mapear')) {
            $output->writeln('<comment>Iniciando mapeamento</comment>');

            # Compila todos os templates
            $command = $this->getApplication()->find('Meister:twig:compile');
            $command->run(new ArrayInput([]), $output);


            # Mapear arquivos PHP
			$output->writeln('<comment>Mapeando arquivos</comment>');
            exec('find ./ -type f -name \*.php > /tmp/list-files-code-translate.txt');
            exec('xgettext --default-domain=messages -p ./i18n --from-code=UTF-8 -L PHP -f /tmp/list-files-code-translate.txt');
            exec('rm -f /tmp/list-files-code-translate.txt');

			$output->writeln('<comment>Gerando arquivos</comment>');
            $m = file_get_contents('i18n/messages.po');

            $m = str_replace('"Content-Type: text/plain; charset=CHARSET\n"','"Content-Type: text/plain; charset=UTF-8\n"',$m);

            file_put_contents('i18n/messages.po',$m);

            foreach (new \DirectoryIterator('i18n') as $fileInfo) {
                if($fileInfo->isDot()) continue;

                if($fileInfo->isDir()){
                    $d = $fileInfo->getFilename();

//                    exec('mv i18n/'.$d.'/LC_MESSAGES/messages.po i18n/'.$d.'/LC_MESSAGES/messages.old.po');

                    exec('msginit -l '. $d .' --no-wrap --no-translator -o i18n/'.$d.'/LC_MESSAGES/messages.new.po -i i18n/messages.po');

//					exec('msgcat --use-first i18n/'.$d.'/LC_MESSAGES/messages.po i18n/'.$d.'/LC_MESSAGES/messages.old.po');

					exec('msgmerge --backup=none -U i18n/'.$d.'/LC_MESSAGES/messages.po i18n/'.$d.'/LC_MESSAGES/messages.new.po');

					exec('rm -f i18n/'.$d.'/LC_MESSAGES/messages.new.po');
                }
            }


            exec('rm -f i18n/messages.po');

//        exec('msginit -l pt_BR --no-wrap --no-translator -o i18n/pt_BR/LC_MESSAGES/hello_multi_world.po -i i18n/hello_multi_world.pot');

//        xgettext -L PHP --from-code=UTF-8 --no-wrap -d hello_multi_world -o hello_multi_world.pot -f $ARQUIVO_TMP

//        xgettext --default-domain=messages -p ./locale --from-code=UTF-8 -n --omit-header -L PHP -f $ARQUIVO_TMP

            $output->writeln("<comment>Finalizando mapeamento</comment>");
        }


        if ($input->getOption('compile')) {
            $output->writeln('<comment>Iniciando</comment>');

            foreach (new \DirectoryIterator('i18n') as $fileInfo) {
                if($fileInfo->isDot()) continue;

                if($fileInfo->isDir()){
                    $d = $fileInfo->getFilename();

                    // msgfmt messages.po
                    exec('msgfmt i18n/'.$d.'/LC_MESSAGES/messages.po -o i18n/'.$d.'/LC_MESSAGES/messages.mo');
                }
            }

            $output->writeln("<comment>Finalizando</comment>");

        }
    }
}