<?php

namespace App\Controller\Usuario;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Undocumented class
 */
class ComponentesByUsuarioService
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
     * @return Array
     */
    public function find(Request $request): array
    {
        $id = $request->attributes->get('id');
        $query = $request->query->get('query');
        $usuario = $this->em->getRepository(Usuario::class);
        $usuario = $usuario->findOneById($id);
        $roles = $usuario->getRols();
        $componentesArray = array();
        foreach ($roles as $rol) {
            $componentes = $rol->getComponentes();
            foreach ($componentes as $componente) {
                if ($query == "") {
                    $cadena_existe = true;
                } else {
                    $cadena_existe = strpos(strtolower($componente->getNombre()), strtolower($query));
                }
                if ($cadena_existe !== false) {
                    if ($componente->getTipoComponente() == 1) {
                        $componentesArray[] = array(
                            "id" => $componente->getId(),
                            "nombre" => $componente->getNombre(),
                            "link" => $componente->getLink(),
                            "ayuda" => $componente->getAyuda(),
                        );
                    }

                }
            }
        }
        $resultado = array_map("unserialize", array_unique(array_map("serialize", $componentesArray)));
        if (!empty($resultado)) {
            // Obtener una lista de columnas
            foreach ($resultado as $clave => $fila) {
                $nombre[$clave] = $fila['nombre'];
            }
            array_multisort($nombre, SORT_ASC, $resultado);
        }
        return $resultado;
    }
}
