<?php

namespace App\Controller\CampoFormulario;

use App\Entity\CampoFormulario;
use App\Entity\Formulario;
use App\Entity\Entidad;
use App\Entity\Lista;
use App\Entity\Registro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class CampoFormularioCreateService
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

    public function put($request)
    {
        $data = json_decode($request->getContent());
        $item = (array) $data;
        $campoFormulario = new CampoFormulario();
        if (isset($item["formularioId"])) {
            $formulario = $this->em->getRepository(Formulario::class)->findOneById($item["formularioId"]);
            $campoFormulario->setFormulario($formulario);
        }
            
        if (isset($item["listaId"])) {
            $lista = $this->em->getRepository(Lista::class)->findOneById($item["listaId"]);
            $campoFormulario->setLista($lista);
        }
        
        if (isset($item["entidadId"])) {
            $entidad = $this->em->getRepository(Entidad::class)->findOneById($item["entidadId"]);
            $campoFormulario->setEntidad($entidad);
        }

        if (isset($item["entidadColumnName"])) {
            $campoFormulario->setEntidadColumnName($item["entidadColumnName"]);
        }
        
        if (isset($item["campo"]))
            $campoFormulario->setCampo($item["valorCuadroTexto"]);
        if (isset($item["tipoCampo"]))
            $campoFormulario->setTipoCampo($item["tipoCampo"]);
        if (isset($item["valorCuadroTexto"]))
            $campoFormulario->setValorCuadroTexto($item["valorCuadroTexto"]);
        if (isset($item["posicion"]))
            $campoFormulario->setPosicion($item["posicion"]);
        if (isset($item["valorMinimo"]))
            $campoFormulario->setValorMinimo($item["valorMinimo"]);
        if (isset($item["longitud"]))
            $campoFormulario->setLongitud($item["longitud"]);
        if (isset($item["obligatorio"]))
            $campoFormulario->setObligatorio($item["obligatorio"]);
        if (isset($item["indice"]))
            $campoFormulario->setIndice($item["indice"]);
        if (isset($item["imprimeSticker"]))
            $campoFormulario->setImprimeSticker($item["imprimeSticker"]);
        if (isset($item["posicionSticker"]))
            $campoFormulario->setPosicionSticker($item["posicionSticker"]);
        if (isset($item["ayuda"]))
            $campoFormulario->setAyuda($item["ayuda"]);
        if (isset($item["itemTablaDefecto"]))
            $campoFormulario->setItemTablaDefecto($item["itemTablaDefecto"]);
        if (isset($item["itemListaDefecto"]))
            $campoFormulario->setItemListaDefecto($item["itemListaDefecto"]);
        if (isset($item["mostrarFront"]))
            $campoFormulario->setMostrarFront($item["mostrarFront"]);
        if (isset($item["posicionFront"]))
            $campoFormulario->setPosicionFront($item["posicionFront"]);
        if (isset($item["estadoId"]))
            $campoFormulario->setEstadoId($item["estadoId"]);
        if (isset($item["campoFormularioId"]))
            $campoFormulario->setCampoFormularioId($item["campoFormularioId"]);
        if (isset($item["campoUnico"]))
            $campoFormulario->setCampoUnico($item["campoUnico"]);
        if (isset($item["ocultoAlRadicar"]))
            $campoFormulario->setOcultoAlRadicar($item["ocultoAlRadicar"]);
        if (isset($item["valorDefecto"]))
            $campoFormulario->setValorDefecto($item["valorDefecto"]);
        $this->em->persist($campoFormulario);
        $this->em->flush();
        /*$registros = $this->em->getRepository(Registro::class)->findBy(array("formulario_id" => $campoFormulario->getFormulario()->getId()));
        foreach ($registros as $registro) {
            $resumen = $registro->getResumen();
            $busqueda = $registro->getBusqueda();
            if (array_key_exists($nombreCampoAnterior, $resumen)) {
                //Reemplazar "valor cuadro de texto" en los resumenes de los registros relacionados al formulario 
                if (isset($item["estadoId"]) && $item["estadoId"] != 0)
                    $resumen[$campoFormulario->getValorCuadroTexto()] = $resumen[$nombreCampoAnterior];
                unset($resumen[$nombreCampoAnterior]);
                $registro->setResumen($resumen);
                //Reemplazar campo "valor cuadro de texto" en el campo busqueda de los registros relacionados al formulario
                if (isset($item["estadoId"]) && $item["estadoId"] != 0)
                    $busqueda[$campoFormulario->getCampo()] = $busqueda[$campoAnterior];
                unset($busqueda[$campoAnterior]);
                $registro->setBusqueda($busqueda);
                $this->em->persist($registro);
                $this->em->flush();
            }
        }*/
        return ($campoFormulario);
    }

    public function saveIndex($data)
    {
        $campoId = $data->{"campoId"};
        $columnOrder = $data->{"columnOrder"};
        $formularioId = $data->{"formularioId"};

        $camposFormulario = $this->em->getRepository(CampoFormulario::class)
                    ->findBy(array("formulario_id" => $formularioId));

        foreach($camposFormulario as $campoFormulario) {
            if($campoFormulario->getIndice() == true || $campoFormulario->getId() == $campoId) {
                $campoFormulario->setIndice($campoFormulario->getId() == $campoId);

                $config = $campoFormulario->getConfig();

                if ($campoFormulario->getIndice() == true) {
                    $config["entidadColumnOrder"] = $columnOrder;
                } else {
                    $config["entidadColumnOrder"] = [];
                }
                

                $campoFormulario->setConfig($config);

                $this->em->persist($campoFormulario);
                $this->em->flush();
            }
        }

        return (array("result" => array("response" => "Indice creado correctamente"))); 
    }
}
