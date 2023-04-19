<?php

namespace App\Controller;

use App\Entity\Hydra;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Undocumented class
 */
class ActiveLicenseService
{
    private $_em;
    private $client;

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager,HttpClientInterface $client)
    {
        $this->em = $entityManager;
        $this->client = $client;
    }
    /**
     * CrearObjetoUsuario function
     *
     * @param string $request
     *
     * @return Usuario
     */
    public function verified(Request $request)
    {
        $license_id = $_ENV['LICENSED'];
        $today = new \DateTime();
        $hydra = $this->em->getRepository(Hydra::class)->findOneBy(['l_id' => $license_id]);
        $response = $this->client->request(
            'POST',
            'https://us-central1-gdocumentlicense.cloudfunctions.net/license/api/id/',[
                'body' => [
                    'key' => $license_id
                ]
            ]
        );
        $statusCode = $response->getStatusCode();
        $content = $statusCode != 200 ? ['ok' => false] : $response->toArray();
        $hydra->setLastDateConfirm($today);
        $hydra->setLastDateConfirmServer($today);
        if($content['ok']){
            $content = $content['license'];
            $hydra->setMax($content['numberLicense']);
            $hydra->setStatus($content['statusLicense']);
            $hydra->setStatusLocal(true);
            $hydra->setMf2a($content['mf2A']);
            $hydra->setCaptcha($content['mf2A']);
            $this->em->persist($hydra);
            $this->em->flush();
            return ([
            'approved' => true
            ]);
        }            
        return ([
            'approved' => false
        ]);
    }



}
