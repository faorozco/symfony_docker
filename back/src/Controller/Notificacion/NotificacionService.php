<?php

namespace App\Controller\Notificacion;

use App\Entity\CampoFormularioVersion;
use App\Entity\EjecucionPaso;
use App\Entity\PasoVersion;
use App\Entity\Registro;
use App\Entity\Usuario;
use App\Entity\EjecucionFlujo;
use App\Entity\FlujoTrabajoVersion;
use App\Entity\FormularioVersion;
use App\Entity\Notificacion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use App\Controller\Registro\RegistroService;
use App\Controller\Async\SaveCompartidoAsyncService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Undocumented class
 */
class NotificacionService
{
    private $_em;

    private RegistroService $registroService;
    private SaveCompartidoAsyncService $saveCompartidoAsyncService;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, KernelInterface $kernel)
    {
        $this->em = $entityManager;
        $this->registroService = new RegistroService($entityManager);
        $this->saveCompartidoAsyncService = new SaveCompartidoAsyncService($entityManager, $tokenStorage, $kernel);
    }

    public function responsablePaso($ejecucionPasoId, $registro) {
        $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
        //responsable
        $responsable = $this->usuarioResponsable($ejecucionPaso);
        $responsableEmail = $this->responsableEmail($responsable);
        //encabezado
        
        $encabezado = "Se te asignó una nueva tarea: ";
        $remitente = $this->remitente($ejecucionPasoId);

        if($remitente != null) {
            $encabezado = "El usuario " . $remitente->getLogin() . " te asign&oacute; una nueva tarea:";
        }

        //detalle
        $contenido = $this->contenido($ejecucionPasoId, $registro, $encabezado);
        $asunto = "Se asignó un nuevo paso con el código: $ejecucionPasoId";
        $this->saveCompartidoAsyncService->saveEmail($responsableEmail, (new \DateTime())->format("Y-m-d"), $asunto, $contenido, "[]", "NOTIFICACION_PASO", $registro);
    }

    public function remitentePaso($ejecucionPasoId, $registro) {
        //remitente
        $remitente = $this->remitente($ejecucionPasoId);

        if($remitente != null) {
            $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);

            //encabezado
            $usuarioResponsable = $this->usuarioResponsable($ejecucionPaso);
            $encabezado = "El usuario " . $usuarioResponsable->getLogin() . " cumplió con la tarea que le asignaste: ";

            //responsable
            $remitenteEmail = $this->responsableEmail($remitente);
            //detalle
            $contenido = $this->contenido($ejecucionPasoId, $registro, $encabezado);
            $asunto = "Se completó el paso con el código: " . $ejecucionPasoId;
            $this->saveCompartidoAsyncService->saveEmail($remitenteEmail, (new \DateTime())->format("Y-m-d"), $asunto, $contenido, "[]", "NOTIFICACION_PASO", $registro);
        }
    }

    public function remitenteVistoBueno($ejecucionPasoId, $registro) {
        $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);

        //encabezado
        $usuarioResponsable = $this->usuarioResponsable($ejecucionPaso);
        $usuarioVistoBueno = $this->usuarioVistoBueno($ejecucionPaso);
        $encabezado = "El usuario " . $usuarioVistoBueno->getLogin() . " aprobó el visto bueno: ";

        //responsable
        $responsableEmail = $this->responsableEmail($usuarioResponsable);
        //detalle
        $contenido = $this->contenido($ejecucionPasoId, $registro, $encabezado);
        $asunto = "Se aprobó el visto bueno para el paso con el código: " . $ejecucionPasoId;
        $this->saveCompartidoAsyncService->saveEmail($responsableEmail, (new \DateTime())->format("Y-m-d"), $asunto, $contenido, "[]", "NOTIFICACION_PASO", $registro);
    }

    private function responsableEmail($usuario) {
        return "[\"" . $usuario->getEmail() . "\"]";
    }

    private function usuarioResponsable($ejecucionPaso) {
        $usuarioId = $ejecucionPaso->getUsuarioResponsable()->getId();
        $usuario = $this->em->getRepository(Usuario::class)->findOneById($usuarioId);

        return $usuario;
    }

    private function usuarioVistoBueno($ejecucionPaso) {
        $usuarioId = $ejecucionPaso->getUsuarioResponsableVistoBueno()->getId();
        $usuario = $this->em->getRepository(Usuario::class)->findOneById($usuarioId);

        return $usuario;
    }

    private function contenido($ejecucionPasoId, $registro, $encabezado) {
        
        $ejecucionPaso = $this->em->getRepository(EjecucionPaso::class)->findOneById($ejecucionPasoId);
        $pasoVersion = $this->em->getRepository(PasoVersion::class)->findOneById($ejecucionPaso->getPasoVersion()->getId());
        $ejecucionFlujo = $this->em->getRepository(EjecucionFlujo::class)->findOneById($ejecucionPaso->getEjecucionFlujo()->getId());
        $flujoTrabajoVersion = $this->em->getRepository(FlujoTrabajoVersion::class)->findOneById($ejecucionFlujo->getFlujoTrabajoVersion()->getId());
        
        //numero y nombre flujo
        $ejecucionFlujoId = $ejecucionFlujo->getId();
        $flujoNombre = $flujoTrabajoVersion->getNombre();

        //numero y nombre paso
        $pasoNombre = $pasoVersion->getDescripcion();
        $pasoNumero = $pasoVersion->getNumero();

        //registro
        $formularioVersion = $this->em->getRepository(FormularioVersion::class)->findOneById($registro->getFormularioVersionId());

        $fechaVencimiento = $ejecucionPaso->getFechaVencimiento()->format("Y-m-d");
        $formularioNombre = explode("/", $formularioVersion->getNombre())[0];
        $registroFecha = $registro->getFechaHora()->format("Y-m-d H:i:s");

        $camposFormularioVersion = $this->em->getRepository(CampoFormularioVersion::class)->findBy(array("formulario_version_id" => $formularioVersion->getId()), array("posicion" => "ASC"), 10);

        $values = "";
        foreach($camposFormularioVersion as $campo) {
            $this->registroService->result = array();
            $value = $this->registroService->getValueByField($campo, $registro);

            $result = "";
            foreach($value as $item) {
                $result = $result . $item["valor"] . ";";
            }
            $value = rtrim($result, ";");

            if($value != "") {
                $values = $values . " | " . $campo->getValorCuadroTexto() . ": " . $value;
            }
            
        }

        $values = $values . " |";
        //detalle
        return "<p style='margin-top:0cm;margin-right:0cm;margin-bottom:7.0pt;margin-left:0cm;line-height:115%;font-size:16px;font-family:\"Liberation Serif\",serif;'><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>" . $encabezado . "</span></p>
        <p><strong><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>N&uacute;mero de Flujo:&nbsp;</span></strong><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>" . $ejecucionFlujoId . "</span></p>
        <p><strong><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>Nombre de Flujo:&nbsp;</span></strong><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>" . $flujoNombre . "</span></p>
        <p><strong><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>N&uacute;mero de Paso:&nbsp;</span></strong><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>" . $pasoNumero . "</span></p>
        <p><strong><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>Nombre de Paso:&nbsp;</span></strong><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>" . $pasoNombre . "</span></p>
        <p><br></p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:7.0pt;margin-left:0cm;line-height:115%;font-size:16px;font-family:\"Liberation Serif\",serif;'><strong><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>Detalle:</span></strong></p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:7.0pt;margin-left:0cm;line-height:115%;font-size:16px;font-family:\"Liberation Serif\",serif;'><span style='font-family:\"Arial\, Helvetica\, sans-serif\";'>Fecha de vencimiento: " . $fechaVencimiento . " | " . $registro->getRadicado() . " | " . $formularioNombre . " | Fecha de radicación: " . $registroFecha . $values;
    }

    private function remitente($ejecucionPasoId) {
        $pasos = $this->em->getRepository(EjecucionPaso::class)->findBy(array("ejecucion_paso_id_siguiente" => $ejecucionPasoId), array("id" => "DESC"));
        $usuario = null;
        if (count($pasos) > 0) {
            $usuarioId = $pasos[0]->getUsuarioResponsableId();
            $usuario = $this->em->getRepository(Usuario::class)->findOneById($usuarioId);
        }

        return $usuario;
    }
}
