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

    #[Route('/api/company', name: 'api_company')]
    public function company(EntityManagerInterface $entityManager): Response
    {   
        $company = $entityManager->getRepository(Company::class)->findAll();

        $arrayCollection = array();

        foreach($company as $comp) {
            $arrayCollection[] = array(
                'id' => $comp->getId(),
                'name' => $comp->getName(),
                'domain' => $comp->getDomain(),
                'sharePrice' => $comp->getSharePrice(),
                'shareQuantity' => $comp->getShareQuantity(),
            );
        }
        return new JsonResponse($arrayCollection);
    }

    #[Route('/api/company/{page}', name:'api_companyId')]
    public function companyId(array $_route_params , EntityManagerInterface $entityManager): Response
    {
        $company = $entityManager->getRepository(Company::class)->findBy(['id' => $_route_params['page']]);

        $arrayCollection = array();

        foreach($company as $comp) {
            $arrayCollection[] = array(
                'id' => $comp->getId(),
                'name' => $comp->getName(),
                'domain' => $comp->getDomain(),
                'sharePrice' => $comp->getSharePrice(),
                'shareQuantity' => $comp->getShareQuantity(),
            );
        }

        return new JsonResponse($arrayCollection);
    } 

    #[Route('/achat', name:'_achat')]
    public function achat(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Recupere client courant ($client) + son compte epargne ($savingaccount)
        $client = $this->getUser();
        $savingAccount = $entityManager->getRepository(Account::class)->findOneBy([
            'client' => $client,
            'type' => 'Compte Epargne Action'
        ]);

        $form = $this->createForm(AchatFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            //Recupere l'action selectionnée
            $companyRepositoryAll = $entityManager->getRepository(Company::class)->findAll();
            $companyRepository = $entityManager->getRepository(Company::class);
            // dump($companyRepository);
            $actionCompany = $companyRepository->findOneBy(['name' => $data['name']->getName()]);

            //Up Date quantité / retire une quantité dans la table Company
            $quantity = $data['quantity'];
            $upDateStock = $actionCompany->setShareQuantity($actionCompany->getShareQuantity() - $quantity);

            //Paiement de l'action calcul et retire le pris de table Account
            $prixUnitaire = $actionCompany->getSharePrice();
            $prixAction = $actionCompany->getSharePrice()*$quantity;
            if($prixAction < $savingAccount->getBalance()){
                $savingAccount->setBalance($savingAccount->getBalance() - $prixAction);
                $entityManager->persist($savingAccount);

            }
            else {
                //Renvoyer vers le compte courant);
                $currentAccount = $entityManager->getRepository(Account::class)->findOneBy([
                    'client' => $client,
                    'type' => 'Compte Courant'
                ]);
                $currentAccount->setBalance($currentAccount->getBalance() - $prixAction);
                $entityManager->persist($currentAccount); 
            }
            
            //Créer une nouvelle transaction
            $transaction = new ShareTransaction();
            $transaction->setShareName($actionCompany);
            $transaction->setSharePrice($prixUnitaire);
            $transaction->setClient($client);
            $transaction->setQuantity($quantity);
            $transaction->setTotalPrice($prixAction);
            $transaction->setType("ACHAT");

            //Créer un nouveau portfolio
            $portfolio = new Portfolio();
            $portfolio->setShareName($actionCompany);
            $portfolio->setClient($client);
            $portfolio->setPrice($prixUnitaire);
            $portfolio->setQuantity($quantity);
            $portfolio->setTotalPrice($prixAction);



            //Enregistre dans la base de donnée
            $entityManager->persist($upDateStock);
            $entityManager->persist($transaction);
            $entityManager->persist($portfolio);
            $entityManager->flush();

        }

        return $this->render('home/achat.html.twig', [
            'AchatFormType' => $form->createView(),
            

        ]);


    } 

    #[Route('/action', name: 'action')]

    public function viewCompanies(): Response
    {
        return $this->render('home/action.html.twig');
    }

    #[Route('/vente', name:'vente')]
    public function vente(Request $request, EntityManagerInterface $entityManager): Response
    {
        // $user = $this->getUser();
        // $transaction = $user->getShareTransaction();
        //dump($transaction);
        $client = $this->getUser();

        $clientTransaction = $entityManager->getRepository(ShareTransaction::class)->findBy([
            'client' => $client->getId(),
        ]);
        //dump($clientTransaction);
    
        $form = $this->createForm(VenteFormType::class, null, ['transactions' => $clientTransaction]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            //dump($data);

            $companyRepository = $entityManager->getRepository(ShareTransaction::class);
            //dump($companyRepository);
            $salingAction = $companyRepository->findOneBy([
                'client' => $client,
                'shareName' => $data['shareName']
            ]);

            $p = $salingAction->getSharePrice();
            $tp = $p * $data['quantity'];
            //dump($salingAction);


            //Modif portfolio

            $clientPortfolio = $entityManager->getRepository(Portfolio::class)->findOneBy([
                'client' => $client,
                'shareName' => $data['shareName'],
            ]);

            $clientPortfolio->setQuantity($clientPortfolio->getQuantity() - $data['quantity']);
            $clientPortfolio->setPrice($clientPortfolio->getPrice());
            $clientPortfolio->setTotalPrice($clientPortfolio->getTotalPrice() - $tp);

            $entityManager->persist($clientPortfolio);
            
            $transaction = new ShareTransaction();
            $transaction->setClient($client);
            $transaction->setShareName($data['shareName']);
            $transaction->setSharePrice($p);
            $transaction->setTotalPrice($tp);
            $transaction->setQuantity($data['quantity']);
            $transaction->setType('VENTE');
            $entityManager->persist($transaction);

            $entityManager->flush();
            return $this->redirectToRoute('vente');
        }

        // dump($clientTransaction);

        return $this->render('home/vente.html.twig', [
            'VenteFormType' => $form->createView(),
            'client' => $client->getFirstname(),
            'actions' => $clientTransaction,
        ]);

        


    }
}