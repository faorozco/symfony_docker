<?php
namespace App\Utils;

use App\Entity\CampoFormularioVersion;
use App\Entity\FormularioVersion;
use App\Entity\TipoCorrespondencia;
use Dompdf\Dompdf;
use Skies\QRcodeBundle\Generator\Generator;

class StickerGenerator
{
    public static function Get($em, $registro)
    {
        //consulto por formulario_version_id el tipo de sticker correspondiente al registro
        $formularioVersion = $em->getRepository(FormularioVersion::class)->findOneById($registro->getFormularioVersionId());
        $tipoSticker = $formularioVersion->getTipoSticker();
        $stickersSeleccionados = explode(",", $tipoSticker);
        $usuarioRegistro = $registro->getUsuario();
        $empresa = $usuarioRegistro->getSede()->getEmpresa()->getNombre();
        $procesoUsuarioRegistro = $usuarioRegistro->getProceso();
        $sedeProcesoUsuarioRegistro = $procesoUsuarioRegistro->getSede();
        $stickers = array();
        $info = array();
        $tipoCorrespondenciaNA = 1;
        //Organizar información del sticker
        //Consultar los campos del formularioVersion relacionado que fueron marcados con la opción "Imprimir Sticker"
        $stickerValues = self::GetStickerValues($em, $registro);
        //Construyo la cabecera del Sticker
        //1. Nombre del formularioVersion
        $tipoDocumental = explode("/", $formularioVersion->getNombre());
        $info["cabecera"]["nombreFormulario"] = $tipoDocumental[0];
        //Detalle del formularioVersion
        $info["cabecera"]["detalleFormulario"] = $formularioVersion->getNomenclaturaFormulario() . " " . $formularioVersion->getFechaVersion()->format("Y-m-d") . " " . $formularioVersion->getVersion();
        //Fecha y hora de registro
        $fechaRegistro = $registro->getFechaHora();
        //Verificar si el radicado tiene tipo de correspondencia y no es el tipo N/A
        $tipoCorrespondencia = $em->getRepository(TipoCorrespondencia::class)->findOneById($registro->getTipoCorrespondencia());
        if ($tipoCorrespondencia->getId() != $tipoCorrespondenciaNA) {
            $info["cabecera"]["tipocorrespondencia"] = "Correspondencia: " . $tipoCorrespondencia->getNombre();
            $info["cabecera"]["consecutivocorrespondencia"] = "Consecutivo: " . $registro->getConsecutivo();
        }
        //Detalle de radicado
        $info["cabecera"]["fecha"] = "Fecha: " . $fechaRegistro->format("Y-m-d H:i:s");
        $info["cabecera"]["sede"] = "Sede: " . $registro->getSede();
        $info["cabecera"]["empresa"] = "Empresa: " . $empresa;
        $info["cabecera"]["radicado"] = "Radicado: " . $formularioVersion->getId() . " - " . $sedeProcesoUsuarioRegistro->getId() . " - " . $registro->getId();
        $info["cabecera"]["usuario"] = "Usuario: " . $usuarioRegistro->getLogin();
        $info["camposFormulario"] = $stickerValues;
        $infoFormated = self::FormatInfo($info);
        //Uno la información del los valores del sticker con la cabecera y la guardo en $info
        foreach ($stickersSeleccionados as $stickerSeleccionado) {
            switch ($stickerSeleccionado) {
                case "qr":
                    $stickers[] = array("tipo" => "qr", "code" => self::GenerateQRBarcode($infoFormated, "qrcode", 50, 50));
                    break;
                case "barras":
                    ob_start();
                    $base64BarCodeImage = self::GenerateQRBarcode($formularioVersion->getId() . " - " . $sedeProcesoUsuarioRegistro->getId() . " - " . $registro->getId(), "c128", 2, 100);
                    $base64StickerDetails = self::generarImagen($infoFormated, "barras");
                    $src = imagecreatefromstring(base64_decode($base64BarCodeImage));
                    $dest = imagecreatefromstring(base64_decode($base64StickerDetails));
                    imagecopymerge($dest, $src, 10, 9, 0, 0, 350, 180, 100);
                    imagepng($dest);
                    $stickers[] = array("tipo" => "barras", "code" => base64_encode(ob_get_clean()));
                    break;
                // case "radicadoelectronico":
                //     $response = self::GetStickerValues($em, $registro);
                //     // $imagen = self::generarImagen($response);
                //     return self::generateRadicadoElectronico($response, $registro);
                //     break;
                case "impreso":
                    //$response = self::GetStickerValues($em, $registro);
                    $imagen = self::generarImagen($infoFormated);
                    $stickers[] = array("tipo" => "impreso", "code" => $imagen);
                    //consulto por formulario_id el tipo de sticker correspondiente al registro
                    break;
                case "ninguno":
                    $response = array();
                    $response = array(
                        "response" => array(
                            "detalle_campo" => array(
                                "nombre" => "Mensaje",
                                "valor" => "El registro no tiene sticker asignado",
                            ),
                        ),
                    );
                    $imagen = self::generarImagen($response);
                    return array("response" => array("tipo" => "impreso", "code" => $imagen));
                    //consulto por formulario_id el tipo de sticker correspondiente al registro
                    break;

            }
        }
        return array("response" => $stickers);
    }

