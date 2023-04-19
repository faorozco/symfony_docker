<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Utils\UsuarioStandard;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Undocumented class
 */
class UserByLoginService
{
    private $_em;
    private $_notFoundException;
    private $_usuario;
    private $_login;
    private $_usuariosGrupos;
    private $_usuarioProcesos;
    private $_empresas;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $login
     *
     * @return UsuarioStandard
     */
    public function get(string $login)
    {
        $this->login = $login;
        if (self::_cargarObjetoUsuario() == "denegado") {
            $response = new Response(json_encode(array("code" => 401, "message" => "Invalid credentials.")), 401);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            self::_crearUsuarioStandard();
            self::_cargarDatosEmpresaUsuarioStandard();
            return $this->usuarioStandard;
        }
    }

    /**
     * _cargarObjetoUsuario function
     *
     * @return void
     */
    private function _cargarObjetoUsuario()
    {
        $this->usuario = $this->em->getRepository(Usuario::class)
            ->findOneBy(array("login" => $this->login, "estado_id" => 1));
        if (!$this->usuario) {
            return "denegado";
            //throw new NotFoundHttpException("Usuario no encontrado para login dado");
        }

    }

    /**
     * _crearUsuarioStandard function
     *
     * @return void
     */
    private function _crearUsuarioStandard()
    {
        $this->usuarioStandard = new UsuarioStandard();
        $this->usuarioStandard->setId($this->usuario->getId());
        $this->usuarioStandard->setLogin($this->usuario->getLogin());
        $this->usuarioStandard->setNombreCompleto(
            $this->usuario->getNombre1() . " " .
            $this->usuario->getNombre2() . " " .
            $this->usuario->getApellido1() . " " .
            $this->usuario->getApellido2()
        );
        // $roles = $this->usuario->getProceso()->getId();
        // foreach ($this->usuario->getRols() as $key => $value) {
        //     echo $value->getId();
        // }
        // // var_dump($roles);
        // die;
        $this->usuarioStandard->setRols($this->usuario->getRols());

        $this->usuarioStandard->setGrupos($this->usuario->getGrupos());

        $this->usuarioStandard->setProcesos($this->usuario->getProceso());
        if ($this->usuario->getImagen() != "") {
            $this->usuarioStandard->setUrlImagen("/api/usuarios/" . $this->usuario->getId() . "/imagen");
        } else {
            $this->usuarioStandard->setUrlImagen("./assets/fotoDefecto.jpg");
        }
    }

    /**
     *
     */
    private function _cargarDatosEmpresaUsuarioStandard()
    {
        $proceso = $this->usuario->getProceso();
        if (null !== $proceso) {
            $sede = $proceso->getSede();
            if (null !== $sede) {
                $this->empresa = array(
                    "id" => $sede->getEmpresa()->getId(),
                    "nombre" => $sede->getEmpresa()->getNombre(),
                );
                $this->usuarioStandard->setEmpresa($this->empresa);
            }
        } else if (null === $proceso) {
            $this->empresa = array(
                "id" => 0,
                "nombre" => "GlobalDoc",
            );
            $this->usuarioStandard->setEmpresa($this->empresa);
        }
    }
}
