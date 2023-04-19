<?php

namespace App\Repository;

use App\Entity\FlujoTrabajoVersion;
use App\Entity\Usuario;
use App\Entity\TablaRetencion;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;

class FlujoTrabajoVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlujoTrabajoVersion::class);
    }

    public function getFlujosAsociados($em, $formularioVersionId, $query, $order)
    {


        $sql = "SELECT f.configuraciones FROM opcion_formulario f
        INNER JOIN opcion_formulario_version ofv ON ofv.opcion_formulario_id = f.id
        INNER JOIN permite p ON p.id = f.permite_id
        WHERE ofv.formulario_version_id = ? AND p.nombre = 'Flujo de Trabajo';";

        $stmt = $em->getConnection()->prepare($sql);

        $args = [
            $formularioVersionId
        ];


        $stmt->execute($args);

        $result = $stmt->fetchAll();

        if (count($result) > 0) {
            $flujos = json_decode($result[0]['configuraciones'])->flujos;

            $flujosId = array();

            foreach($flujos as $flujo) {
                $flujosId[] = $flujo->id;
            }

            $orderQuery = "asc";
            if ($order === "desc") {
                $orderQuery = "desc";
            }

            $sql = "SELECT * FROM flujo_trabajo_version ftv
                    INNER JOIN (
                        SELECT ftv.flujo_trabajo_id, MAX(ftv.version) as version FROM flujo_trabajo_version ftv
                        INNER JOIN flujo_trabajo ft ON ft.id IN (" . implode(",", $flujosId) . ") AND ft.id = ftv.flujo_trabajo_id
                        WHERE ftv.nombre LIKE ? GROUP BY ftv.flujo_trabajo_id
                    ) ftv2
                    ON ftv.flujo_trabajo_id = ftv2.flujo_trabajo_id AND ftv.version = ftv2.version ORDER BY ftv.id " . $orderQuery . ";";


            $stmt = $em->getConnection()->prepare($sql);

            $args = [
                "%" . $query . "%"
            ];
            $stmt->execute($args);

            return $stmt->fetchAll();
        } else {
            return [];
        }
    }

    public function getFormulariosRelacionados($formularioId)
    {
        //Primero debo consultar con que formularios esta relacionado este formulario.
        //Esto se hace a través de sus campos
        //Consulto los campos del formulario

    }
}
