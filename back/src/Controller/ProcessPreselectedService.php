<?php

namespace App\Controller;

use App\Entity\Cargo;
use App\Entity\Grupo;
use App\Entity\Proceso;
use App\Entity\Usuario;
use App\Utils\StickerGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class ProcessPreselectedService
{
    private $_em;

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
     * @param string $request
     *
     * @return Usuario
     */
    public function process(Request $request)
    {
        /*
        Cada vez que se carguen datos en el combo Preseleccionados se debe enviar al Back una
        estructura que identifique el tipo de preselección (Grupo, Cargo, Usuario, Proceso) y
        los datos seleccionados. Se debe construir un JSON de la siguiente manera:
        {
        "type": "grupo",
        "selected":[
        "1",
        "2",
        "3"
        ]
        }
        {
        "type": "cargo",
        "selected":[
        "1",
        "2",
        "3"
        ]
        }
        {
        "type": "usuario",
        "selected":[
        "1",
        "2",
        "3"
        ]
        }
        {
        "type": "proceso",
        "selected":[
        "1",
        "2",
        "3"
        ]
        }

        El endpoint a construir seria asi: /api/notificacions/preseleccionados/process
         */
        $data = json_decode($request->getContent());
        switch ($data->{"type"}) {
        case "grupo":
            $manager = $this->em->getRepository(Grupo::class);
            break;
        case "cargo":
            $manager = $this->em->getRepository(Cargo::class);
            break;
        case "usuario":
            $manager = $this->em->getRepository(Usuario::class);
            break;
        case "proceso":
            $manager = $this->em->getRepository(Proceso::class);
            break;
        }
        $selectedIds = $data->{"selected"};
        $selectedElements = array();
        foreach ($selectedIds as $selectedId) {
            $result = $manager->findOneById($selectedId);
            if (null !== $result) {
                $selectedElements[] = $result;
            }
        }
        /*
        La respuesta sería esta:

        [
        { id:20, nombreusuario:"Asdrubal Gutierrez", proceso:"Sistemas" },
        { id:2, nombreusuario:"Carlos Pérez", proceso:"Sistemas" },
        { id:1, nombreusuario:"Pepito Pérez", proceso:"Contabilidad" }
        ]
         */
        $response = array();
        switch ($data->{"type"}) {
        case "proceso":
        case "grupo":
        case "cargo":
            foreach ($selectedElements as $selectedElement) {
                $usuarios = $selectedElement->getUsuarios();
                foreach ($usuarios as $usuario) {
                    $response[] = array("id" => $usuario->getId(), "nombreusuario" => $usuario->getNombre1() . " " . $usuario->getNombre2() . " " . $usuario->getApellido1() . " " . $usuario->getApellido2());
                }
            }
            break;
            break;
        case "usuario":
            foreach ($selectedElements as $usuario) {
                    $response[] = array("id" => $usuario->getId(), "nombreusuario" => $usuario->getNombre1() . " " . $usuario->getNombre2() . " " . $usuario->getApellido1() . " " . $usuario->getApellido2());                
            }
            break;
        }
        return $response;
    }
}
