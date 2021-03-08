<?php
namespace App\DataPersister;

use App\Entity\Agence;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;

class AgencePersister implements DataPersisterInterface 
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports($data): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof Agence;
    }
    public function persist($data)
    {
    }
    public function remove($data)
    {
        if ($data->getIsActive() == true) {
            $data->setIsActive(false);
            foreach ($data->getAdmin() as $value) {
                $value->setIsActive(false);
            }
            foreach ($data->getUsersAgence() as $value) {
                $value->setIsActive(false);
            }
        }
        else{
            $data->setIsActive(true);
        }
        $this->entityManager->flush();
    }
}