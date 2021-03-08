<?php

namespace App\Controller;

use DateTime;
use App\Entity\Caissier;
use App\Entity\AdminSystem;
use App\Repository\DepotRepository;
use App\Repository\ComptesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DepotController extends AbstractController {
    private $manager;
    private $serializer;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->serializer = $serializer;
    }


    

    /**
     * @Route("api/depots", name="depotzer", methods={"post"})
    */
    public function depot(Request $request, ComptesRepository $compteRepo,TokenStorageInterface $tokenStorage)
    {
        $depot = $request->getContent();
        $depot = $this->serializer->decode($depot, "json");
        $montant = $depot['montant'];
        $compte = $depot['compte'];
        unset($depot['compte']);
        $depot = $this->serializer->denormalize($depot, "App\Entity\Depot");
        if (!$compte) {
            return $this->json(["message" => "un depot doit etre obligatoirement avoir un compte wesh !!!"],Response::HTTP_FORBIDDEN);
        }
        $compteFind = $compteRepo->findOneBy(["numeroCompte" => $compte]);
        if (!$compteFind) {
            return $this->json(["message" => "compte erroné"],Response::HTTP_FORBIDDEN);
        }
        $depot->setCompte($compteFind);
        $now = new DateTime();
        $compteFind->setUpdatedAt($now);
        $compteFind->setSolde($compteFind->getSolde() + $montant);
        $autor = $tokenStorage->getToken()->getUser();
        if ($autor instanceof Caissier) {
            $depot->setCaissier($autor);
        }
        elseif ($autor instanceof AdminSystem) {
            $depot->setAdminSystem($autor);
        }
        else{
            return $this->json(["message" => "Imposteur va"],Response::HTTP_FORBIDDEN);
        }
        $this->manager->persist($depot);
        $this->manager->flush();
        return new JsonResponse(Response::HTTP_CREATED);
    }

    /**
     * @Route("api/depots/users", name="userDepots", methods={"get"})
    */
    public function getDepots(TokenStorageInterface $tokenStorage, DepotRepository $depo)
    {
        $author = $tokenStorage->getToken()->getUser();
        //   dd($author);
        //   dd($author instanceof AdminAgence);
          if (!($author instanceof AdminSystem || $author instanceof Caissier)) {
              return $this->json(["message" => "Imposteur va"],Response::HTTP_FORBIDDEN);
          }

        $depots = $author->getDepots();
        return $this->json($depots);
    }

}   