    public static function print($em, $registro, $type) {
        $tipoCorrespondenciaNA = 1;
        //consulto por formulario_id el tipo de sticker correspondiente al registro
        $formularioVersion = $registro->getFormularioVersion();
        $tipoSticker = $formularioVersion->getTipoSticker();
        $usuarioRegistro = $registro->getUsuario();
        $empresa = $usuarioRegistro->getSede()->getEmpresa()->getNombre();
        $procesoUsuarioRegistro = $usuarioRegistro->getProceso();
        $sedeProcesoUsuarioRegistro = $procesoUsuarioRegistro->getSede();
        $stickers = array();
        $info = array();
        //Organizar información del sticker
        //Consultar los campos del formularioVersion relacionado que fueron marcados con la opción "Imprimir Sticker"
        $stickerValues = self::GetStickerValues($em, $registro);

        //Construyo la cabecera del Sticker
        //1. Nombre del formularioVersion
        $tipoDocumental = explode("/", $formularioVersion->getNombre());
        $info["cabecera"]["nombreFormulario"] = $tipoDocumental[0];
        //Detalle del formularioVersion
        $info["cabecera"]["detalleFormulario"] = $formularioVersion->getNomenclaturaFormulario() . " " . $formularioVersion->getFechaVersion()->format("Y-m-d") . " " . $formularioVersion->getVersion();
        //Fecha y hora de registro
        //Fecha y hora de registro
        $fechaRegistro = $registro->getFechaHora();

        $tipoCorrespondencia = $em->getRepository(TipoCorrespondencia::class)->findOneById($registro->getTipoCorrespondencia());
        if ($tipoCorrespondencia->getId() != $tipoCorrespondenciaNA) {
            $info["cabecera"]["tipocorrespondencia"] = "Correspondencia: " . $tipoCorrespondencia->getNombre();
            $info["cabecera"]["consecutivocorrespondencia"] = "Consecutivo: " . $registro->getConsecutivo();
        }

        //Detalle de radicado
        $info["cabecera"]["fecha"] = "Fecha: " . $fechaRegistro->format("Y-m-d H:i:s");
        $info["cabecera"]["sede"] = "Sede: " . $registro->getSede();
        $info["cabecera"]["empresa"] = "Empresa: " . $empresa;
        $info["cabecera"]["radicado"] = "Radicado: " . $formularioVersion->getId() . " - " . $sedeProcesoUsuarioRegistro->getId() . " - " . $registro->getId();
        $info["cabecera"]["usuario"] = "Usuario: " . $usuarioRegistro->getLogin();
        $info["camposFormulario"] = $stickerValues;
        $infoFormated = self::FormatInfo($info);
        //Uno la información del los valores del sticker con la cabecera y la guardo en $info
        switch ($type) {
            case "qr":
                $sticker = array("tipo" => "qr", "code" => self::GenerateQRBarcode($infoFormated, "qrcode", 50, 50));
                break;
            case "barras":
                ob_start();
                $base64BarCodeImage = self::GenerateQRBarcode($formularioVersion->getId() . " - " . $sedeProcesoUsuarioRegistro->getId() . " - " . $registro->getId(), "c128", 2, 100);
                $base64StickerDetails = self::generarImagen($infoFormated, "barras");
                $src = imagecreatefromstring(base64_decode($base64BarCodeImage));
                $dest = imagecreatefromstring(base64_decode($base64StickerDetails));
                imagecopymerge($dest, $src, 10, 9, 0, 0, 350, 180, 100);
                imagepng($dest);
                $sticker = array("tipo" => "barras", "code" => base64_encode(ob_get_clean()));
                break;
            case "impreso":
                //$response = self::GetStickerValues($em, $registro);
                $imagen = self::generarImagen($infoFormated);
                $sticker = array("tipo" => "impreso", "code" => $imagen);
                //consulto por formulario_id el tipo de sticker correspondiente al registro
                break;
            case "radicadoelectronico":
                return self::generateRadicadoElectronico($infoFormated);

                break;
            case "ninguno":
                $response = array();
                $response = array(
                    "response" => array(
                        "detalle_campo" => array(
                            "nombre" => "Mensaje",
                            "valor" => "El registro no tiene sticker asignado",
                        ),
                    ),
                );
                $imagen = self::generarImagen($response);
                return array("response" => array("tipo" => "impreso", "code" => $imagen));
                //consulto por formulario_id el tipo de sticker correspondiente al registro
                break;

        }

        return array("response" => $sticker);
    }

