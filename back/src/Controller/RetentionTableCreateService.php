<?php

namespace App\Controller;

use App\Entity\Rol;
use App\Entity\EstructuraDocumental;
use App\Entity\TablaRetencion;
use App\Entity\Formulario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Undocumented class
 */
class RetentionTableCreateService
{
    private $_em;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Group
     */
    public function create(Request $request):TablaRetencion
    {
        $retentionTable = json_decode($request->getContent());
        $newRetentionTable = new TablaRetencion();
        $newRetentionTable->setTiempoRetencionArchivoGestion($retentionTable->tiempoRetencionArchivoGestion);
        $newRetentionTable->setUnidadRetencionArchivoGestion($retentionTable->unidadRetencionArchivoGestion);
        $newRetentionTable->setTiempoRetencionArchivoCentral($retentionTable->tiempoRetencionArchivoCentral);
        $newRetentionTable->setUnidadRetencionArchivoCentral($retentionTable->unidadRetencionArchivoCentral);
        $newRetentionTable->setTipoSoporte($retentionTable->tipoSoporte);
        $newRetentionTable->setDisposicionFinalBorrar($retentionTable->disposicionFinalBorrar);
        $newRetentionTable->setDisposicionFinalConservacionTotal($retentionTable->disposicionFinalConservacionTotal);
        $newRetentionTable->setDisposicionFinalConservacionDigital($retentionTable->disposicionFinalConservacionDigital);
        $newRetentionTable->setDisposicionFinalMicrofilmado($retentionTable->disposicionFinalMicrofilmado);
        $newRetentionTable->setDisposicionFinalSeleccion($retentionTable->disposicionFinalSeleccion);
        $newRetentionTable->setProcedimientoDisposicion($retentionTable->procedimientoDisposicion);
        $newRetentionTable->setInicioVigencia(date_create_from_format("Y-m-d", date('Y-m-d', strtotime($retentionTable->inicioVigencia))));
        $newRetentionTable->setLeyNormatividad($retentionTable->leyNormatividad);
        $newRetentionTable->setDisposicionFinalDigitalizacionMicrofilmacion($retentionTable->disposicionFinalDigitalizacionMicrofilmacion);
        $newRetentionTable->setDisposicionFinalMigrar($retentionTable->disposicionFinalMigrar);
        $newRetentionTable->setTransferenciaMedioElectronico($retentionTable->transferenciaMedioElectronico);
        $newRetentionTable->setDireccionDocumentosAlmacenadosElectronicamente($retentionTable->direccionDocumentosAlmacenadosElectronicamente);
        $newRetentionTable->setEstadoId($retentionTable->estadoId);
        $newRetentionTable->setEstructuraDocumentalId($retentionTable->estructuraDocumentalId);
        
        $this->em->persist($newRetentionTable);
        $this->em->flush();

        $estructuraDocumental = $this->em->getRepository(EstructuraDocumental::class)->findOneById($retentionTable->estructuraDocumentalId);
        
        /*if($estructuraDocumental->getType() == 'tipo_documental' && isset($retentionTable->formularioId)) {
            $formulario = $this->em->getRepository(Formulario::class)->findOneById($retentionTable->formularioId);
            $estructuraDocumental->setFormulario($formulario);
            $formulario->setEstructuraDocumental($estructuraDocumental);
            $this->em->persist($formulario);
        }*/
    

        $newRetentionTable->setEstructuraDocumental($estructuraDocumental);
        $this->em->persist($newRetentionTable);
        $this->em->flush();

        
        $estructuraDocumental->setTablaRetencion($newRetentionTable);
        $this->em->persist($estructuraDocumental);
        $this->em->flush();
        return $newRetentionTable;
    }
}
