<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use App\Entity\Account;
use App\Form\AccountType;
use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Form\VirementFormType;
use App\Form\BuyOnlineFormType;
use App\Form\RetraitDepotFormType;
use App\Entity\Company;
use App\Form\AchatFormType;
use App\Form\VenteFormType;
use App\Entity\ShareTransaction;
use App\Entity\Portfolio;
use App\Entity\Events;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\HttpFoundation\JsonResponse;

class ShareController extends AbstractController
{
    #[Route("/api/company", name: "api_company")]
    public function company(EntityManagerInterface $entityManager): Response
    {
        $company = $entityManager->getRepository(Company::class)->findAll();

        $arrayCollection = [];

        foreach ($company as $comp) {
            $arrayCollection[] = [
                "id" => $comp->getId(),
                "name" => $comp->getName(),
                "domain" => $comp->getDomain(),
                "sharePrice" => $comp->getSharePrice(),
                "shareQuantity" => $comp->getShareQuantity(),
            ];
        }
        return new JsonResponse($arrayCollection);
    }

    #[Route("/api/company/details/{id}", name: "api_companyId")]
    public function companyId(
        array $_route_params,
        EntityManagerInterface $entityManager
    ): Response {
        $company = $entityManager
            ->getRepository(Company::class)
            ->findBy(["id" => $_route_params["id"]]);

        $arrayCollection = [];

        foreach ($company as $comp) {
            $arrayCollection[] = [
                "id" => $comp->getId(),
                "name" => $comp->getName(),
                "domain" => $comp->getDomain(),
                "sharePrice" => $comp->getSharePrice(),
                "shareQuantity" => $comp->getShareQuantity(),
            ];
        }

        return new JsonResponse($arrayCollection);
    }

    #[Route("/api/company/{client}", name: "api_companyClientId")]
    public function comp(
        array $_route_params,
        EntityManagerInterface $entityManager
    ): Response {
        $client = $this->getUser();
        $portfolioClient = $entityManager
            ->getRepository(Portfolio::class)
            ->findBy([
                "client" => $client->getId(),
            ]);

        $arrayCollection = [];

        foreach ($portfolioClient as $comp) {
            $arrayCollection[] = [
                "id" => $comp->getId(),
                "id_client" => $comp->getClient(),
                "name" => $comp->getShareName(),
                "sharePrice" => $comp->getPrice(),
                "shareQuantity" => $comp->getQuantity(),
            ];
        }

        return new JsonResponse($arrayCollection);
    }

