<?php
namespace App\Command;

use App\Service\Twig;
use App\Utils\MailerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class SendMailCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager, \Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('gdocument:share')
        // the short description shown while running "php bin/console list"
            ->setDescription('Despacha correos electrónicos relacionados en la entidad Compartido.')
        // the full command description shown when running the command with
        // the "--help" option
            ->setHelp('Este comando permite ejecutar el envío de los mensajes relacionados en la entidad Compartido');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sender = new MailerService($this->entityManager, $this->twig, $this->mailer);
        $sender->send();
        return 0;
    }
}
