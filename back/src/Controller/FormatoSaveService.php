<?php

namespace App\Controller;

use \DateTime;
use Dompdf\Dompdf;
use App\Utils\Auditor;
use App\Entity\Formato;
use App\Entity\Registro;
use App\Utils\TextUtils;
use App\Entity\PlantillaVersion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class FormatoSaveService
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
     * @return Formato
     */
    public function save(Request $request)
    {
        $registroRepository = $this->em->getRepository(Registro::class);
        $plantillaRepository = $this->em->getRepository(PlantillaVersion::class);
        
        $usuario = $this->tokenStorage->getToken()->getUser();
        $data = json_decode($request->getContent());
        $dataRegistro = explode("/", $data->{"registro"});
        $registro=$registroRepository->findOneById($dataRegistro[3]);
        //Guardado de la entidad
        $formato = new Formato();
        $formato->setTitulo($data->{"titulo"});
        $formato->setContenido($data->{"contenido"});
        $formato->setCuando(new DateTime($data->{"cuando"}));
        $formato->setPlantillaVersion($plantillaRepository->findOneById($data->{"plantillaId"})) ;
        $formato->setRegistro($registro);
        $formato->setEstadoId($data->{"estadoId"});
        $this->em->persist($formato);
        $this->em->flush();
        $valor=array("titulo"=>$data->{"titulo"},"fecha"=>$data->{"cuando"});
        Auditor::registerAction($this->em, $registro, $usuario, null, $valor, "FORMATO GUARDADO");
        return $formato;     
        }
}
