<?php

namespace App\Controller;

use App\Entity\Archivo;
use App\Entity\CampoFormularioVersion;
use App\Entity\FormularioVersion;
use App\Entity\Registro;
use App\Entity\RegistroEntidad;
use App\Entity\RegistroCampo;
use App\Entity\TipoCorrespondencia;
use App\Utils\Auditor;
use App\Utils\Gdrive;
use App\Utils\GestorArchivos;
use App\Utils\TextUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class VersionarArchivoService
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
    public function save(Request $request)
    {

        //validar tamaño máximo de subida de archivo en el servidor.
        // estructura_formulario_id
        // registro_id
        // version
        // comentario
        // archivo a guardar
        //$data = json_decode($request->getContent());
        $fileToUpload = $request->files->get("archivo");
        $archivoOrigen = $request->request->get("archivoOrigen");

        $archivoLastVersion = $this->em->getRepository(Archivo::class)->findLastVersion($archivoOrigen);
        $registro = $this->em->getRepository(Registro::class)->findOneById($archivoLastVersion->getRegistroId());

        $nombreArchivo = $fileToUpload->getClientOriginalName();
        $parts = explode('.', $nombreArchivo);
        $last = array_pop($parts);
        $parts = array(implode('_', $parts), $last);
        $nombreArchivo = $parts[0];
        $extension =  $parts[1];

        $MensajetipoDocumental = "";

        if ($archivoLastVersion->getTipoDocumental() == 1) {
            $parts = explode('.', $archivoLastVersion->getNombre());
            $last = array_pop($parts);
            $parts = array(implode('_', $parts), $last);
            $nombreArchivo = $parts[0];

            $MensajetipoDocumental = "TIPO DOCUMENTAL";
        }

        $nombreArchivo = $nombreArchivo . '.' . $extension;

        $gestorArchivo = new GestorArchivos();
        // Aca se extrae el directorio a almacenar a traves del valor enviado en estructura_formulario_id
        $rootFolder = $_ENV["FILE_LOCATION"];
        $folder = date("Ymd");
        $result = $gestorArchivo->uploadFile($this->em, $fileToUpload, $folder, $rootFolder);

        $newArchivo = new Archivo();
        $newArchivo->setRegistro($registro);
        $newArchivo->setCarpeta($result["carpeta"]);
        $newArchivo->setNombre($nombreArchivo);
        $newArchivo->setVersion($archivoLastVersion->getVersion() + 1);
        $newArchivo->setFechaVersion(new \DateTime());
        $newArchivo->setComentario($archivoLastVersion->getComentario());
        $newArchivo->setEstadoId($archivoLastVersion->getEstadoId());
        $newArchivo->setEjecucionPasoId($archivoLastVersion->getEjecucionPasoId());
        $newArchivo->setTipoDocumental($archivoLastVersion->getTipoDocumental());
        $newArchivo->setVigente(1);
        $newArchivo->setIdentificador($result["gDriveFileSavedID"]);
        $newArchivo->setTipoArchivo($archivoLastVersion->getTipoArchivo());
        $newArchivo->setArchivoOrigen($archivoLastVersion->getArchivoOrigen());
        $this->em->persist($newArchivo);
        $this->em->flush();

        $usuario = $this->tokenStorage->getToken()->getUser();
        $valor_actual = array(
            "Radicado" => $registro->getId(),
            "Archivo" => $newArchivo->getNombre(),
            "Versión" => $newArchivo->getVersion(),
            "Tipo archivo" => $newArchivo->getTipoArchivo(),
            "Archivo origen" => $newArchivo->getArchivoOrigen()
        );

        $valor_anterior = array(
            "Radicado" => $registro->getId(),
            "Archivo" => $archivoLastVersion->getNombre(),
            "Versión" => $archivoLastVersion->getVersion(),
            "Tipo archivo" => $archivoLastVersion->getTipoArchivo(),
            "Archivo origen" => $archivoLastVersion->getArchivoOrigen()
        );

        $accion = "VERSIONAR ARCHIVO";

        if(strlen($MensajetipoDocumental) > 0) {
            $accion = $accion . " " . $MensajetipoDocumental;
        }
        
        Auditor::registerAction($this->em, $registro, $usuario, $valor_anterior, $valor_actual, $accion);

        return $newArchivo;
    }
}
