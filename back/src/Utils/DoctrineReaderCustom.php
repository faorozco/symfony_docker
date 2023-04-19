<?php

namespace App\Utils;

use Port\Doctrine\DoctrineReader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query;

class DoctrineReaderCustom extends DoctrineReader
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $objectName;

    /** @var QueryBuilder */
    protected $queryBuilder;

    /**
     * @param ObjectManager $objectManager
     * @param string $objectName e.g. YourBundle:YourEntity
     */
    public function __construct(ObjectManager $objectManager, $objectName, $hydrationMode = Query::HYDRATE_OBJECT)
    {
        $this->objectManager = $objectManager;
        $this->objectName = $objectName;
        $this->hydrationMode = $hydrationMode;
    }

    protected function getQueryBuilder()
    {
        if ($this->queryBuilder === null) {
            $this->queryBuilder = $this->objectManager->createQueryBuilder()
                ->from($this->objectName, 'o')
                ->where("o.estado_id=1");
        }

        return clone $this->queryBuilder;
    }

    public function rewind(): void
    {
        $select = "o";
        switch ($this->objectName) {
            case "App:Tercero":
                $select = "o.id,o.identificacion,o.nombre,o.direccion,o.telefono,o.celular,o.ciudad_id,o.estado_id,o.correo_electronico";
                break;
            case "App:Contacto":
                $select = "o.id,o.nombre,o.tratamiento,o.telefono_fijo,o.celular,o.correo,o.comentario,o.tercero_id,o.ciudad_id,o.tipo_contacto_id,o.estado_id,o.cargo";
                break;
            case "App:Proceso":
                $select = "o.id,o.nombre,o.codigo_interno,o.direccion_oficina,o.extension,o.telefono_fijo_oficina,o.sede_id";
                break;
            case "App:TablaRetencion":
                $select = "o.id,o.estructura_documental_id,o.version,o.fecha_version,o.tiempo_retencion_archivo_gestion,o.unidad_retencion_archivo_gestion,o.tiempo_retencion_archivo_central,o.unidad_retencion_archivo_central,o.tipo_soporte,o.disposicion_final_borrar,o.disposicion_final_conservacion_total,o.disposicion_final_conservacion_digital,o.disposicion_final_microfilmado,o.disposicion_final_seleccion,o.disposicion_final_digitalizacion_microfilmacion,o.disposicion_final_migrar,o.procedimiento_disposicion,o.ley_normatividad,o.inicio_vigencia,o.transferencia_medio_electronico,o.direccion_documentos_almacenados_electronicamente,o.estado_id,o.has_change";
                break;
            case "App:Cargo":
                $select = "o.id,o.nombre,o.estado_id";
                break;
        }
        if (!$this->iterableResult) {
            $query = $this->getQueryBuilder()->select($select)->getQuery();

            $this->iterableResult = $query->iterate([], $this->hydrationMode);
        }

        $this->iterableResult->rewind();
    }
   
}

