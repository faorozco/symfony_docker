<?php

namespace App\Controller;

use App\Entity\Archivo;
use App\Entity\Formato;
use App\Utils\GestorArchivos;
use App\Utils\TextUtils;
use App\Utils\Auditor;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class StoreFormatService
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
    public function store(Request $request)
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

            $gestorArchivo = new GestorArchivos();
            $folder = date("Ymd");
            $fileLocation = $_ENV['TMP_LOCATION'] . TextUtils::slugify($formato->getTitulo()) . '-' . date("Ymdhis") . '.pdf';
            $output = $dompdf->output();
            file_put_contents($fileLocation, $output);
            $mime_type = mime_content_type($fileLocation);
            // $fileToUpload=new File($fileLocation);
            $fileToUpload = new UploadedFile($fileLocation, date("Ymdhis") . '.pdf', $mime_type, null, true);
            $result = $gestorArchivo->uploadFile($this->em, $fileToUpload, $folder, $_ENV['FILE_LOCATION']);
            $archivo = new Archivo();
            $fechaVersion = new DateTime();
            // se setean todos los valores
            $archivo->setRegistro($formato->getRegistro());
            $archivo->setVersion("1");
            $archivo->setFechaVersion($fechaVersion);
            $archivo->setComentario("Formato Archivado");
            $archivo->setEstadoId(1);
            $archivo->setNombre($fileToUpload->getClientOriginalName());
            $archivo->setIdentificador($result["gDriveFileSavedID"]);
            $archivo->setCarpeta($result["carpeta"]);
            $this->em->persist($archivo);
            //Se hace la relación entre formato y archivo
            $formato->setArchivo($archivo);
            $this->em->persist($formato);
            $this->em->flush();
            $valor = array("Formato" => $fileToUpload->getClientOriginalName(), "Fecha" => $fechaVersion->format("Y-m-d H:i:s"));
            Auditor::registerAction($this->em, $formato->getRegistro(), $usuario, null, $valor, "FORMATO ALMACENADO");
            
            return array("response" => array("message" => "Formato Archivado"));
            //return $response;File
        } else {
            return array("response" => "Formato no encontrado");
        }

    }
}
