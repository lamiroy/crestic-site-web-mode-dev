<?php
namespace App\EventSubscriber;

use App\Entity\Actualites;
use App\Entity\MembresCrestic;
use App\Entity\Projets;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityUpdatedEvent::class => ['setUpdateEquipeForMembre'],
            BeforeEntityPersistedEvent::class => ['setSlug'],
        ];
    }

    public function setSlug(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof MembresCrestic) &&
            !($entity instanceof Actualites) && !($entity instanceof Projets)) {
            return;
        }

        if ($entity instanceof MembresCrestic) {
            $slug = explode('@',$entity->getEmail());
            $entity->setSlug();
        }

        if ($entity instanceof Actualites) {
            $entity->setSlug($this->slugger->slug($entity->getTitre()));
        }

        if ($entity instanceof Projets) {
            $entity->setSlug($this->slugger->slug($entity->getTitre()));
        }
    }

    public function setUpdateEquipeForMembre(AfterEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof MembresCrestic)) {
            return;
        }

        if ($entity->getAncienMembresCrestic() === true) {
            foreach ($entity->getEquipesHasMembres() as $eqme)
            {
                $this->entityManager->remove($eqme);
            }
            $this->entityManager->flush();
        }
    }
}
