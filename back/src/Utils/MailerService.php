<?php

namespace App\Utils;

use App\Entity\Archivo;
use App\Entity\Compartido;
use App\Entity\Enviado;
use App\Utils\Gdrive;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class MailerService
{
    private $_em;
    private $_templating;
    private $_mailer;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct($entityManager, $templating, $mailer)
    {
        $this->em = $entityManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function send()
    {
        //como esta tarea puede traer muchos registros para despachar,
        //la estrategia a utilizar  para hacer el envío sera la siguiente:
        //Primero consultamos si tenemos algo para enviar en la entidad Enviado
        // Si tenemos algo para enviar se consultan los primeros 50 registros de la entidad compartido que cumplan
        // con la condición de no haber sido todavia despachados.
        // se ordenan de manera ascendente por id para que asi nunca se queden correos sin despachar
        // si otro usuario llega a ejecutar el proceso de Compartir
        $pendientesEnviar = $this->em->getRepository(Enviado::class)->findToSend();
        if (empty($pendientesEnviar)) {
            //Si no tenemos nada para enviar
            //Consultamos el siguiente registro Compartido pendiente por enviar.
            //Esta consulta se hace a la entidad Compartido
            //De ahi tomamos solo 1 de los refistros que se deben compartir y
            $compartido = $this->em->getRepository(Compartido::class)->findOneBy(
                array('estado_id' => '1'),
                array('id' => 'ASC')
            );  
            // luego se guarda en la entidad Enviado desglosado por los destinatarios.
            // Nota: Los identificadores de archivos a enviar se consultan a través
            // de la relación con Compartido
            if (null !== $compartido) {
                $destinatarios = json_decode($compartido->getPara());
                foreach ($destinatarios as $destinatario) {
                    // var_dump($destinatario);
                    // die;
                    $enviado = new Enviado();
                    $enviado->setCompartido($compartido);
                    $enviado->setDestinatario($destinatario);
                    $enviado->setEstadoId(1);
                    $this->em->persist($enviado);                    
                }
                $compartido->setEstadoId(2);
                $this->em->persist($compartido);
                $this->em->flush();
            }
        }
        if ($_ENV["COMMAND_FROM_CONTROLLER"] == 'true' || !empty($pendientesEnviar)) {
            if ($_ENV["COMMAND_FROM_CONTROLLER"] == 'true') {
                $pendientesEnviar = $this->em->getRepository(Enviado::class)->findToSend();
            }
            // Si encontramos un registros para ser enviados en la entidad Enviado
            // procedemos a despacharlos con el método de envio de correo
            foreach ($pendientesEnviar as $pendienteEnviar) {                
                $compartidoRelacionado = $this->em->getRepository(Compartido::class)->findOneById($pendienteEnviar->getCompartido()->getId());
                $message = (new \Swift_Message("Compartido gDocument: " . $compartidoRelacionado->getAsunto()))
                    ->setFrom([$_ENV["MAILER_FROM"]  =>$_ENV["MAILER_FROM_NAME"]])
                    ->setTo($pendienteEnviar->getDestinatario());

                if($compartidoRelacionado->getTipoNotificacion() == "NOTIFICACION_PASO") {
                    $message = $message->setBody(
                        $compartidoRelacionado->getContenido(),
                        'text/html'
                    );

                    $message = $message->addPart($compartidoRelacionado->getContenido(), 'text/html');
                } else {
                    $message = $message->setBody(
                        $this->templating->render(
                            'emails/mensajeConfirmado.html.twig',
                            array("contenido" => $compartidoRelacionado->getContenido())
                        ),
                        'text/html'
                    );
                }
                //Consulto los identificadores de los archivos adjuntos
                $client = new Gdrive();
                $clientGDocument = $client->getClient();
                $service = new \Google_Service_Drive($clientGDocument);
                $archivos_id = json_decode($compartidoRelacionado->getDescripcionAdjuntos());
                foreach ($archivos_id as $archivo_id) {
                    $archivo = $this->em->getRepository(Archivo::class)->findOneById($archivo_id);
                    $documentoLeido = $client->readFile($service, $archivo->getIdentificador());
                    $fileMetada = $documentoLeido["fileMetada"];
                    $attachment = new \Swift_Attachment($documentoLeido["archivo"], $archivo->getNombre(), $fileMetada["mimeType"]);
                    $message->attach($attachment);
                }
                $this->mailer->send($message);
                // por cada envío guardar evidencia en entidad Enviado
                $pendienteEnviar->setFechaEnviado(new \DateTime());
                $pendienteEnviar->setEstadoId(2);
                $this->em->persist($pendienteEnviar);
            }
            $this->em->flush();
        }
    }
}
