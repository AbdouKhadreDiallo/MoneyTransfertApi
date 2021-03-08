<?php
namespace App\DataPersister;

use App\Entity\Comptes;
use App\Entity\Caissier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;

class CaissierPersister implements DataPersisterInterface 
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports($data): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof Caissier;
    }
    public function persist($data)
    {
    }
    public function remove($data)
    {
        if ($data->getIsActive() == true) {
            $data->setIsActive(false);
        }
        else{
            $data->setIsActive(true);
        }
        $this->entityManager->flush();
    }
}