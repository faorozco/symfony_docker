<?php

namespace App\Controller;

use App\Entity\Formulario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\FormularioVersion;
use App\Entity\CampoFormulario;
use App\Entity\CampoFormularioVersion;
use App\Entity\OpcionFormulario;
use App\Entity\OpcionFormularioVersion;
use App\Entity\ConsultaMaestra;
use App\Entity\EstructuraDocumental;
use App\Entity\EstructuraDocumentalVersion;
use App\Entity\TablaRetencion;
use App\Entity\TablaRetencionVersion;
use App\Entity\Plantilla;
use App\Entity\PlantillaVersion;
use App\Entity\PasoCampo;
use App\Entity\PasoCampoVersion;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Undocumented class
 */
class GenerateFormVersionService
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
    public function generate(Request $request)
    {
        $formulario = $this->em->getRepository(Formulario::class)->findOneById($request->attributes->get('id'));
        $version = $formulario->getVersion();
        $version++;
        $formulario->setVersion($version);
        $formulario->setFechaVersion(new \DateTime());

        $formularioVersion = new FormularioVersion();
        $formularioVersion->setTipoFormulario($formulario->getTipoFormulario());
        $formularioVersion->setVersion($formulario->getVersion());
        $formularioVersion->setFechaVersion($formulario->getFechaVersion());
        $formularioVersion->setNombre($formulario->getNombre());
        $formularioVersion->setNomenclaturaFormulario($formulario->getNomenclaturaFormulario());
        $formularioVersion->setFormularioTransversal($formulario->getFormularioTransversal());
        $formularioVersion->setPermiteTareas($formulario->getPermiteTareas());
        $formularioVersion->setGeneraPdfConFirmaDigital($formulario->getGeneraPdfConFirmaDigital());
        $formularioVersion->setRadicadoElectronico($formulario->getRadicadoElectronico());
        $formularioVersion->setTipoSticker($formulario->getTipoSticker());
        $formularioVersion->setInicioVigencia($formulario->getInicioVigencia());
        $formularioVersion->setFinVigencia($formulario->getFinVigencia());
        $formularioVersion->setAyuda($formulario->getAyuda());
        $formularioVersion->setEstadoId($formulario->getEstadoId());
        $formularioVersion->setTablaRetencionDisposicionFinalConservacionDigital($formulario->getTablaRetencionDisposicionFinalConservacionDigital());
        
        $formularioVersion->setFormulario($formulario);

        // Confirmar que sea así
        //$formularioVersion->setFlujoTrabajoVersionId($formulario->getFlujoTrabajoId());

        // Confirmar que sea así
        //$formularioVersion->setEstructuraDocumentalVersion($formulario->getEstructuraDocumental());

        $this->em->persist($formularioVersion);
        $this->em->flush();   

        // Versionar campos del formulario
        $hashRelacion = new ArrayCollection();
        $hashCampoFormulario = new ArrayCollection();
        foreach ($formulario->getCampoFormularios() as $campoFormulario) {
            $hashCampoFormulario[$campoFormulario->getId()] = $campoFormulario;
            $formularioVersion->addCampoFormularioVersion($this->generateCampoFormularioVersion($campoFormulario, $formularioVersion, $hashRelacion));
        }

        // Relacionar campos del formulario
        foreach ($formularioVersion->getCampoFormulariosVersion() as $campoFormularioVersion) {
            $this->relacionarCampoFormularioVersion($campoFormularioVersion, $formularioVersion, $hashRelacion, $hashCampoFormulario);
        }

        // Versionar opciones del formulario
        foreach ($formulario->getOpcionFormularios() as $opcionFormulario) {
            $formularioVersion->addOpcionFormularioVersion($this->generateOpcionFormularioVersion($opcionFormulario, $formularioVersion));
        }

        // Versionar plantillas
        foreach($formulario->getPlantillas() as $plantilla) {
            $formularioVersion->addPlantillaVersion($this->generatePlanillaVersion($plantilla, $formularioVersion));
        }

         // Versionar estructura documental
        $estructuraDocumentalVersion = $this->em->getRepository(EstructuraDocumentalVersion::class)
        ->getEstructuraDocumentalVersionMaxVersionByEstructuraDocumentalId($formulario->getId());
        //$this->generateEstructuraDocumentalVersion($formulario->getEstructuraDocumental(), $formularioVersion);
        $formularioVersion->setEstructuraDocumentalVersion($estructuraDocumentalVersion);
        
       
        $this->em->persist($formularioVersion);
        $this->em->persist($formulario);
        $this->em->flush();
        return array("response" => $formulario);
    }

    private function generateCampoFormularioVersion(CampoFormulario $campoFormulario, FormularioVersion $formularioVersion, $hashRelacion) {
        $campoFormularioVersion = new CampoFormularioVersion();
        $campoFormularioVersion->setCampo($campoFormulario->getCampo());
        $campoFormularioVersion->setTipoCampo($campoFormulario->getTipoCampo());
        $campoFormularioVersion->setValorCuadroTexto($campoFormulario->getValorCuadroTexto());
        $campoFormularioVersion->setValorMinimo($campoFormulario->getValorMinimo());
        $campoFormularioVersion->setLongitud($campoFormulario->getLongitud());
        $campoFormularioVersion->setObligatorio($campoFormulario->getObligatorio());
        $campoFormularioVersion->setIndice($campoFormulario->getIndice());
        $campoFormularioVersion->setPosicionSticker($campoFormulario->getPosicionSticker());
        $campoFormularioVersion->setImprimeSticker($campoFormulario->getImprimeSticker());
        $campoFormularioVersion->setAyuda($campoFormulario->getAyuda());
        $campoFormularioVersion->setItemTablaDefecto($campoFormulario->getItemTablaDefecto());
        $campoFormularioVersion->setValorDefecto($campoFormulario->getValorDefecto());
        $campoFormularioVersion->setItemListaDefecto($campoFormulario->getItemListaDefecto());
        $campoFormularioVersion->setMostrarFront($campoFormulario->getMostrarFront());
        $campoFormularioVersion->setPosicionFront($campoFormulario->getPosicionFront());
        $campoFormularioVersion->setPosicion($campoFormulario->getPosicion());
        $campoFormularioVersion->setMostrarFront($campoFormulario->getMostrarFront());
        $campoFormularioVersion->setLista($campoFormulario->getLista());
        $campoFormularioVersion->setEstadoId($campoFormulario->getEstadoId());
        $campoFormularioVersion->setEntidad($campoFormulario->getEntidad());
        $campoFormularioVersion->setCampoUnico($campoFormulario->getCampoUnico());
        $campoFormularioVersion->setOcultoAlRadicar($campoFormulario->getOcultoAlRadicar());
        $campoFormularioVersion->setCampoFormularioId($campoFormulario->getId());
        $campoFormularioVersion->setFormularioVersion($formularioVersion);
        $campoFormularioVersion->setEntidadColumnName($campoFormulario->getEntidadColumnName());
        $campoFormularioVersion->setConfig($campoFormulario->getConfig());

        $this->em->persist($campoFormularioVersion);
        $this->em->flush();

        $hashRelacion[$campoFormulario->getId()] = $campoFormularioVersion->getId();

        foreach($campoFormulario->getPasoCampos() as $pasoCampo) {
            $campoFormularioVersion->addPasoCampoVersion($this->generatePasoCampoVersion($pasoCampo, $campoFormularioVersion));
        }

        $this->em->persist($campoFormularioVersion);

        return $campoFormularioVersion;
    }

    private function relacionarCampoFormularioVersion(CampoFormularioVersion $campoFormularioVersion, FormularioVersion $formularioVersion, $hashRelacion, $hashCampoFormulario) {
        $campoFormulario = $hashCampoFormulario[$campoFormularioVersion->getCampoFormularioId()];

        if ($campoFormulario->getCampoFormularioId() != null) {
            if (!isset($hashRelacion[$campoFormulario->getCampoFormularioId()])) {
                $campoFormularioVersionRelacionado = $this->em->getRepository(CampoFormularioVersion::class)->findOneByCampoFormularioId($campoFormulario->getCampoFormularioId());
                $hashRelacion[$campoFormulario->getCampoFormularioId()] = $campoFormularioVersionRelacionado->getId();
            }
            $campoFormularioVersion->setCampoFormularioVersionId($hashRelacion[$campoFormulario->getCampoFormularioId()]);
            $this->em->persist($campoFormularioVersion);
            $this->em->flush();
        }
    }

    private function generateOpcionFormularioVersion(OpcionFormulario $opcionFormulario, FormularioVersion $formularioVersion) {
        $opcionFormularioVersion = new OpcionFormularioVersion();
        $opcionFormularioVersion->setOpcionFormulario($opcionFormulario);
        $opcionFormularioVersion->setFormularioVersion($formularioVersion);

        $this->em->persist($opcionFormularioVersion);
        $this->em->flush();

        return $opcionFormularioVersion;
    }


    private function generatePlanillaVersion(Plantilla $plantilla, FormularioVersion $formularioVersion) {
        $plantillaVersion = new PlantillaVersion();
        $plantillaVersion->setDescripcion($plantilla->getDescripcion());
        $plantillaVersion->setContenido($plantilla->getContenido());
        $plantillaVersion->setEstadoId($plantilla->getEstadoId());
        $plantillaVersion->setPlantillaId($plantilla->getId());
        $plantillaVersion->setFormularioVersion($formularioVersion);

        $this->em->persist($plantillaVersion);
        $this->em->flush();

        return $plantillaVersion;
    }

    private function generatePasoCampoVersion(PasoCampo $pasoCampo, CampoFormularioVersion $campoFormularioVersion) {
        $pasoCampoVersion = new PasoCampoVersion();
        $pasoCampoVersion->setCampoFormularioVersion($campoFormularioVersion);
        $pasoCampoVersion->setPasoCampoId($pasoCampo->getId());
        $pasoCampoVersion->setEstadoId($pasoCampo->getEstadoId());

        $this->em->persist($pasoCampoVersion);
        $this->em->flush();

        return $pasoCampoVersion;
    }
}
