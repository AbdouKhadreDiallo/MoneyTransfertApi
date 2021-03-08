<?php

namespace App\Controller;

use App\Entity\Comptes;
use App\Service\Shared;
use App\Entity\UserAgence;
use App\Entity\AdminAgence;
use App\Entity\AdminSystem;
use App\Repository\AgenceRepository;
use App\Repository\ComptesRepository;
use App\Repository\UserAgenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AgenceController extends AbstractController
{
    private $shared;
    private $manager;
    private $serializer;

    public function __construct(SerializerInterface $serializer,Shared $shared, EntityManagerInterface $manager)
    {
        $this->shared = $shared;
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/agence/{id}/users", name="usersAgence", methods={"get"})
    */
    public function usersAgence($id,TokenStorageInterface $tokenStorage, AgenceRepository $agenceRepo)
    {
        $author = $tokenStorage->getToken()->getUser();
        if (!($author instanceof AdminAgence)) {
            return $this->json(["message" => "Imposteur va"],Response::HTTP_FORBIDDEN);
        }
        
        $agenceEnCours = $agenceRepo->findOneBy(["id" => $id]);
        if (!$agenceEnCours) {
            return $this->json(["message" => "Agence not found"],Response::HTTP_NOT_FOUND);
        }
        foreach ($agenceEnCours->getAdmin() as  $value) {
            # code...
            if ($value != $author ) {
                return $this->json(["message" => "sens interdit loll"],Response::HTTP_FORBIDDEN);
            }
        }
        $sortie_show = $this->serializer->serialize($agenceEnCours, 'json',["groups"=>["usersAgence:read"]]);
        return new JsonResponse($sortie_show, Response::HTTP_OK, [], true);

    }
    /**
     * @Route("/api/agence/{id}/users/{idUser}", name="block", methods={"delete"})
    */
    public function block($id, $idUser, TokenStorageInterface $tokenStorage, AgenceRepository $agenceRepo, UserAgenceRepository $userAgentRepo)
    {
        $author = $tokenStorage->getToken()->getUser();
        if (!($author instanceof AdminAgence)) {
            return $this->json(["message" => "Imposteur va"],Response::HTTP_FORBIDDEN);
        }

        $agenceEnCours = $agenceRepo->findOneBy(["id" => $id]);
        $userEnCours = $userAgentRepo->findOneBy(["id" => $idUser]);
        // dd($userEnCours);
        if (!$agenceEnCours) {
            return $this->json(["message" => "Agence not found"],Response::HTTP_NOT_FOUND);
        }
        if (!$userEnCours) {
            return $this->json(["message" => "user not found"],Response::HTTP_NOT_FOUND);
        }
        if ($author->getAgence() != $agenceEnCours) {
            return $this->json(["message" => "boko fa gayine"],Response::HTTP_FORBIDDEN);
        }
       
        if ($userEnCours->getAgence() != $agenceEnCours) {
            return $this->json(["message" => "boko fa gayine"],Response::HTTP_FORBIDDEN);
        }
        if ($userEnCours->getIsActive() == true) {
            $userEnCours->setIsActive(false);
        }
        else {
            $userEnCours->setIsActive(true);
        }
        $this->manager->flush();
        return new JsonResponse( Response::HTTP_OK); 
    }

    /**
     * @Route("/api/agences", name="addAgence", methods={"post"})
    */
    public function addAgence(Request $request, TokenStorageInterface $tokenStorage, Shared $shared, EntityManagerInterface $manager)
    {
        $author = $tokenStorage->getToken()->getUser();
        if (!($author instanceof AdminSystem)) {
            return $this->json(["message" => "Imposteur va"],Response::HTTP_FORBIDDEN);
        }

        $content = $request->getContent();
       $value = $this->shared->CreateCompte($content);
       $status = Response::HTTP_BAD_REQUEST;
       if ($value instanceof Comptes) {
        $this->manager->persist($value);
        $this->manager->flush();
        $status = Response::HTTP_CREATED;
        //dd($status);
        return new JsonResponse($status);
       }
       return $this->json($status);

    }

   


}