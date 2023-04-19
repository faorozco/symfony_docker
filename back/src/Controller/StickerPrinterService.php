<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Utils\Auditor;
use App\Entity\Registro;
use App\Utils\FileUtils;
use App\Utils\StickerGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class StickerPrinterService
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
    public function Get(Request $request)
    {
        $registro_id = $request->attributes->get("id");
        $registro = $this->em->getRepository(Registro::class)->findOneById($registro_id);
        $infoSticker = "";
        $location = $node = $request->query->get('location');
        $type = $request->query->get('type');
        $usuario = $this->tokenStorage->getToken()->getUser();

        //Registro Auditoria
        $valor_actual = array(
            "Tipo_Sticker" => $type,
        );
        Auditor::registerAction($this->em, $registro, $usuario, null, $valor_actual, "IMPRIMIR_STICKER");

        if (isset($registro)) {
            $sticker = StickerGenerator::print($this->em, $registro, $type);
            FileUtils::base64ToImage($sticker["response"]["code"], "sticker_" . $usuario->getLogin());
            $options = new Options();
            $options->set('isRemoteEnabled', TRUE);
            $options->set('isHtml5ParserEnabled', TRUE);
            $dompdf = new Dompdf($options);
            $path = $_ENV["PUBLIC_TMP_LOCATION"].'sticker_' . $usuario->getLogin().".jpg";
            $format = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $format . ';base64,' . base64_encode($data);
            $html = "";
            if ($_ENV['RADICADORA'] == "true") {
                switch ($type) {
                    case "qr":
                        $infoSticker .= '<img src="'.$base64.'" height="' . ($_ENV["RADICADORA_STICKER_IMAGE_HEIGHT"] - 10) . '"/>';
                        break;
                    case "barras":
                        $infoSticker .= '<img src="data:image/png;base64, ' . $sticker["response"]["code"] . '" height="' . $_ENV["RADICADORA_STICKER_IMAGE_HEIGHT"] . '"/>';
                        break;
                    case "impreso":
                        $infoSticker .= '<img src="data:image/png;base64, ' . $sticker["response"]["code"] . '"height="' . $_ENV["RADICADORA_STICKER_IMAGE_HEIGHT"] . '"/>';
                        break;
                    case "ninguno":
                        $infoSticker .= '<img src="data:image/png;base64, ' . $sticker["response"]["code"] . '"/>';
                        break;
                }
                $customPaper = array(0, 0, $_ENV["RADICADORA_STICKER_WIDTH"], $_ENV["RADICADORA_STICKER_HEIGHT"]);
                $dompdf->set_paper($customPaper);
                $html = '<html><style>html { margin: 2px; margin-left: 10px; }</style><body>' . $infoSticker . '</body></html>';
            } else {
                switch ($type) {
                    case "qr":
                        $infoSticker .= '<img src="'.$base64.'" width="' . $_ENV["PAPER_STICKER_IMAGE_WIDTH"] . '"/>';
                        break;
                    case "barras":
                        $infoSticker .= '<img src="data:image/png;base64, ' . $sticker["response"]["code"] . '" width="' . $_ENV["PAPER_STICKER_IMAGE_WIDTH"] . '" />';
                        break;
                    case "impreso":
                        $infoSticker .= '<img src="data:image/png;base64, ' . $sticker["response"]["code"] . '"width="' . $_ENV["PAPER_STICKER_IMAGE_WIDTH"] . '"/>';
                        break;
                    case "ninguno":
                        $infoSticker .= '<img src="data:image/png;base64, ' . $sticker["response"]["code"] . '"/>';
                        break;
                }
                $dompdf->setPaper('letter', 'portrait');
                $tabla = '<table  width="' . $_ENV["PAPER_WIDTH"] . '" height="' . $_ENV["PAPER_HEIGHT"] . '" border="0"> ' .
                    '<tr>
                                <td width="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" height="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" valign="top" align="left">---top_left---&nbsp;</td>
                                <td width="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" height="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" valign="top" align="center">---top_center---&nbsp;</td>
                                <td width="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" height="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" valign="top" align="right">---top_right---&nbsp;</td></tr>' .
                    '<tr>
                                <td width="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" height="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" valign="middle" align="left">---middle_left---&nbsp;</td>
                                <td width="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" height="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" valign="middle" align="center">---middle_center---&nbsp;</td>
                                <td width="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" height="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" valign="middle" align="right">---middle_right---&nbsp;</td>
                            </tr>' .
                    '<tr>
                                <td width="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" height="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" valign="bottom" align="left">---bottom_left---&nbsp;</td>
                                <td width="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" height="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" valign="bottom" align="center">---bottom_center---&nbsp;</td>
                                <td width="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" height="' . $_ENV["PAPER_GRID_IMAGE_HEIGHT"] . '" valign="bottom" align="right">---bottom_right---&nbsp;</td>
                            </tr>' .
                    '</table>';
                $tabla = str_replace($location, $infoSticker, $tabla);
                $tabla = preg_replace('/---[\s\S]+?---/', '', $tabla);
                $html = '<html><style>body { margin: 10px}</style><body>' . $tabla . '</body></html>';
            }

            //  echo $html;
            //  die;
            $dompdf->setBaseHost($_ENV["BASE_URL"]);
            $dompdf->load_html($html);

            $dompdf->render();
            //Guardo la fecha_hora de impresiòn del sticker
            if (null === $registro->getFechaSticker()) {
                $registro->setFechaSticker(new \DateTime());
            }
            //Construyo el atributo radicación

            //Este campo se calcula se la siguiente manera:

            // fechahora actual y se le concatena un consecutivo por año.

            // Este se puede calcular consultando el último registro almacenado
            // en la entidad registro y verificando si ya ha cambiado el año
            // basado en su campo radicacion. Si el año traido es menor al
            // actual se empieza de nuevo el consecutivo.
            if (null === $registro->getRadicacionYear() && null === $registro->getRadicacionCounter()) {
                $maxRegistro = $this->em->getRepository(Registro::class)->findMax();
                //si el registro traido no contiene ningun valor en este campo se
                // se construye la radicación aplicando la lógica necesaria
                // Formato a manejar YYYYMMddhhmmss-[consecutivo]
                $year = date("Y");
                if (null === $maxRegistro) {
                    $camporadicacionYear = date("Y");
                    $camporadicacionCounter = 1;
                } else if (null !== $maxRegistro) {
                    $camporadicacionYearActual = $maxRegistro->getRadicacionYear();
                    $camporadicacionCounterActual = $maxRegistro->getRadicacionCounter();
                    if ($camporadicacionYearActual < $year) {
                        $camporadicacionYear = $year;
                        $camporadicacionCounter = 1;
                    } else if ($camporadicacionYearActual == $year) {
                        $camporadicacionYear = $camporadicacionYearActual;
                        $camporadicacionCounter = ++$camporadicacionCounterActual;
                    }
                }
                $registro->setRadicacionYear($camporadicacionYear);
                $registro->setRadicacionCounter($camporadicacionCounter);
                $this->em->persist($registro);
                $this->em->flush();
            }
            // Si ya cuenta con una radicación por el momento no la voy a modificar
            // Si ya se requiere que cada vez que se imprima se radique de nuevo quito la logica de veriricación
            $response = new Response();

            $response->setContent($dompdf->stream('registro ' . $registro->getId() . ' ' . date("Ymdhis") . '.pdf'));
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'application/pdf');

            return $response;
        } else {
            return array("response" => "Registro de formulario no encontrado");
        }
    }
}
