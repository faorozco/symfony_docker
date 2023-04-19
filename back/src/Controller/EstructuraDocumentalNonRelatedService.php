<?php

namespace App\Controller;

use App\Entity\EstructuraDocumental;
use App\Entity\TablaRetencion;
use App\Utils\EstructuraDocumentalStandard;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Undocumented class
 */
class EstructuraDocumentalNonRelatedService
{
    private $_em;
    private $_estructuraDocumentalStandard;
    private $_childNodes;

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
     * @return EstructuraDocumentalStandard
     */
    public function getNonRelated($request)
    {
        $query = $request->query->get('query');
        $estructuraDocumentalId = $request->query->get('estructuraDocumentalId');
        $listDocumentalStructure = $this->em->getRepository(EstructuraDocumental::class)->findNonRelated($this->em, $query, $estructuraDocumentalId);

        return $listDocumentalStructure;
    }
}
