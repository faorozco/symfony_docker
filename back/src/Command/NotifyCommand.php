<?php
namespace App\Command;

use App\Utils\NotifyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('gdocument:notify')
        // the short description shown while running "php bin/console list"
            ->setDescription('Despacha las notificaciones relacionadas en la entidad Notificación.')
        // the full command description shown when running the command with
        // the "--help" option
            ->setHelp('Este comando permite desagregar las notificaciones pendientes.');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sender = new NotifyService($this->entityManager);
        $sender->notify();
        return 0;
    }
}
