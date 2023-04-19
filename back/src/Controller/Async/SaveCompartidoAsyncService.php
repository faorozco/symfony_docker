<?php

namespace App\Controller\Async;

use App\Entity\Compartido;
use App\Entity\Registro;
use App\Utils\Auditor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\HttpKernel\KernelInterface;
use \DateTime;

/**
 * Undocumented class
 */
class SaveCompartidoAsyncService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, KernelInterface $kernel)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->kernel = $kernel;

    }


    public function  saveEmail($destinatarios, $envioFecha, $asunto, $contenido, $Adjuntos, $typeNotificacion, $registro)
    {
        $usuario = $this->tokenStorage->getToken()->getUser();
        $compartido = new Compartido();
        $cuando = new DateTime($envioFecha);
        $registroRepository = $this->em->getRepository(Registro::class);
        $compartido->setCuando($cuando);
        $compartido->setPara($destinatarios);
        $compartido->setAsunto($asunto);
        $compartido->setContenido($contenido);
        $compartido->setRegistro($registro);
        $compartido->setDescripcionAdjuntos($Adjuntos);
        $compartido->setEstadoId(1);
        $compartido->setTipoNotificacion($typeNotificacion);
        $this->em->persist($compartido);
        $this->em->flush();

        if ($_ENV["COMMAND_FROM_CONTROLLER"] == "true") {
            $application = new Application($this->kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput([
                'command' => 'gdocument:send-mail',
            ]);

            $output = new NullOutput();
            $application->run($input, $output);
        }
        $queryBuilder = $this->em->createQueryBuilder();
        $adjuntos = $queryBuilder
            ->select('a.nombre')
            ->from('App\\Entity\\Archivo', 'a')
            ->where("a.id IN (:adjuntos)")
            ->setParameter('adjuntos', json_decode($Adjuntos))
            ->getQuery()
            ->execute();
        $archivoAdjuntosArray = array();
        foreach ($adjuntos as $adjunto) {
            $archivoAdjuntosArray[] = $adjunto["nombre"];
        }        
        $archivoAdjuntos=implode(" ",$archivoAdjuntosArray);
        //Registro Auditoria
        $valor_actual = array(
            "Radicado" => $registro->getId(),
            "Asunto" => $asunto,
            "Adjuntos" => $archivoAdjuntos,
            "Fecha" => $cuando->format("Y-m-d H:i:s"),
        );
        Auditor::registerAction($this->em, $registro, $usuario, null, $valor_actual, $typeNotificacion);
        return $compartido;

    }


    public function save(Request $request){
        return array(["response" => "true"]);
    }


}
