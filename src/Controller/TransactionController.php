<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Agence;
use App\Entity\Client;
use App\Entity\UserAgence;
use App\Entity\AdminAgence;
use App\Entity\AdminSystem;
use App\Entity\Transaction;
use App\Repository\AgenceRepository;
use App\Repository\ClientRepository;
use App\Repository\ComptesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TransactionController extends AbstractController {
    private $manager;
    private $serializer;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/transactions/depot", name="depot", methods={"post"})
    */
    public function depot(Request $request,TokenStorageInterface $tokenStorage,ComptesRepository $compteRepo)
    {
        $clientSender = new Client();
        $clientReceiver = new Client();

        $depot = $request->getContent();
        $depot = $this->serializer->decode($depot, "json");
        $sender = $depot['sender'];
        $receiver = $depot['receiver'];
        
        unset($depot['sender']);
        unset($depot['receiver']);
        $montant = $depot['montant'];
        
        $transaction = $this->serializer->denormalize($depot, "App\Entity\Transaction");
        $frais = $this->calculTaxe($montant);
        $transaction->setCodeTransmission($this->generate());
        $transaction->setFrais($frais);
        $transaction->setFraisEtat($this->fraisEtat($frais));
        $transaction->setFraisSystem($this->fraisSystem($frais));
        $transaction->setFraisRetrait($this->fraisRetrait($frais));
        $transaction->setFraisDepot($this->fraisDepot($frais));
        $author = $tokenStorage->getToken()->getUser();
        if ($author instanceof UserAgence) {
            $transaction->setUserAuthorDepot($author);
        }
        elseif ($author instanceof AdminAgence) {
            $transaction->setAdminSystemAuthorDepot($author);
        }
        else {
            return $this->json(["message" => "Imposteur va"],Response::HTTP_FORBIDDEN);
        }

        $agence = $author->getAgence();
        $comptes = $compteRepo->findAll();
        foreach ($comptes as $value) {
            if ($value->getAgence() == $agence) {
                $compteAssociated = $value;
            }
        }
        if ($compteAssociated->getSolde() < 5000) {
            return $this->json(["message" => "Solde insuffisant"],Response::HTTP_FORBIDDEN);
        }

        // verification si somme existe
        if ($compteAssociated->getSolde() < $montant) {
            return $this->json(["message" => "Solde insuffisant"],Response::HTTP_FORBIDDEN);
        }
        $now = new DateTime();
        $transaction->setCompte($compteAssociated);
        $compteAssociated->setSolde($compteAssociated->getSolde()-$montant);
        $compteAssociated->setUpdatedAt($now);
        // dd($compteAssociated->getSolde());

        // add sender
        $clientSender->setNomComplet($sender['fullname']);
        $clientSender->setTelephone($sender['telephone']);
        $clientSender->setCNI($sender['cin']);
        $this->manager->persist($clientSender);
        $transaction->setSender($clientSender);

        //add Receiver
        $clientReceiver->setNomComplet($receiver['fullname']);
        $clientReceiver->setTelephone($receiver['telephone']);
        $clientReceiver->setCNI($receiver['cin']);
        $this->manager->persist($clientReceiver);
        $transaction->setReceiver($clientReceiver);

        $this->manager->persist($transaction);
        $this->manager->flush();
        return $this->json([$transaction],Response::HTTP_CREATED);


    }

    /**
     * @Route("/api/transactions/trie/{parametre}", name="trier", methods={"get"})
    */
    public function FunctionName($parametre,TransactionRepository $transactionRepo)
    {
        $transaction = $transactionRepo->findBy(array(), array($parametre=>'ASC'));
        return $this->json([$transaction],Response::HTTP_OK);
    }

    /**
     * @Route("/api/transactions/commissions", name="commissions", methods={"get"})
    */
    public function commissions(TokenStorageInterface $tokenStorage,ComptesRepository $compteRepo, TransactionRepository $transactionRepo)
    {
        $users = [];
        $depot=[];
        $retrait = [];
        $depotRetrait = [];
        $transactionConserné = [];
        $user = $tokenStorage->getToken()->getUser();
        if (!($user instanceof AdminAgence)) {
            return $this->json(["message" => "yamal sa place"],Response::HTTP_FORBIDDEN);
        }
        // return $this->json($user->getTransactionDepot(),Response::HTTP_OK);
        $agence = $user->getAgence();
        $usersInAgence = $agence->getUsersAgence();
        $adminInAgence = $agence->getAdmin();
        $users[] = $usersInAgence;
        $users[] = $adminInAgence;
        $transaction = $transactionRepo->findAll();
        
        foreach ($transaction as $value) {
            // dd($value->getUserAuthorDepot());
            if (in_array( (array)$value->getUserAuthorDepot(), (array)$usersInAgence) || in_array( (array)$value->getAdminSystemAuthorDepot(), (array)$adminInAgence) &&  in_array( (array)$value->getUserAuthorRetrait(), (array)$usersInAgence) || in_array( (array)$value->getAdminSystemAuthorRetrait(), (array)$adminInAgence)) {
                $depotRetrait[] = $value;
                $pos = array_search($value, $transaction);
                unset($transaction[$pos]);
            }
            elseif ( in_array( (array)$value->getUserAuthorDepot(), (array)$usersInAgence) || in_array( (array)$value->getUserAuthorDepot(), (array)$adminInAgence) ) {
                $depot[] = $value;
            }
            elseif (in_array( (array)$value->getUserAuthorRetrait(), (array)$usersInAgence) || in_array( (array)$value->getUserAuthorRetrait(), (array)$adminInAgence)) {
                $retrait[] = $value;
            }
        }

       return $this->json(count($depotRetrait),Response::HTTP_OK);
       
        $sortie_show = $this->serializer->serialize($agence, 'json',["groups"=>["usersAgence:read"]]);
        return new JsonResponse($sortie_show, Response::HTTP_OK, [], true);
        dd($userAgence->getAdmin());
        if ($transaction) {
            return $this->json($transaction,Response::HTTP_OK);
        }
    }

    
    /**
     * @Route("/api/transactions/code", name="GetCode", methods={"post"})
    */
    public function getByCode(Request $request,TransactionRepository $transactionRepo)  
    {
        $code = $request->getContent();
        $code = $this->serializer->decode($code, "json");
        $codeTransmission = $code['codeTransmission'];
        $transcationValable = $transactionRepo->findOneBy(["codeTransmission"=>$codeTransmission]);
        if ($transcationValable) {
            return $this->json([$transcationValable],Response::HTTP_OK);
        }
    }
    /**
     * @Route("/api/transactions/retrait", name="retrait", methods={"post"})
    */
    public function retrait(ClientRepository $clientRepo,Request $request,TokenStorageInterface $tokenStorage,ComptesRepository $compteRepo, TransactionRepository $transactionRepo)
    {
        $retrait = $request->getContent();
        $retrait = $this->serializer->decode($retrait, "json");
        $receiver = $retrait['receiver'];
        $codeTransmission = $retrait['codeTransmission'];
        
        
        $transcationValable = $transactionRepo->findOneBy(["codeTransmission"=>$codeTransmission]);
        if (!$transcationValable) {
            return $this->json(["message" => "Code introuvalble"],Response::HTTP_NOT_FOUND);
        }
        if ($transcationValable->getIsFinished() == true) {
            return $this->json(["message" => "l'argent a déjà été retiré"],Response::HTTP_FORBIDDEN);
        }

        $destinataire = $clientRepo->findOneBy([
            "CNI" => $receiver['cni'],
        ]);
        //dd($destinataire);
        if ($destinataire) {
            if ($transcationValable->getReceiver() != $destinataire) {
                return $this->json(["message" => "Verifiz vos informations personnelles"],Response::HTTP_FORBIDDEN);
            }
        }
        $author = $tokenStorage->getToken()->getUser();
        if ($author instanceof UserAgence) {
            $transcationValable->setUserAuthorRetrait($author);
        }
        elseif ($author instanceof AdminAgence) {
            $transcationValable->setAdminSystemAuthorRetrait($author);
        }
        else{
            return $this->json(["message" => "Imposteur va"],Response::HTTP_FORBIDDEN);
        }
        $agence = $author->getAgence();
        $comptes = $compteRepo->findAll();
        foreach ($comptes as $value) {
            if ($value->getAgence() == $agence) {
                $compteAssociated = $value;
            }
        }
        $transcationValable->setCompteRetrait($compteAssociated);
        $montant = ($transcationValable->getMontant()-$transcationValable->getFrais())+$transcationValable->getFraisRetrait();
        $compteAssociated->setSolde($compteAssociated->getSolde() + $montant);
        $now = new DateTime();
        $compteAssociated->setUpdatedAt($now);
        $transcationValable->setDateRetrait($now);
        // $transcationValable->setDateRetrait($now->format('Y-m-d H:i:s'));
        $transcationValable->setIsFinished(true);
        $this->manager->flush();
        return $this->json(["message" => "retrait avec succes"],Response::HTTP_OK);



    }

    /**
     * @Route("/api/allTransactions", name="all", methods={"get"})
    */
    public function allTransaction(TokenStorageInterface $tokenStorage)
    {
        $depotRetrait = [];
        $depot = [];
        $retrait = [];
        $depotAdmin = [];
        $retraitAdmin = [];
        $transaction = [];


        $depotRetraituser = [];
        $depotuser = [];
        $retraituser = [];
        $depotUserFinal = [];
        $retraitUserFinal = [];

        $user = $tokenStorage->getToken()->getUser();
        if (!($user instanceof AdminAgence)) {
            return $this->json(["message" => "yamal sa place"],Response::HTTP_FORBIDDEN);
        }

        $agence = $user->getAgence();
        $sortie_show = $this->serializer->serialize($agence, 'json',["groups"=>["usersAgence:read"]]);
        $users = $this->serializer->deserialize($sortie_show, Agence::class, 'json');
        // dd($users);
        foreach ($users->getAdmin() as $value) {
            $depot[] = $value->getTransactionDepot();
            $retrait[] = $value->getTransactionRetrait();
        }
        foreach ($users->getUsersAgence() as $value) {
            if ($value->getTransactionDepot()) {
                $depotuser[] = $value->getTransactionDepot();
            }
            if ($value->getTransactionRetrait()) {
                $retraituser[] = $value->getTransactionRetrait();
            }
        }
        foreach ($depot as $value) {
           
            foreach ($retrait as $retr) {
               for ($i=0; $i < count($value); $i++) { 
                   for ($j=0; $j < count($retr); $j++) { 
                       if ($value[$i] == $retr[$j]) {
                           $depotRetrait[] = $value[$i];
                       }
                       
                   }
               }
            }
        }
        foreach ($depotuser as $value) {
           
            foreach ($retraituser as $retr) {
               for ($i=0; $i < count($value); $i++) { 
                   for ($j=0; $j < count($retr); $j++) { 
                       if ($value[$i] == $retr[$j]) {
                           $depotRetraituser[] = $value[$i];
                       }
                       
                   }
               }
            }
        }
        foreach ($depotRetrait as $value) {
            if (!in_array($value, $transaction)) {
                $transaction[] = $value;
            }
        }
        foreach ($depotRetraituser as $value) {
            if (!in_array($value, $transaction)) {
                $transaction[] = $value;
            }
        }
        foreach ($depot as $value) {
            foreach ($value as $dep) {
                if (!in_array($dep, $transaction)) {
                    $transaction[] = $dep;
                }
            }
        }
        foreach ($depotuser as $value) {
            foreach ($value as $dep) {
                if (!in_array($dep, $transaction)) {
                    $transaction[] = $dep;
                }
            }
        }
        foreach ($retrait as $value) {
            foreach ($value as $dep) {
                if (!in_array($dep, $transaction)) {
                    $transaction[] = $dep;
                }
            }
        }
        foreach ($retraituser as $value) {
            foreach ($value as $dep) {
                if (!in_array($dep, $transaction)) {
                    $transaction[] = $dep;
                }
            }
        }
        

        
    
        return $this->getSerializedResponse($transaction);
        // $sortie_show = $this->serializer->serialize($transaction, 'json',["groups"=>["allTransaction:read"]]);
        // return new JsonResponse($sortie_show, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/trans", name="alls", methods={"get"})
    */
    public function alls(TokenStorageInterface $tokenStorage, TransactionRepository $transactionRepo,ComptesRepository $compteRepo)
    {
        $depotRetrait = [];
        $onlyDepot = [];
        $onlyRetrait = [];
        $user = $tokenStorage->getToken()->getUser();
        if (!($user instanceof AdminAgence)) {
            return $this->json(["message" => "yamal sa place"],Response::HTTP_FORBIDDEN);
        }

        $agence = $user->getAgence();
        $compte = $compteRepo->findOneBy(["agence" => $agence]);
        
        $transactions = $compte->getTransactions();
        $trans = $compte->getTransactionRetrait();
       
               for ($i=0; $i < count($transactions); $i++) { 
                   for ($j=0; $j < count($trans); $j++) { 
                       if ($transactions[$i] == $trans[$j]) {
                           $depotRetrait[] = $transactions[$i];
                       }
            }
        }
        
        
        foreach ($transactions as $value) {
            if (!in_array($value, $depotRetrait)) {
                $onlyDepot[] = $value;
            }
        }
        foreach ($trans as $value) {
            if (!in_array($value, $depotRetrait)) {
                $onlyRetrait[] = $value;
            }
        }
        // dd(count($onlyRetrait));
        // return $this->json($depotRetrait);
        // $sortie_show = $this->serializer->serialize([$transactions, 'retrait'=>$trans], 'json',["groups"=>["read"]]);
        // $allTransactions = $this->serializer->deserialize($sortie_show, Transaction::class, 'json');
        // $all = $this->getSerializedResponse([$transactions, 'retrait'=>$trans]);
        $dontKnow[] = $transactions;
        $dontKnow[] = $trans;
        
        foreach ($dontKnow as $value) {
            foreach ($value as $tr) {
                foreach ($agence->getUsersAgence() as $user ) {
                    if ($tr->getUserAuthorRetrait() == $user) {
                        $tab[] = $tr;
                    }
                }
            }
        }

        // return $this->json($tab);
        return $this->json([
           "depot"=> $onlyDepot,
           "retrait" => $onlyRetrait,
           "all" => $depotRetrait
        ],Response::HTTP_OK);
        return $all; 
    }
    
    /**
     * @Route("/api/user/transactions", name="myTransactions", methods={"get"})
    */
    public function myTransation(TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();
        if (!($user instanceof UserAgence || $user instanceof AdminAgence)) {
            return $this->json(["message" => "yamal sa place"],Response::HTTP_FORBIDDEN);
        }

        // dd(count($user->getTransactionDepot()) + count($user->getTransactionRetrait()));
        return $this->json([$user->getTransactionDepot(),$user->getTransactionRetrait()
        ]);
    }
    /**
     * @Route("/api/user/transactions/retrait", name="userRetrait", methods={"get"})
    */
    public function myTransationRetrait(TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();
        if (!($user instanceof UserAgence || $user instanceof AdminAgence)) {
            return $this->json(["message" => "yamal sa place"],Response::HTTP_FORBIDDEN);
        }

        
        return $this->json($user->getTransactionRetrait());
    }

    /**
     * @Route("/api/part/etat", name="partEtat", methods={"get"})
    */
    public function partEtat(TokenStorageInterface $tokenStorage, TransactionRepository $transactionRepo)
    {
        $user = $tokenStorage->getToken()->getUser();
        if (!($user instanceof AdminSystem)) {
            return $this->json(["message" => "yamal sa place"],Response::HTTP_FORBIDDEN);
        }

        $transcation = $transactionRepo->findAll();
        $sortie_show = $this->serializer->serialize($transcation, 'json',["groups"=>["partEtat:read"]]);
        return new JsonResponse($sortie_show, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/part/etat", name="partEtat", methods={"get"})
    */
    public function partAgence(TokenStorageInterface $tokenStorage, TransactionRepository $transactionRepo)
    {
        $user = $tokenStorage->getToken()->getUser();
        if (!($user instanceof AdminSystem)) {
            return $this->json(["message" => "yamal sa place"],Response::HTTP_FORBIDDEN);
        }
    }

    

    private function calculTaxe($montant){
        $taxe = 0;
        switch (true) {
            case in_array($montant, range(0,5000)):
                $taxe = 425;
                break;
            case in_array($montant, range(50000,10000)):
                $taxe = 850;
                break;
            case in_array($montant, range(10000,15000)):
                $taxe = 1270;
                break;
            case in_array($montant, range(15000,20000)):
                $taxe = 1695;
                break;
            case in_array($montant, range(20000,50000)):
                $taxe = 2500;
                break;
            case in_array($montant, range(50000,60000)):
                $taxe = 3000;
                break;
            case in_array($montant, range(60000,75000)):
                $taxe = 4000;
                break;
            case in_array($montant, range(75000,120000)):
                $taxe = 5000;
                break;
            case in_array($montant, range(120000,150000)):
                $taxe = 6000;
                break;
            case in_array($montant, range(150000,200000)):
                $taxe = 7000;
                break;
            case in_array($montant, range(200000,250000)):
                $taxe = 8000;
                break;
            case in_array($montant, range(250000,300000)):
                $taxe = 9000;
                break;
            case in_array($montant, range(300000,400000)):
                $taxe = 12000;
                break;
            case in_array($montant, range(400000,750000)):
                $taxe = 15000;
                break;
            case in_array($montant, range(750000,900000)):
                $taxe = 22000;
                break;
            case in_array($montant, range(900000,1000000)):
                $taxe = 25000;
                break;
            case in_array($montant, range(1000000,1125000)):
                $taxe = 27000;
                break;
            case in_array($montant, range(1125000,1400000)):
                $taxe = 30000;
                break;
            case in_array($montant, range(1400000,2000000)):
                $taxe = 32000;
                break;
            case ($montant > 2000000):
                $taxe = (2/100)*$montant;
                break;
        }
        return $taxe;
    }
    private function fraisEtat($taxe){
        return (40/100)*$taxe;
    }
    private function fraisSystem($taxe){
        return (30/100)*$taxe;
    }
    private function fraisDepot($taxe){
        return (10/100)*$taxe;
    }
    private function fraisRetrait($taxe){
        return (20/100)*$taxe;
    }
    function generate(){
    	return random_int(0,100).'-'.random_int(101,500).'-'.random_int(501,999);
    }

    function getSerializedResponse($data = null): JsonResponse
{
    $serializer = $this->get('serializer');

    if (null === $data) {
        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT, [], true);
    }

    $serializedData = $serializer->serialize($data, 'json', [
        ObjectNormalizer::SKIP_NULL_VALUES => true, 
    ]);

    return new JsonResponse($serializedData, 200, [], true);
}
}