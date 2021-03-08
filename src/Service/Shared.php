<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\Agence;
use App\Entity\UserAgence;
use App\Entity\AdminAgence;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Shared {
    private $serializer;
    private $manager;
    private $tokenStorage;
    private $profilRepo;
    private $encoder;
    private $validator;

    public function __construct(ValidatorInterface $validator,UserPasswordEncoderInterface $encoder,ProfilRepository $profilRepo,SerializerInterface $serializer, EntityManagerInterface $manager, TokenStorageInterface $tokenStorage)
    {
        $this->serializer = $serializer;
        $this->tokenStorage = $tokenStorage;
        $this->manager = $manager;
        $this->profilRepo = $profilRepo;
        $this->encoder = $encoder;
        $this->validator = $validator;
    }

    public function CreateCompte($content){
        $content = $this->serializer->decode($content, "json");
        if ($content['solde'] < 700000) {
            return new JsonResponse("un nouveau compte doit obligatoirement avoir 700000 au minimum",Response::HTTP_BAD_REQUEST, [], true);
        }
        $agence = isset($content['agence']) ? $content['agence'] : [];
        $adminAgence = isset($content['adminAgence']) ? $content['adminAgence'] : [];
        $userAgence = isset($content['userAgence']) ? $content['userAgence'] : [];

        unset($content["agence"]);
        unset($content["adminAgence"]);
        unset($content["userAgence"]);

        $compte = $this->serializer->denormalize($content, "App\Entity\Comptes");
        $compte = $compte->setNumeroCompte(uniqid());
        $compte->setCreator($this->tokenStorage->getToken()->getUser());
        if (isset($agence) && isset($adminAgence) && isset($userAgence)) {
            $compte = $this->addAgenceToCompte($compte, $agence,$adminAgence, $userAgence);
        }
        return $compte;

    }
    // public function addUser($content){
    //     $user =  $this->serializer->denormalize($content, "App\Entity\User");
    //     $user->setProfil($this->profilRepo->findOneBy(['libelle' => "CAISSIER"]));
    //     $user->setPassword($this->encoder->encodePassword($user,strtolower('caissier')));
    //     $error = $this->validator->validate($user);
    //     if (!count($error)) {
    //         return $user;
    //     }
    //     return $error;
    // }

    private function addAgenceToCompte($compte, $agence, $admin, $user){
        $newAgence = new Agence();
        foreach ($agence as $key => $value) {
            $FirstMajuscume = "set".ucfirst(strtolower($key));
                if (method_exists("App\Entity\Agence", $FirstMajuscume)) {
                    $newAgence->$FirstMajuscume($value);
                }
        }
        // dd($this->addAdminToAgence($admin) instanceof User);
        $this->addAdminToAgence($admin, $newAgence);
        $this->addUserToAgence($user, $newAgence);

        // $this->$newAgence->addAdminAgence($this->addAdminToAgence($admin));
        // $this->$newAgence->addUserAgence($this->addUserToAgence($user));

        $this->manager->persist($newAgence);
        $compte->setAgence($newAgence);
        return $compte;
    }

    private function addAdminToAgence($admin, $agence){
        $adminAgence = new AdminAgence();
        $adminAgence->setProfil($this->profilRepo->findOneBy(['libelle' => "ADMIN AGENCE"]));
        $adminAgence->setPassword($this->encoder->encodePassword($adminAgence,strtolower('admin agence')));
        foreach ($admin as $key => $value) {
            foreach ($value as $key => $adm) {
                if ($key == "cni") {
                    $adminAgence->setCIN($adm);
                }
                $FirstMajuscume = "set".ucfirst(strtolower($key));
                    if (method_exists("App\Entity\User", $FirstMajuscume)) {
                        $adminAgence->$FirstMajuscume($adm);
                    }
            }
        }
        $this->manager->persist($adminAgence);
        $agence->addAdmin($adminAgence);
    }
    private function addUserToAgence($user, $agence){
        $userAgence = new UserAgence();
        $userAgence->setProfil($this->profilRepo->findOneBy(['libelle' => "USER AGENCE"]));
        $userAgence->setPassword($this->encoder->encodePassword($userAgence,strtolower('user agence')));

        foreach ($user as $key => $value) {
            foreach ($value as $key => $std) {
                if ($key == "cni") {
                    $userAgence->setCIN($std);
                }
                $FirstMajuscume = "set".ucfirst(strtolower($key));
                    if (method_exists("App\Entity\User", $FirstMajuscume)) {
                        $userAgence->$FirstMajuscume($std);
                    }
            }
        }
        $this->manager->persist($userAgence);
        $agence->addUsersAgence($userAgence);
    }
   
    public function addUser($entite, $request)
    {
        $user = $request->getContent();
        $user = $this->serializer->decode($user, "json");
        
        $errors = $this->validator->validate($user);
        if(count($errors) > 0){
            $errors = $this->serializer->serialize($errors,'json');
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $user = $this->serializer->denormalize($user, $entite, true);
        $user->setProfil($this->assign_Profil($entite));
       
        $user->setPassword($this->encoder->encodePassword($user,"password"));
        return $user;
    }

    public function assign_Profil($entity)
    {
        $tab = ['AdminAgence', 'AdminSystem', 'Caissier', 'UserAgence'];
        foreach ($tab as $value) {
            if (strstr($entity, $value)) {
                if ($value == "AdminAgence") {
                    return $this->profilRepo->findOneBy(["libelle" => "ADMIN AGENCE"]);
                }
                elseif ($value == "AdminSystem") {
                    return $this->profilRepo->findOneBy(["libelle" => "ADMIN SYSTEM"]);
                    # code...
                }
                elseif ($value == "Caissier") {
                    return $this->profilRepo->findOneBy(["libelle" => "CAISSIER"]);
                    # code...
                }
                else {
                    return $this->profilRepo->findOneBy(["libelle" => "USER AGENCE"]);
                    # code...
                }
            }
        }
    }



}