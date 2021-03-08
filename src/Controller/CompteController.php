<?php

namespace App\Controller;

use App\Entity\Comptes;
use App\Service\Shared;
use App\Entity\UserAgence;
use App\Entity\AdminAgence;
use App\Repository\ComptesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;



class CompteController extends AbstractController{
    
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
     * @Route("/api/comptes", name="add_compte", methods={"post"})
     */
   public function addCompte(Request $request)
   {
       
       $content = $request->getContent();
    //    dd($content['agence']);
       $value = $this->shared->CreateCompte($content);
    //    dd($value);
       $status = Response::HTTP_BAD_REQUEST;
       if ($value instanceof Comptes) {
        $this->manager->persist($value);
        $this->manager->flush();
        $status = Response::HTTP_CREATED;
        //dd($status);
        return new JsonResponse($status);
       }
       return $this->json($status);
    //    dd($value);
   }

    /**
     * @Route("/api/compte/user", name="getCompte", methods={"get"})
    */

    public function getUserCompte(Request $request, TokenStorageInterface $tokenStorage, ComptesRepository $cmptRepo)
    {
        $author = $tokenStorage->getToken()->getUser();
      //   dd($author);
      //   dd($author instanceof AdminAgence);
        if (!($author instanceof AdminAgence || $author instanceof UserAgence)) {
            return $this->json(["message" => "Imposteur va"],Response::HTTP_FORBIDDEN);
        }

        $compte = $cmptRepo->findOneBy(["agence" => $author->getAgence()]);
        if ($compte) {
            $sortie_show = $this->serializer->serialize($compte , 'json',["groups"=>["UsersCompte:read"]]);
            return new JsonResponse($sortie_show, Response::HTTP_OK, [], true);
        }
    }

    /**
     * @Route("/api/compte/numero", name="GetNumero", methods={"post"})
    */
    public function getByCode(Request $request,ComptesRepository $compteRepo)  
    {
        $code = $request->getContent();
        $code = $this->serializer->decode($code, "json");
        $numeroCompte = $code['numeroCompte'];
        $transcationValable = $compteRepo->findOneBy(["numeroCompte"=>$numeroCompte]);
        if ($transcationValable) {
            $sortie_show = $this->serializer->serialize($transcationValable , 'json',["groups"=>["getByCode:read"]]);
            return new JsonResponse($sortie_show, Response::HTTP_OK, [], true);
        }
    }
}