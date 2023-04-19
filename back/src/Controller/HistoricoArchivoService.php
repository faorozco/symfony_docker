<?php

namespace App\Controller;

use App\Entity\Archivo;
use App\Entity\CampoFormularioVersion;
use App\Entity\FormularioVersion;
use App\Entity\Registro;
use App\Entity\RegistroEntidad;
use App\Entity\RegistroCampo;
use App\Entity\TipoCorrespondencia;
use App\Utils\Auditor;
use App\Utils\Gdrive;
use App\Utils\GestorArchivos;
use App\Utils\TextUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Undocumented class
 */
class HistoricoArchivoService
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
    public function save(Request $request)
    {

        //validar tamaño máximo de subida de archivo en el servidor.
        // estructura_formulario_id
        // registro_id
        // version
        // comentario
        // archivo a guardar
        //$data = json_decode($request->getContent());
        $data = json_decode($request->getContent());

        if (isset($data) && $data->{'archivoOrigen'} > 0) {
            $archivos = $this->em->getRepository(Archivo::class)->findBy(['archivo_origen' => $data->{'archivoOrigen'}, "estado_id" => 1], ['version' => 'DESC']);
        }

        return $archivos;
    }
}
