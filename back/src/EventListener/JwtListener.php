<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hydra;
use App\Entity\Usuario;
use App\Entity\Usersfakes;
use App\Utils\Auditor;

class JwtListener 
{
    private $requestStack;
    private $em;
    static $userActive;
    static $allowedToken;
    static $daysExpiration;
    static $confirmeLicensed;
    static $bloqueo;
    static $hydra;
    static $hydra_id;
    private $client;
    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack,EntityManagerInterface $entityManager,HttpClientInterface $client)
    {
        $this->requestStack = $requestStack;
        $this->em = $entityManager;
        $this::$hydra_id =  $_ENV['LICENSED'];
        $this->client = $client;
    }

    public function onJWTCreated(JWTCreatedEvent $event){
        $this::$hydra = $this->em->getRepository(Hydra::class)->findOneBy(['l_id' => $this::$hydra_id]);
        $this::$bloqueo = false;
        $request = $this->requestStack->getCurrentRequest();
        $payload       = $event->getData();
        $payload['ip'] = $request->getClientIp();
        
        if($this->initCreatedToken($this::$hydra)){
            if(property_exists($event->getUser(), 'id')){
                $user = $this->em->getRepository(Usuario::class)->findOneById($event->getUser()->getId());
                $auditor = new Auditor();
                $auditor->login($this->em, $event->getUser());
            }else{
                $user = $this->em->getRepository(Usuario::class)->findOneBy(['login' => $event->getUser()->getUsername()]);
            }
            if($user->getBloqueo() || !$user->getEstadoId()){
                $this::$allowedToken = false;
                $this::$bloqueo = true;
                return;
            }
            $this::$userActive = $user->getActiveSesion()? true : false;
            $payload['roles'] = $this->getRoles($user);
            $payload['grupos'] = $this->getGrupos($user);
            $event->setData($payload);
            $max = $this::$hydra->getMax();
            $act = $this::$hydra->getActual();
            if($max > $act||($max == $act && $this::$userActive)){
                $this::$allowedToken  = true;
                if(!$this::$userActive){
                    $sesion = $this::$hydra->getActual();
                    if($sesion<0){
                        $sesion=0;
                    }else{
                        $sesion++;
                    }
                    $this::$hydra->setActual($sesion);
                }
                $user->setTry(0);
                $user->setTokenValidAfter(new \DateTime());
                $user->setActiveSesion(true);
                $this->em->persist($this::$hydra);
                $this->em->persist($user);
                $this->em->flush();
            }
        }else{
            $this::$allowedToken = false;
        }
        

    } 


    public function onJWTDecoded(JWTDecodedEvent $event)
    {   
        $payload = $event->getPayload();
        $user = $this->em->getRepository(Usuario::class)->findOneBy([
            'login' => $payload['username']
        ]);
        if (
            $user &&
            $user->getTokenValidAfter() instanceof \DateTime &&
            $payload['iat'] < $user->getTokenValidAfter()->getTimestamp()
        ) {
            $event->markAsInvalid();
        }
    }



    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        if($this::$allowedToken){
            $event->setData([
                'code' => $event->getResponse()->getStatusCode(),
                'activeSesion' => $this::$userActive,
                'token'=> $event->getData()['token'],
                'licenseConfirm'=> $this::$confirmeLicensed,
                'allowedLicensed' => true
            ]);
        }else{
            $event->setData([
                'code' => $event->getResponse()->getStatusCode(),
                'activeSesion' => $this::$userActive,
                'licenseConfirm'=> $this::$confirmeLicensed,
                'allowedLicensed' => false,
                'bloqueo' => $this::$bloqueo
            ]);
        }
        
    }

    public function onJWTExpired(JWTExpiredEvent $event)
    {
        
        $response = $event->getResponse();
        $response->setStatusCode(426);
        $response->setMessage('Token expirado, debes actulizar!');
    }

    public function initCreatedToken($hydra){
        $today = new \DateTime();
        if(!$hydra){
            $createHydra = new Hydra();
            $response = $this->client->request(
                'POST',
                'https://us-central1-gdocumentlicense.cloudfunctions.net/license/api/id/',[
                 'body' => [
                     'key' => $this::$hydra_id
                 ]
                ]
            );
            $statusCode = $response->getStatusCode();
            $content = $statusCode != 200 ? ['ok' => false] : $response->toArray();
            $createHydra->setLastDateConfirm($today);
            $createHydra->setLastDateConfirmServer($today);
            $this::$daysExpiration = $today;
            $createHydra->setLId($this::$hydra_id);
            $createHydra->setActual(0);
            if($content['ok']){
                $content = $content['license'];
                $createHydra->setMax($content['numberLicense']);
                $createHydra->setStatus($content['statusLicense']);
                $createHydra->setStatusLocal(true);
                $createHydra->setMf2a($content['mf2A']);
                $createHydra->setCaptcha($content['mf2A']);
                $this::$confirmeLicensed = true;
            }else{
                $createHydra->setMax(5);
                $createHydra->setStatusLocal(false);
                $createHydra->setStatus(true);
                $createHydra->setMf2a(false);
                $createHydra->setCaptcha(false);
                $this::$confirmeLicensed = false;
            }
            $this->em->persist($createHydra);
            $this->em->flush();
            $this::$hydra = $this->em->getRepository(Hydra::class)->findOneBy(['l_id' => $this::$hydra_id]);
            return true;
        }else if(!$this::$hydra->getStatus()){
            $response = $this->client->request(
                'POST',
                'https://us-central1-gdocumentlicense.cloudfunctions.net/license/api/id/',[
                 'body' => [
                     'key' => $this::$hydra_id
                 ]
                ]
            );
            $statusCode = $response->getStatusCode();
            $content = $statusCode != 200 ? ['ok' => false, 'license' =>[ 'statusLicense' => false ] ] : $response->toArray();
            if($content['license']['statusLicense']){
                $this::$hydra->setLastDateConfirmServer($today);
                $content = $content['license'];
                $this::$hydra->setMax($content['numberLicense']);
                $this::$hydra->setStatus($content['statusLicense']);
                $this::$hydra->setStatusLocal(true);
                $this::$hydra->setActual(0);
                $this::$hydra->setLastDateConfirm($today);
                $this::$hydra->setMf2a($content['mf2A']);
                $this::$hydra->setCaptcha($content['mf2A']);
                $this::$confirmeLicensed = true;
                $this->em->persist($this::$hydra);
                $this->em->flush();
                $this::$hydra = $this->em->getRepository(Hydra::class)->findOneBy(['l_id' => $this::$hydra_id]);
                return true;
            }
            return false;
        }else if($this::$hydra->getLastDateConfirmServer()->diff($today)->d >= 15){
            $response = $this->client->request(
                'POST',
                'https://us-central1-gdocumentlicense.cloudfunctions.net/license/api/id/',[
                 'body' => [
                     'key' => $this::$hydra_id
                 ]
                ]
            );
            $statusCode = $response->getStatusCode();
            $content = $statusCode != 200 ? ['ok' => false] : $response->toArray();
            $this::$daysExpiration = $today;
            $this::$hydra->setActual(0);
            $this::$hydra->setLastDateConfirm($today);
            if($content['ok']){
                $this::$hydra->setLastDateConfirmServer($today);
                $content = $content['license'];
                $this::$hydra->setMax($content['numberLicense']);
                $this::$hydra->setStatus($content['statusLicense']);
                $this::$hydra->setStatusLocal(true);
                $this::$hydra->setMf2a($content['mf2A']);
                $this::$hydra->setCaptcha($content['mf2A']);
                $this::$confirmeLicensed = true;
            }else{
                $this::$hydra->setStatusLocal(false);
                $this::$hydra->setStatus(false);
                $this::$confirmeLicensed = false;
            }
            $this->em->persist($this::$hydra);
            $this->em->flush();
            $this::$hydra = $this->em->getRepository(Hydra::class)->findOneBy(['l_id' => $this::$hydra_id]);
            return true;
        }else if($this::$hydra->getLastDateConfirm()->diff($today)->d >= 1){ 
            $response = $this->client->request(
                'POST',
                'https://us-central1-gdocumentlicense.cloudfunctions.net/license/api/id/',[
                'body' => [
                    'key' => $this::$hydra_id
                ]
                ]
            );
            $statusCode = $response->getStatusCode();
            $content = $statusCode != 200 ? ['ok' => false] : $response->toArray();
            $this::$daysExpiration = $today;
            $this::$hydra->setActual(0);
            $this::$hydra->setLastDateConfirm($today);
            if($content['ok']){
                $this::$hydra->getLastDateConfirmServer($today);
                $content = $content['license'];
                $this::$hydra->setMax($content['numberLicense']);
                $this::$hydra->setStatus($content['statusLicense']);
                $this::$hydra->setStatusLocal(true);
                $this::$hydra->setMf2a($content['mf2A']);
                $this::$hydra->setCaptcha($content['mf2A']);
                $this::$confirmeLicensed = true;
            }else{
                $this::$hydra->setStatusLocal(false);
                $this::$confirmeLicensed = false;
            }
            $this->em->persist($this::$hydra);
            $this->em->flush();
            $this::$hydra = $this->em->getRepository(Hydra::class)->findOneBy(['l_id' => $this::$hydra_id]);
            return true;
        }else{
            return true;
        }
        
    }


    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event){
        $userName = $event->getException()->getToken()->getUser();
        $user = $this->em->getRepository(Usuario::class)->findOneBy(['login' => $userName]);
        $bloqueo = false;
        if($user){
            $tries = intval($user->getTry());
            if($tries<3){
                $user->setTry($tries+1); 
            }else{
                $user->setBloqueo(1); 
                $bloqueo= true;
            };
            $this->em->persist($user);
            $this->em->flush();
        }else{
            $userFake = $this->em->getRepository(Usersfakes::class)->findOneBy(['login' => $userName]);
            if($userFake){
                $tries = intval($userFake->getTry());
                if($tries<3){
                    $userFake->setTry($tries+1); 
                }else{
                    $userFake->setBloqueo(1); 
                    $bloqueo= true;
                };
                
            }else{
                $userFake = new usersfakes();
                $userFake->setLogin($userName);
                $userFake->setTry(1);
                $userFake->setBloqueo(0);

            }
            $this->em->persist($userFake);
            $this->em->flush();
        }
        $data = [
            'status'  => '401 Unauthorized',
            'message' => 'Bad credentials',
            'bloqueo' => $bloqueo,
            'code' => 401
        ];
        $response = new JsonResponse($data, 401);
        $event->setResponse($response);

    }


    private function getGrupos(Usuario $user){
        $grupos = $user->getGrupos()->getValues();
        $groups = [];
        foreach ($grupos as $grupos){
            array_push($groups, $grupos->getId());
        }   
        return $groups;

    }

    private function getRoles(Usuario $user){
        $roles = $user->getRols()->getValues();
        $rols = [];
        foreach ($roles as $rol){
            array_push($rols, $rol->getId());
        }   
        return $rols;

    }
        


} 