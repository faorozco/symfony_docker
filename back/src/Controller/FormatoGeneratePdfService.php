<?php

namespace App\Controller;

use App\Entity\Formato;
use App\Utils\Auditor;
use App\Utils\TextUtils;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use \DateTime;

/**
 * Undocumented class
 */
class FormatoGeneratePdfService
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
        $usuario = $this->tokenStorage->getToken()->getUser();
        $formato_id = $request->attributes->get("id");
        $formato = $this->em->getRepository(Formato::class)->findOneById($formato_id);
        if (isset($formato)) {
            $html = '<html><style>html { margin: 111px}</style><body><strong>' . $formato->getTitulo() . "</strong><br /><br />" . nl2br($formato->getContenido()) . '</body></html>';
            $dompdf = new Dompdf();
            $dompdf->load_html($html);
            $dompdf->setPaper('letter', 'portrait');

            $dompdf->render();
            // TODO: Si ya cuenta con una radicación por el momento no la voy a modificar
            // TODO: Si ya se requiere que cada vez que se imprima se radique de nuevo quito la logica de verificación
            $response = new Response();
            // Enviar PDF al navegador
            // $response->setContent($dompdf->stream(TextUtils::slugify($formato->getTitulo()) . '-' . date("Ymdhis") . '.pdf'));
            // $response->setStatusCode(200);
            // $response->headers->set('Content-Type', 'application/pdf');
            $output = $dompdf->output();
            $horaDescarga=new DateTime();
            $nombreArchivo = TextUtils::slugify($formato->getTitulo()) . '-' . $horaDescarga->format("Ymdhis") . '.pdf';
            $fileLocation = $_ENV['PUBLIC_TMP_LOCATION'] . $nombreArchivo;
            file_put_contents($fileLocation, $output);
            if (null === $formato->getFechaHoraImpresion()) {
                $formato->setFechaHoraImpresion(new \DateTime());
                $this->em->persist($formato);
                $this->em->flush();
            }

            $schema = $request->server->get("SYMFONY_DEFAULT_ROUTE_SCHEME");
            if ($schema == "") {
                $schema = "https";
            }
            $baseurl = $schema . '://' . $request->getHttpHost() . $request->getBasePath();
            $valor = array("Formato" => $nombreArchivo, "Fecha descarga" => $horaDescarga->format("Y-m-d H:i:s"));
            Auditor::registerAction($this->em, $formato->getRegistro(), $usuario, null, $valor, "FORMATO DESCARGADO");
            return array("response" => array("location" => $baseurl . "/tmp/" . TextUtils::slugify($formato->getTitulo()) . '-' . date("Ymdhis") . '.pdf'));
            //return $response;
        } else {
            return array("response" => "Registro de formulario no encontrado");
        }

    }
}
