<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GarbageCollectorCommand extends Command
{
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        parent::__construct();
    }

    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('gdocument:garbage-collector')
        // the short description shown while running "php bin/console list"
            ->setDescription('Elimina archivos temporales de gDocument.')
        // the full command description shown when running the command with
        // the "--help" option
            ->setHelp('Este comando permite limpiar las carpetas temporales de gDocument');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $dir = $this->params->get('kernel.project_dir')."/public/tmp/";
        $di = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
        $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            $file->isDir() ? rmdir($file) : unlink($file);
        }
    }
}
