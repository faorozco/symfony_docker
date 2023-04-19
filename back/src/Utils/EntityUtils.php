<?php
namespace App\Utils;

use App\Entity\EstructuraDocumental;

class EntityUtils
{
    public static function crearRutaEstructuraDocumental($em, $ruta, $elementoEstructuraDocumental)
    {
        if (null !== $elementoEstructuraDocumental) {
            if ($elementoEstructuraDocumental->getCodigoDirectorioPadre() != "") {
                $elementoEstructuraDocumentalPadre = $em->getRepository(EstructuraDocumental::class)->FindOneBy(array("codigo_directorio" => $elementoEstructuraDocumental->getCodigoDirectorioPadre()));

                if ($elementoEstructuraDocumentalPadre != null) {
                    return self::crearRutaEstructuraDocumental($em, $ruta . $elementoEstructuraDocumental->getDescripcionSimple() . " / ", $elementoEstructuraDocumentalPadre);
                } else {
                    return $ruta .= $elementoEstructuraDocumental->getDescripcionSimple();
                }
            } else {
                $ruta .= $elementoEstructuraDocumental->getDescripcionSimple();
                return $ruta;
            }
        } 
    }

}
