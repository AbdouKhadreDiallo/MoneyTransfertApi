<?php

namespace App\Controller;

use App\Service\Shared;
use App\Entity\UserAgence;
use App\Entity\AdminAgence;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class UserAgenceController extends AbstractController {
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
     * @Route("/api/user_agences", name="userAgenceadd", methods={"post"})
    */
    public function createUserAgence(Request $request,TokenStorageInterface $tokenStorage)
    {
        $author = $tokenStorage->getToken()->getUser();
        if (!($author instanceof AdminAgence)) {
            return $this->json(["message" => "Imposteur va"],Response::HTTP_FORBIDDEN);
        }
        $value = $this->shared->addUser("App\Entity\UserAgence", $request);
        $status = Response::HTTP_BAD_REQUEST;
        if ($value instanceof UserAgence)
        {
            $value->setAgence($author->getAgence());
            $this->manager->persist($value);
            $this->manager->flush();
            $status = Response::HTTP_CREATED;
            //dd($status);
            return new JsonResponse($status);
        }
        return $this->json($status);
    }

    

}