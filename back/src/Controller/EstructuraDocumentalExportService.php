<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\EstructuraDocumental;
/**
 * Undocumented class
 */
class EstructuraDocumentalExportService
{
    private $_em;
    private $estructura_documental =  array();
    private $recorridos =  array();
    private $export;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->entidad = "EstructuraDocumental";

    }

    

    private function Create_string(EstructuraDocumental $ed)
    {
        $st = $ed->getId()."," 
            . $ed->getCodigoDirectorioPadre()."," 
            . $ed->getCodigoDirectorio().","
            . $ed->getDescripcionSimple().","
            . $ed->getIdestructura().","
            . strval($ed->getEstadoId()).","
            . $ed->getType().","
            . $ed->getPeso().","
            . $ed->getVersion().","
            . $ed->getHasChange().","
            . $ed->getFormularioId();
        return $st;
    }

    private function SearchChild(EstructuraDocumental $node){
        $hijos = $this->em->getRepository(EstructuraDocumental::class)->findBy(["codigo_directorio_padre"=>$node->getCodigoDirectorio(),"estado_id" => 1,], ['codigo_directorio_padre' => 'ASC', 'codigo_directorio' => 'ASC', 'peso' => 'ASC']);
        return $hijos; 
    }


    /**
     * CrearObjetoUsuario function
     * 
     * @param string $request
     *
     * @return Usuario
     */
    public function Export(Request $request)
    {
        $entityColumns = $this->em->getClassMetadata("App\Entity\\" . $this->entidad)->getFieldNames();
        $entityColumns = implode(",", $entityColumns) . PHP_EOL;
        $estructura = $this->em->getRepository(EstructuraDocumental::class)->findOneById(1);
        array_push($this->estructura_documental,$estructura);

        while (sizeof($this->estructura_documental)>0){
            $ed = $this->estructura_documental[0];
            array_push($this->recorridos,$ed);
            if ($ed->getType() != "tipo_documental") {
                $hijos = $this->SearchChild($ed);
                array_shift($this->estructura_documental);
                $this->estructura_documental= array_merge($hijos,$this->estructura_documental);                
            }else{
                array_shift($this->estructura_documental);
            }
        }
        foreach( $this->recorridos as $node){
            $this->export .=  $this->Create_string($node)."\n";
        }
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=$this->entidad.csv");
        header('Content-Transfer-Encoding: binary');
        $contents = $entityColumns . rtrim($this->export, PHP_EOL);     
        exit($contents);
    }

}