   #[Route("/action", name: "action")]
    public function achat(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response  {
        //Recupere client courant ($client) + son compte epargne ($savingaccount)
        $client = $this->getUser();
        $savingAccount = $entityManager->getRepository(Account::class)->findOneBy([
            'client' => $client,
            'type' => 'Compte Epargne Action'
        ]);
        $existingAccounts = $entityManager
            ->getRepository(Account::class)
            ->findOneBy([
                "client" => $client,
                "type" => "Compte Epargne Action",
            ]);
        $currentAccount = $entityManager->getRepository(Account::class)->findOneBy([
            'client' => $client,
            'type' => 'Compte Courant'
            ]);

        //Methode de form builder
        $form = $this->createForm(AchatFormType::class);
        $form->handleRequest($request);

        //Methoe de form builder /
        if ($form->isSubmitted() && $form->isValid()) {
            //recupere les datas passé fans le form
            $data = $form->getData();
            
            //Recupere ttes les infos des compa
            $companyRepositoryAll = $entityManager->getRepository(Company::class)->findAll();
            $companyRepository = $entityManager->getRepository(Company::class);
            // Recupere le nom de l'action dans la bdd en fonction du nom passé dans le form
            $actionCompany = $companyRepository->findOneBy([
                'name' => $data['name']->getName()
            ]);

            //Up Date quantité / retire une quantité dans la table Company
            $quantity = $data['quantity'];

            //Condition ! si le quantité d'action dispo a l'achat est suffisante
            if($actionCompany->getShareQuantity() >= $quantity){ 
                $upDateStock = $actionCompany->setShareQuantity($actionCompany->getShareQuantity() - $quantity);
                $entityManager->persist($upDateStock);

                //Recupere le prix unitaire et prix/quantité de l'action choisi
                $prixUnitaire = $actionCompany->getSharePrice();
                $prixAction = $actionCompany->getSharePrice()*$quantity;

                    //Si prix/quantité < solde compte epargne on debite le compte epargne
                    if($prixAction < $savingAccount->getBalance()){
                        $savingAccount->setBalance($savingAccount->getBalance() - $prixAction);
                        $entityManager->persist($savingAccount);

                        //Créer une nouvelle transaction -> alimente table shareTransaction
                        $transaction = new ShareTransaction();
                        $transaction->setShareName($actionCompany);
                        $transaction->setSharePrice($prixUnitaire);
                        $transaction->setClient($client);
                        $transaction->setQuantity($quantity);
                        $transaction->setTotalPrice($prixAction);
                        $transaction->setType("ACHAT (compte epargne)");
                        $entityManager->persist($transaction);
                    }
                    //Si le solde compte epargne est insuffisant ET que le solde de compte courant peut regler la diff
                    elseif ($savingAccount->getBalance() < $prixAction && ($currentAccount->getBalance() + $savingAccount->getBalance()) > $prixAction){
                        $resteAPayer = $prixAction - $savingAccount->getBalance();
                        $majEpargne = $prixAction - $resteAPayer;
                        // Mise A jour solde epargne = mise a 0
                        $savingAccount->setBalance($savingAccount->getBalance() - $majEpargne); 
                        $entityManager->persist($savingAccount);
                        //Mise a jour solde courant
                        $currentAccount->setBalance($currentAccount->getBalance() - $resteAPayer);
                        $entityManager->persist($currentAccount);

                        //Création d'une nouvelle entrée transation
                        $transaction = new ShareTransaction();
                        $transaction->setShareName($actionCompany);
                        $transaction->setSharePrice($prixUnitaire);
                        $transaction->setClient($client);
                        $transaction->setQuantity($quantity);
                        $transaction->setTotalPrice($prixAction);
                        $transaction->setType("ACHAT (compte courant)");
                        $entityManager->persist($transaction);

                    }
                    else { //Sinon on débite le compte courant


                            $this->addFlash('error', 'Les soldes de vos comptes sont insuffisants ');
                        
                    }

                    
                    //Gestion porfolio
                    //Recupere le porte folio en fonction du client connecté et du nom de l'action selectionnée
                    $clientPortfolio = $entityManager->getRepository(Portfolio::class)->findOneBy([
                        'client' => $client,
                        'shareName' => $data['name']->getName(),
                    ]);
                    //Si portfolio existe ajoute la quantité et le prix
                    if($clientPortfolio != null){
                        $clientPortfolio->setQuantity($clientPortfolio->getQuantity() + $quantity);
                        $clientPortfolio->setTotalPrice($clientPortfolio->getTotalPrice() + $prixAction);
                        $entityManager->persist($clientPortfolio);
                    } 
                    else {
                        //Créer un nouveau portfolio
                        $portfolio = new Portfolio();
                        $portfolio->setShareName($actionCompany);
                        $portfolio->setClient($client);
                        $portfolio->setPrice($prixUnitaire);
                        $portfolio->setQuantity($quantity);
                        $portfolio->setTotalPrice($prixAction);
                        $entityManager->persist($portfolio);
                    }

                    //Enregistre dans la base de donnée
                    
                    $entityManager->flush();

                    //Toutes les 5 actions acheter
                    $companyQuantity = $actionCompany->getShareQuantity();
                    $currentPrice = $actionCompany->getSharePrice();
                   
                    switch ($companyQuantity) {  
                        case $companyQuantity > 45 && $companyQuantity < 50 :
                            break;  
                        case $companyQuantity > 40 && $companyQuantity < 46 :    
                            $newPrice = round($currentPrice * (1 + 5 / 100), 2);
                            $actionCompany->setSharePrice($newPrice);
                            if($clientPortfolio != null){
                                $clientPortfolio->setPrice($newPrice);
                                $entityManager->persist($clientPortfolio);
                            }
                            else{
                                $portfolio->setPrice($newPrice);
                                $entityManager->persist($portfolio);

                            }
                            $entityManager->persist($actionCompany);
                            $entityManager->flush();
                            break;  
                        case $companyQuantity > 35 && $companyQuantity < 41 :  
                            $basePrice = round($currentPrice * (1 - 5 / 100), 2);
                            $newPrice = round($currentPrice * (1 + 10 / 100), 2);
                            $actionCompany->setSharePrice($newPrice);
                            if($clientPortfolio != null){
                                $clientPortfolio->setPrice($newPrice);
                                $entityManager->persist($clientPortfolio);
                            }
                            else{
                                $portfolio->setPrice($newPrice);
                                $entityManager->persist($portfolio);

                            }
                            $entityManager->persist($actionCompany);
                            $entityManager->flush(); 
                             break;  
                        case $companyQuantity > 30 && $companyQuantity < 36 :  
                            $basePrice = round($currentPrice * (1 - 10 / 100), 2);
                            $newPrice = round($currentPrice * (1 + 15 / 100), 2);
                            $actionCompany->setSharePrice($newPrice);

                            if($clientPortfolio != null){
                                $clientPortfolio->setPrice($newPrice);
                                $entityManager->persist($clientPortfolio);
                            }
                            else{
                                $portfolio->setPrice($newPrice);
                                $entityManager->persist($portfolio);

                            }
                            
                            $entityManager->persist($actionCompany); 
                            $entityManager->flush();
                            break;  
                        case $companyQuantity > 25 && $companyQuantity < 31 :  
                            $basePrice = round($currentPrice * (1 - 15 / 100), 2);
                            $newPrice = round($currentPrice * (1 + 20 / 100), 2);
                            $actionCompany->setSharePrice($newPrice);
                            if($clientPortfolio != null){
                                $clientPortfolio->setPrice($newPrice);
                                $entityManager->persist($clientPortfolio);
                            }
                            else{
                                $portfolio->setPrice($newPrice);
                                $entityManager->persist($portfolio);

                            }
                            $entityManager->persist($actionCompany);
                            $entityManager->flush(); 
                            break;  
                        case $companyQuantity > 20 && $companyQuantity < 26 :  
                            $basePrice = round($currentPrice * (1 - 20 / 100), 2);
                            $newPrice = round($currentPrice * (1 + 25 / 100), 2);
                            $actionCompany->setSharePrice($newPrice);
                            if($clientPortfolio != null){
                                $clientPortfolio->setPrice($newPrice);
                                $entityManager->persist($clientPortfolio);
                            }
                            else{
                                $portfolio->setPrice($newPrice);
                                $entityManager->persist($portfolio);

                            }
                            $entityManager->persist($actionCompany);
                            $entityManager->flush(); 
                            break;  
                        case $companyQuantity > 15 && $companyQuantity < 21 :  
                            $basePrice = round($currentPrice * (1 - 25 / 100), 2);
                            $newPrice = round($currentPrice * (1 + 30 / 100), 2);
                            $actionCompany->setSharePrice($newPrice);
                            if($clientPortfolio != null){
                                $clientPortfolio->setPrice($newPrice);
                                $entityManager->persist($clientPortfolio);
                            }
                            else{
                                $portfolio->setPrice($newPrice);
                                $entityManager->persist($portfolio);

                            }
                            $entityManager->persist($actionCompany);
                            $entityManager->flush(); 
                            break;  
                        case $companyQuantity > 10 && $companyQuantity < 16 :  
                            $basePrice = round($currentPrice * (1 - 30 / 100), 2);
                            $newPrice = round($currentPrice * (1 + 45 / 100), 2);
                            $actionCompany->setSharePrice($newPrice);
                            if($clientPortfolio != null){
                                $clientPortfolio->setPrice($newPrice);
                                $entityManager->persist($clientPortfolio);
                            }
                            else{
                                $portfolio->setPrice($newPrice);
                                $entityManager->persist($portfolio);

                            }
                            $entityManager->persist($actionCompany); 
                            $entityManager->flush();
                            break;  
                        case $companyQuantity > 5 && $companyQuantity < 11 :  
                            $basePrice = round($currentPrice * (1 - 40 / 100), 2);
                            $newPrice = round($currentPrice * (1 + 45 / 100), 2);
                            $actionCompany->setSharePrice($newPrice);
                            if($clientPortfolio != null){
                                $clientPortfolio->setPrice($newPrice);
                                $entityManager->persist($portfolio);
                            }
                            else{
                                $portfolio->setPrice($newPrice);
                                $entityManager->persist($portfolio);

                            }
                            $entityManager->persist($actionCompany); 
                            $entityManager->flush();
                            break;  
                        case $companyQuantity > 0 && $companyQuantity < 6 :  
                            $basePrice = round($currentPrice * (1 - 45 / 100), 2);
                            $newPrice = round($currentPrice * (1 + 50 / 100), 2);
                            $actionCompany->setSharePrice($newPrice);
                            if($clientPortfolio != null){
                                $clientPortfolio->setPrice($newPrice);
                                $entityManager->persist($clientPortfolio);
                            }
                            else{
                                $portfolio->setPrice($newPrice);
                                $entityManager->persist($portfolio);

                            }
                            $entityManager->persist($actionCompany); 
                            $entityManager->flush();
                            break;  
                                 
                        default:   
                        echo 'ntm';
                    }
                    
            }
            else {
                $this->addFlash('error', 'Nombre d\'action insuffisant');
            }

        }

        return $this->render('home/action.html.twig', [
            'AchatFormType' => $form->createView(),
            'account' => $existingAccounts
        ]);
    } 

    #[Route("/vente", name: "vente")]
    public function vente(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Recupere le client connecté -> methode de EntityManagerInterface
        $client = $this->getUser();
        $savingAccount = $entityManager->getRepository(Account::class)->findOneBy([
            'client' => $client,
            'type' => 'Compte Epargne Action'
        ]); //recupere dans la table Account le compte epargne du client connecté

        $portfolioClient = $entityManager->getRepository(Portfolio::class)->findBy([
            'client' => $client->getId(),
        ]); //recupere le portfolio du client connecté

        $form = $this->createForm(VenteFormType::class, null, ['transactions' => $portfolioClient]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $clientPortfolio = $entityManager->getRepository(Portfolio::class)->findOneBy([
                'client' => $client,
                'shareName' => $data['shareName'],
            ]);


            $companyRepository = $entityManager->getRepository(Company::class);
            $company = $companyRepository->findOneBy(['name' => $data['shareName']]);

            $prixUnitaire = $company->getSharePrice();
            $totalPrice = $prixUnitaire * $data['quantity'];



            if($clientPortfolio->getQuantity() > $data['quantity']){
                $clientPortfolio->setQuantity($clientPortfolio->getQuantity() - $data['quantity']);
                $clientPortfolio->setPrice($clientPortfolio->getPrice());
                $clientPortfolio->setTotalPrice($clientPortfolio->getTotalPrice() - $totalPrice);
                $entityManager->persist($clientPortfolio);
                
            }
            elseif ($clientPortfolio->getQuantity() == $data['quantity']){
                $miseZero = $clientPortfolio->setQuantity($clientPortfolio->getQuantity() - $data['quantity']);
                $entityManager->remove($miseZero);
                // $entityManager->flush();
            }
            else {
                $this->addFlash('error', 'Nombre d\'action insuffisant');
            }

            //Instancie un objet ShareTransaction
            $transaction = new ShareTransaction();
            $transaction->setClient($client);
            $transaction->setShareName($data['shareName']);
            $transaction->setSharePrice($prixUnitaire);
            $transaction->setTotalPrice($totalPrice);
            $transaction->setQuantity($data['quantity']);
            $transaction->setType('VENTE');
            $entityManager->persist($transaction);

            //créditer Company
            $company = $entityManager->getRepository(Company::class)->findOneBy([
                'name' => $data['shareName']
            ]);
            $company->setShareQuantity($company->getShareQuantity() + $data['quantity']);
            $entityManager->persist($company);


            //créditer le compte epargne
            $savingAccount->setBalance($savingAccount->getBalance() + $totalPrice);
            $entityManager->persist($savingAccount);
            $entityManager->flush();

            $companyQuantity = $company->getShareQuantity();
            $currentPrice = $company->getSharePrice();

            switch ($companyQuantity) {  
                case $companyQuantity > 45 && $companyQuantity < 50 :
                    break;  
                case $companyQuantity > 40 && $companyQuantity < 46 :    
                    $newPrice = round($currentPrice * (1 - 5 / 100), 2);
                    $company->setSharePrice($newPrice);
                    $clientPortfolio->setPrice($newPrice);
                    $entityManager->persist($clientPortfolio);
                    $entityManager->persist($company);
                    $entityManager->flush();
                    break;  
                case $companyQuantity > 35 && $companyQuantity < 41 :  
                    $basePrice = round($currentPrice * (1 + 5 / 100), 2);
                    $newPrice = round($currentPrice * (1 - 10 / 100), 2);
                    $company->setSharePrice($newPrice);
                    $clientPortfolio->setPrice($newPrice);
                    $entityManager->persist($clientPortfolio);
                    $entityManager->persist($company);
                    $entityManager->flush(); 
                     break;  
                case $companyQuantity > 30 && $companyQuantity < 36 :  
                    $basePrice = round($currentPrice * (1 + 10 / 100), 2);
                    $newPrice = round($currentPrice * (1 - 15 / 100), 2);
                    $company->setSharePrice($newPrice);
                    $clientPortfolio->setPrice($newPrice);
                    $entityManager->persist($clientPortfolio);
                    $entityManager->persist($company); 
                    $entityManager->flush();
                    break;  
                case $companyQuantity > 25 && $companyQuantity < 31 :  
                    $basePrice = round($currentPrice * (1 + 15 / 100), 2);
                    $newPrice = round($currentPrice * (1 - 20 / 100), 2);
                    $company->setSharePrice($newPrice);
                    $clientPortfolio->setPrice($newPrice);
                    $entityManager->persist($clientPortfolio);
                    $entityManager->persist($company);
                    $entityManager->flush(); 
                    break;  
                case $companyQuantity > 20 && $companyQuantity < 26 :  
                    $basePrice = round($currentPrice * (1 + 20 / 100), 2);
                    $newPrice = round($currentPrice * (1 - 25 / 100), 2);
                    $company->setSharePrice($newPrice);
                    $clientPortfolio->setPrice($newPrice);
                    $entityManager->persist($clientPortfolio);
                    $entityManager->persist($company);
                    $entityManager->flush(); 
                    break;  
                case $companyQuantity > 15 && $companyQuantity < 21 :  
                    $basePrice = round($currentPrice * (1 + 25 / 100), 2);
                    $newPrice = round($currentPrice * (1 - 30 / 100), 2);
                    $company->setSharePrice($newPrice);
                    $clientPortfolio->setPrice($newPrice);
                    $entityManager->persist($clientPortfolio);
                    $entityManager->persist($company);
                    $entityManager->flush(); 
                    break;  
                case $companyQuantity > 10 && $companyQuantity < 16 :  
                    $basePrice = round($currentPrice * (1 + 30 / 100), 2);
                    $newPrice = round($currentPrice * (1 - 45 / 100), 2);
                    $company->setSharePrice($newPrice);
                    $clientPortfolio->setPrice($newPrice);
                    $entityManager->persist($clientPortfolio);
                    $entityManager->persist($company); 
                    $entityManager->flush();
                    break;  
                case $companyQuantity > 5 && $companyQuantity < 11 :  
                    $basePrice = round($currentPrice * (1 + 40 / 100), 2);
                    $newPrice = round($currentPrice * (1 - 45 / 100), 2);
                    $company->setSharePrice($newPrice);
                    $clientPortfolio->setPrice($newPrice);
                    $entityManager->persist($clientPortfolio);
                    $entityManager->persist($company); 
                    $entityManager->flush();
                    break;  
                case $companyQuantity > 0 && $companyQuantity < 6 :  
                    $basePrice = round($currentPrice * (1 + 45 / 100), 2);
                    $newPrice = round($currentPrice * (1 - 50 / 100), 2);
                    $company->setSharePrice($newPrice);
                    $clientPortfolio->setPrice($newPrice);
                    $entityManager->persist($clientPortfolio);
                    $entityManager->persist($company); 
                    $entityManager->flush();
                    break;  
                case $companyQuantity = 0 :  
                    // $entityManager->remove($clientPortfolio->getClient());
                    // $entityManager->remove($clientPortfolio);
                    // $entityManager->flush();
                    break; 
                         
                default:   
                echo 'ntm';
            }
            
            return $this->redirectToRoute('vente');
        }

        return $this->render('home/vente.html.twig', [
            'VenteFormType' => $form->createView(),
            'client' => $client->getFirstname(),
            'actions' => $portfolioClient,
        ]);

    }
    
    #[Route("/action/{param}", name: "actionId")]
    public function actionId(): Response
    {
        return $this->render("home/actionid.html.twig");
    }
}


