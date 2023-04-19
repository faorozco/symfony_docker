<?php

namespace App\Controller;

use App\Entity\Notificacion;
use App\Entity\Registro;
use App\Entity\Usuario;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class SaveNotificacionService
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
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function save(Request $request, $kernel)
    {
        $data = json_decode($request->getContent());
        $dataRegistro = explode("/", $data->{"registro"});
        //Se verifica a que usuarios se les notificó el registro
        $notificacionRepository = $this->em->getRepository(Notificacion::class);
        $sentNotifications = $notificacionRepository->findBy(array("registro_id" => $dataRegistro[2]));
        $notificationsArray = array();
        foreach ($sentNotifications as $sentNotification) {
            $notificacionArray = json_decode($sentNotification->getPara());
            $notificationsArray = array_merge($notificationsArray, $notificacionArray);
        }
        $notificados = array_unique($notificationsArray);
        $aNotificar = json_decode($data->{"para"});
        $para = $aNotificar;
        //$diffNotificados = array_diff($aNotificar, $notificados);
        //$para = array();
        /*foreach($diffNotificados as $diffNotificado) {
            $para[] = $diffNotificado;
        }*/
        //Guardado de la entidad
        if (count($para) > 0) {
            $notificacion = new Notificacion();
            $registroRepository = $this->em->getRepository(Registro::class);
            $notificacion->setContenido($data->{"contenido"});
            $notificacion->setEstadoId($data->{"estadoId"});
            $notificacion->setNotificado($data->{"notificado"});
            $notificacion->setPara(json_encode($para));
            $notificacion->setCuando(new DateTime());
            $notificacion->setRegistro($registroRepository->findOneById($dataRegistro[2]));
            $this->em->persist($notificacion);
            $this->em->flush();
            if ($_ENV["COMMAND_FROM_CONTROLLER"] == "true") {            
                $application = new Application($kernel);
                $application->setAutoExit(false);

                $input = new ArrayInput([
                    'command' => 'gdocument:notify',
                ]);

                $output = new NullOutput();
                $application->run($input, $output);
            }
            return $notificacion;
        } else {
            return array("response" => array("message" => "Los usuarios seleccionados ya fueron notificados"));
        }

    }
}