    protected static function FormatInfo($info)
    {
        //convertir arreglo de información a una cadena de texto
        //formatear cabecera
        $cabecera = "";
        $detalle = "";
        $cabecera = implode("\n", $info["cabecera"]);
        //formatear campos marcados para verse en sticker
        foreach ($info["camposFormulario"] as $detalleCamposFormulario) {
            if ($detalleCamposFormulario["detalle_campo"]["valor"] instanceof \DateTime) {
                $detalle .= "\n" . $detalleCamposFormulario["detalle_campo"]["valor_cuadro_texto"] . ": " . $detalleCamposFormulario["detalle_campo"]["valor"]->format("Y-m-d");
            } else {
                $detalle .= "\n" . $detalleCamposFormulario["detalle_campo"]["valor_cuadro_texto"] . ": " . $detalleCamposFormulario["detalle_campo"]["valor"];
            }

        }
        return $cabecera . $detalle;
    }

    protected static function GenerateQRBarcode($info, $tipoSticker, $width, $height)
    {
        $generator = new Generator();
        $options = array(
            'code' => $info,
            'type' => $tipoSticker,
            'format' => 'png',
            'width' => $width,
            'height' => $height,
            'color' => array(0, 0, 0),
        );
        return $generator->generate($options);

    }

    protected static function GetStickerValues($em, $registro)
    {
        $camposFormularioVersion = $em->getRepository(CampoFormularioVersion::class)->findBy(array("formulario_version_id" => $registro->getFormularioVersion()->getId(), "imprime_sticker" => 1));
        $formularioVersion = $em->getRepository(FormularioVersion::class)->findOneById($registro->getFormularioVersionId());
        $response = array();
        foreach ($camposFormularioVersion as $campoFormularioVersion) {
            switch ($campoFormularioVersion->getTipoCampo()) {
                case "NumericoDecimal":
                    $manager = $em->getRepository(\App\Entity\RegistroNumericoDecimal::class);
                    break;
                case "NumericoEntero":
                    $manager = $em->getRepository(\App\Entity\RegistroNumericoEntero::class);
                    break;
                case "NumericoMoneda":
                    $manager = $em->getRepository(\App\Entity\RegistroNumericoEntero::class);
                    break;
                case "TextoCorto":
                    $manager = $em->getRepository(\App\Entity\RegistroTextoCorto::class);
                    break;
                case "TextoLargo":
                    $manager = $em->getRepository(\App\Entity\RegistroTextoLargo::class);
                    break;
                case "Fecha":
                    $manager = $em->getRepository(\App\Entity\RegistroFecha::class);
                    break;
                case "Hora":
                    $manager = $em->getRepository(\App\Entity\RegistroHora::class);
                    break;
                case "Booleano":
                    $manager = $em->getRepository(\App\Entity\RegistroBooleano::class);
                    break;
                case "FormularioVersion":
                    //Saber que campo formularioVersion tiene relacionado
                    $campoFormularioRelacionadoId = $campoFormularioVersion->getCampoFormularioId();
                    //Saber que campo formularioVersion es
                    $campoFormulario = $em->getRepository(CampoFormularioVersion::class)->findOneById($campoFormularioRelacionadoId);
                    //Saber que tipo de Campo es
                    $tipoCampo = $campoFormulario->getTipoCampo();

                    //Verificar que valores están relacionados con ese tipo de Campo
                    $queryBuilder = $em->createQueryBuilder();                    
                    $campoFormularioRelacionado = $queryBuilder
                        ->select("r.id, r.valor")
                        ->from('App\\Entity\\RegistroCampo', 'r')
                        ->where('r.registro_id = :registroid')
                        ->andWhere('r.campo_formulario_version_id = :campoformularioid')
                        ->setParameter('registroid', $registro->getId())
                        ->setParameter('campoformularioid', $campoFormularioVersion->getId())
                        ->getQuery()
                        ->execute();           
                    $response[] = array(
                        "detalle_campo" => array(
                            "nombre" => $campoFormulario->getCampo(),
                            "valor" => $campoFormularioRelacionado[0]["valor"],
                            "valor_cuadro_texto" => $campoFormulario->getValorCuadroTexto(),
                        ),
                        "posicion_sticker" => $campoFormulario->getPosicionSticker(),
                    );

                    //Consultar que tipo de campo es
                    //Realizar la consulta de los datos relacionados con ese registro
                    break;
                case "Multiseleccion":
                    //saber que lista se selecciono
                    $campoFormularioId = $campoFormularioVersion->getId();
                    $campo = $campoFormularioVersion;
                    $registrosMultiseleccion = $em->getRepository(\App\Entity\RegistroMultiseleccion::class)->findBy(array("registro_id" => $registro->getId(), "campo_formulario_version_id" => $campoFormularioId));
                    $valor = array();
                    foreach ($registrosMultiseleccion as $registroMultiseleccion) {
                        $valor[] = $registroMultiseleccion->getDetalleLista()->getDescripcion();
                    }

                    $response[] = array(
                        "detalle_campo" => array(
                            "nombre" => $campo->getCampo(),
                            "valor" => implode(", ", $valor),
                            "valor_cuadro_texto" => $campo->getValorCuadroTexto(),
                        ),
                        "posicion_sticker" => $campo->getPosicionSticker(),
                    );
                    break;
                case "Opcion":
                case "Lista":
                    $campoFormularioId = $campoFormularioVersion->getId();
                    $registroLista = $em->getRepository(\App\Entity\RegistroLista::class)->findOneBy(array("registro_id" => $registro->getId(), "campo_formulario_version_id" => $campoFormularioId));
                    //saber que lista se selecciono
                    $campo = $registroLista->getCampoFormularioVersion()->getCampo();
                    $valor = $registroLista->getDetalleLista()->getDescripcion();
                    $response[] = array(
                        "detalle_campo" => array(
                            "nombre" => $campo,
                            "valor" => $valor,
                            "valor_cuadro_texto" => $campoFormularioVersion->getValorCuadroTexto(),
                        ),
                        "posicion_sticker" => $campoFormularioVersion->getPosicionSticker(),
                    );
                    break;
            }
            if (isset($manager)) {
                $entidad = $manager->findOneBy(array("campo_formulario_version_id" => $campoFormularioVersion->getId(), "registro_id" => $registro->getId()));
                if (isset($entidad)) {
                    $response[] = array(
                        "detalle_campo" => array(
                            "nombre" => $campoFormularioVersion->getCampo(),
                            "valor" => $entidad->getValor(),
                            "valor_cuadro_texto" => $campoFormularioVersion->getValorCuadroTexto(),
                        ),
                        "posicion_sticker" => $campoFormularioVersion->getPosicionSticker(),
                    );
                    foreach ($response as $clave => $fila) {
                        $detalleCampo[$clave] = $fila['detalle_campo'];
                        $posicionSticker[$clave] = $fila['posicion_sticker'];
                    }
                    array_multisort($posicionSticker, SORT_ASC, $response);

                }
            }
            // $response["cabecera"]["nombreformulario"] = $formularioVersion->getNombre();

        }
        return $response;
    }

