<?php

namespace App\Controller;

use App\Entity\Compartido;
use App\Entity\Registro;
use App\Utils\Auditor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use \DateTime;

/**
 * Undocumented class
 */
class SaveCompartidoService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;

    }
    /**
     * save function
     *
     * @param string $request
     *
     * @return Compartido
     */
    public function save(Request $request, $kernel)
    {
        $usuario = $this->tokenStorage->getToken()->getUser();
        $data = json_decode($request->getContent());
        $dataRegistro = explode("/", $data->{"registro"});
        $registro = $this->em->getRepository(Registro::class)->findOneById($dataRegistro[2]);
        //Guardado de la entidad
        $compartido = new Compartido();
        $cuando = new DateTime($data->{"cuando"});
        $registroRepository = $this->em->getRepository(Registro::class);
        $compartido->setCuando($cuando);
        $compartido->setPara($data->{"para"});
        $compartido->setAsunto($data->{"asunto"});
        $compartido->setContenido($data->{"contenido"});
        $compartido->setDescripcionAdjuntos($data->{"descripcionAdjuntos"});
        $compartido->setRegistro($registro);
        $compartido->setEstadoId($data->{"estadoId"});
        $this->em->persist($compartido);
        $this->em->flush();

        if ($_ENV["COMMAND_FROM_CONTROLLER"] == "true") {
            $application = new Application($kernel);
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
            ->setParameter('adjuntos', json_decode($data->{"descripcionAdjuntos"}))
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
            "Asunto" => $data->{"asunto"},
            "Adjuntos" => $archivoAdjuntos,
            "Fecha" => $cuando->format("Y-m-d H:i:s"),
        );
        Auditor::registerAction($this->em, $registro, $usuario, null, $valor_actual, "RADICADO COMPARTIDO");
        return $compartido;

    }
}
