<?php
namespace App\EventListener;

use App\Entity\EstructuraDocumental;
use App\Entity\TablaRetencion;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseActivitySubscriber implements EventSubscriber
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->logActivity('persist', $args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->logActivity('remove', $args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->logActivity('update', $args);
    }

    private function logActivity(string $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this subscriber only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if ($entity instanceof TablaRetencion) {
            if ($action == "update" || $action == "persist") {
                $this->em->getRepository(TablaRetencion::class)->markUpdated($entity->getId());
            }
        } else if ($entity instanceof EstructuraDocumental) {
            if ($action == "update" || $action == "persist") {
                $this->em->getRepository(EstructuraDocumental::class)->markUpdated($entity->getId());
            }
        }

        // ... get the entity information and log it somehow
    }
}