    protected function generateRadicadoElectronico($response)
    {
        //Armar PDF
        //recibir imagen en base64

        //enviarlo a Google Drive
        //Retornar el ID de consulta al igual que como se hace con las fotos de usuario
        if ($response != "") {
            $html = '<html><style>html { margin: 111px}</style><body>' . nl2br($response) . '</body></html>';
            $dompdf = new Dompdf();
            $dompdf->load_html($html);
            $dompdf->setPaper('letter', 'portrait');

            $dompdf->render();
            // Si ya cuenta con una radicación por el momento no la voy a modificar
            // Si ya se requiere que cada vez que se imprima se radique de nuevo quito la logica de verificación
            // $response = new Response();

            // $response->setContent($dompdf->stream(TextUtils::slugify("rd_" . $registro->getId() . "_formulario_" . $registro->getFormularioVersion()->getNombre()) . '-' . date("Ymdhis") . '.pdf'));
            // $response->setStatusCode(200);
            // $response->headers->set('Content-Type', 'application/pdf');
            // return $response;
            return $dompdf->output();
        } else {
            return array("response" => "Registro de formularioVersion no encontrado");
        }
    }

    protected static function generarImagen($response, $type = null)
    {
        $responseRows = explode("\n", $response);
        $alturaimagen = (count($responseRows) - 1) * 25;
        $alturaprimerrenglon = 0;
        if ($type == "barras") {
            $alturaimagen += 100;
            $alturaprimerrenglon = 100;
        }

        $text = "";
        $font = $_ENV['GDFONTPATH'];
        $alturarenglon = 20;
        $altura = 0;
        ob_start();
        $img = imagecreatetruecolor(350, $alturaimagen);
        $textbgcolor = imagecolorallocate($img, 255, 255, 255);
        $textcolor = imagecolorallocate($img, 0, 0, 0);
        imagefilledrectangle($img, 0, 0, 350, $alturaimagen, $textbgcolor);
        foreach ($responseRows as $field) {
            $altura++;
            // $text = $field["detalle_campo"]["nombre"] . ": " . $field["detalle_campo"]["valor"];
            imagettftext($img, 10, 0, 10, ($altura * $alturarenglon) + $alturaprimerrenglon, $textcolor, $font, $field);
        }
        imagepng($img);
        return base64_encode(ob_get_clean());
    }
}
