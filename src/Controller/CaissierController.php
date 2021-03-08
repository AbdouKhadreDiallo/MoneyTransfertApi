<?php

namespace App\Controller;

use App\Service\Shared;
use App\Entity\Caissier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CaissierController extends AbstractController {
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
     * @Route("/api/caissiers", name="add_caissier", methods={"post"})
    */
    public function createCaissier(Request $request)
    {
        $value = $this->shared->addUser("App\Entity\Caissier", $request);
        // dd($value);
        $status = Response::HTTP_BAD_REQUEST;
        if ($value instanceof Caissier)
        {
            //dd($value);
            $this->manager->persist($value);
            $this->manager->flush();
            $status = Response::HTTP_CREATED;
            //dd($status);
            return new JsonResponse($status);
        }
        return $this->json($status);
    }
}