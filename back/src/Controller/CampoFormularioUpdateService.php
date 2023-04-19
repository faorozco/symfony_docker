<?php

namespace App\Controller;

use App\Entity\CampoFormulario;
use App\Entity\Registro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class CampoFormularioUpdateService
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
        $id = $request->attributes->get('id');
        $item = (array) $data;
        $campoFormulario = $this->em->getRepository(CampoFormulario::class)->findOneById($id);
        $campoAnterior = $campoFormulario->getCampo();
        $nombreCampoAnterior = $campoFormulario->getValorCuadroTexto();

        if (isset($item["indice"]))
            $campoFormulario->setIndice($item["indice"]);
        if (isset($item["formularioId"]))
            $campoFormulario->setFormularioId($item["formularioId"]);
        if (isset($item["listaId"]))
            $campoFormulario->setListaId($item["listaId"]);            
        if (isset($item["entidadColumnName"]))
            $campoFormulario->setEntidadColumnName($item["entidadColumnName"]);
        if (isset($item["campo"]))
            $campoFormulario->setCampo($item["valorCuadroTexto"]);
        if (isset($item["tipoCampo"])) {
            $storeTipoCampo = $campoFormulario->getTipoCampo();
            $currentTipoCampo = $item["tipoCampo"];

            if ($currentTipoCampo != $storeTipoCampo) {
                $campoFormulario->setIndice(false);

                $config = $campoFormulario->getConfig();

                $config["entidadColumnOrder"] = [];
                
                $campoFormulario->setConfig($config);
            }
            $campoFormulario->setTipoCampo($item["tipoCampo"]);
        }
           
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

        if (isset($item["entidadId"])) {
            $storeEntidad = $campoFormulario->getEntidadId();
            $currentEntidad = $item["entidadId"];

            if ($currentEntidad != $storeEntidad) {
                $campoFormulario->setIndice(false);

                $config = $campoFormulario->getConfig();

                $config["entidadColumnOrder"] = [];
                
                $campoFormulario->setConfig($config);
            }
            $campoFormulario->setEntidadId($item["entidadId"]);
        } else {
            $campoFormulario->setEntidadId(null);
        }

        if (isset($item["entidadColumnName"])) {
            $campoFormulario->setEntidadColumnName($item["entidadColumnName"]);
        } else {
            $campoFormulario->setEntidadColumnName(null);
        }

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
}
