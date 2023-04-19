<?php

namespace App\Controller\Contacto;

use \DateTime;
use App\Entity\Ciudad;
use App\Entity\Tercero;
use App\Utils\ArrayExport;
use App\Utils\EntityExport;
use App\Entity\TipoContacto;
use App\Entity\TipoCorrespondencia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class ContactoDownloadModelService
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
        $this->entidad = "Contacto";
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function download(Request $request)
    {
        $resultado = array();
        
        $atributosContacto[] = ["nombre", "tratamiento", "telefono_fijo", "celular", "correo", "comentario", "tercero", "ciudad", "tipoContacto", "estado_id", "cargo"];
        $tratamiento[] = ["Sr.", "Sra.", "Dr."];
        $resultado["contacto"] = $atributosContacto;
        $resultado["tratamiento"] = $tratamiento;

        $resultado = $this->GenerateTipoCorrespondencia($resultado);
        $resultado = $this->GenerateTercero($resultado);
        $resultado = $this->GenerateCiudad($resultado);
        $resultado = $this->GenerateTipoContacto($resultado);      

        $exportedFile = "modelocontactos.zip";
        $exportData = new ArrayExport($resultado, $exportedFile);
        $response = $exportData->Export($request);
        return $response;
    }

    protected function GenerateTipoCorrespondencia($resultado)
    {
        //consultar la entidad Tipo de Correspondencia
        $tiposCorrespondencia = $this->em->getRepository(TipoCorrespondencia::class)->findAll();
        foreach ($tiposCorrespondencia as $tipoCorrespondencia) {
            $items[] = array("id" => $tipoCorrespondencia->getId(), "nombre" => $tipoCorrespondencia->getNombre());
        }
        $resultado["tipos-correspondencia"] = $items;
        return $resultado;
    }

    protected function GenerateTercero($resultado)
    {
        //consultar la entidad Tipo de Correspondencia
        $terceros = $this->em->getRepository(Tercero::class)->findAll();
        foreach ($terceros as $tercero) {
            $items[] = array("id" => $tercero->getId(), "identificacion" => $tercero->getIdentificacion(), "nombre" => $tercero->getNombre());
        }
        $resultado["terceros"] = $items;
        return $resultado;
    }

    protected function GenerateCiudad($resultado)
    {
        //consultar la entidad Tipo de Correspondencia
        $ciudades = $this->em->getRepository(Ciudad::class)->findAll();
        foreach ($ciudades as $ciudad) {
            $items[] = array("id" => $ciudad->getId(), "nombre" => $ciudad->getNombre());
        }
        $resultado["ciudades"] = $items;
        return $resultado;
    }

    protected function GenerateTipoContacto($resultado)
    {
        //consultar la entidad Tipo de Correspondencia
        $tiposContacto = $this->em->getRepository(TipoContacto::class)->findAll();
        foreach ($tiposContacto as $tipoContacto) {
            $items[] = array("id" => $tipoContacto->getId(), "descripcion" => $tipoContacto->getDescripcion());
        }
        $resultado["tipos-contacto"] = $items;
        return $resultado;
    }
}
