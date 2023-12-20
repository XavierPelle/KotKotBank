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

use App\Entity\Portfolio;

class HomeController extends AbstractController
{

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/home.html.twig', []);
    }


    #[Route('/feed', name: 'app_feed')]
    public function feed(EntityManagerInterface $entityManager): Response
    {
        $client = $this->getUser();
        $transaction = null;
        $transaction2 = null;
        $existingAccounts = $entityManager->getRepository(Account::class)->findBy(['client' => $client]);

        $hasCurrentAccount = false;
        $hasSavingsAccount = false;


        foreach ($existingAccounts as $account) {
            if ($account->getType() === 'Compte Courant') {
                $emeteurAccount = $account->getId();
                $transaction = $entityManager->getRepository(Transaction::class)->findBy(['emeteurAccount' => $emeteurAccount]);
                $hasCurrentAccount = true;
            } elseif ($account->getType() === 'Compte Epargne Action') {
                $hasSavingsAccount = true;
                $beneficiaryAccount = $account->getId();
                $transaction2 = $entityManager->getRepository(Transaction::class)->findBy(['beneficiaryAccount' => $beneficiaryAccount]);
            }
        }

            return $this->render('home/feed.html.twig', [
                'client' => $client,
                'hasCurrentAccount' => $hasCurrentAccount,
                'hasSavingsAccount' => $hasSavingsAccount,
                'accounts' => $existingAccounts,
                'transaction' => $transaction,
                'transaction2' => $transaction2,
            ]);

        }

    #[Route('/create-current-account', name: 'create_current_account')]
    public function createCurrentAccount(EntityManagerInterface $entityManager): Response
    {
        $client = $this->getUser();
        $existingAccount = $entityManager->getRepository(Account::class)->findOneBy([
            'client' => $client,
            'type' => 'Compte Courant'
        ]);

        if (!$existingAccount) {
            $account = new Account();
            $account->setClient($client);
            $account->setType('Compte Courant');
            $account->setBalance(10000);

            $entityManager->persist($account);
            $entityManager->flush();

        }
        return $this->redirectToRoute('app_feed');
    }

    #[Route('/create-savings-account', name: 'create_savings_account')]
    public function createSavingsAccount(EntityManagerInterface $entityManager): Response
    {
        $client = $this->getUser();
        $existingAccount = $entityManager->getRepository(Account::class)->findOneBy([
            'client' => $client,
            'type' => 'Compte Epargne Action'
        ]);

        if (!$existingAccount) {
            $account = new Account();
            $account->setClient($client);
            $account->setType('Compte Epargne Action');
            $account->setBalance(100);

            $entityManager->persist($account);
            $entityManager->flush();
        } 
        return $this->redirectToRoute('app_feed');
    }


    #[Route('/transaction', name: 'app_virement')]
    public function virement(Request $request, EntityManagerInterface $entityManager ): Response
    {
        $user = $this->getUser();
        $account = $user->getAccounts();
        $client = $this->getUser();

        $currentAccount = $entityManager->getRepository(Account::class)->findOneBy([
            'client' => $client,
            'type' => 'Compte Courant'
        ]);

        
        $accounts = $entityManager->getRepository(Account::class)->findAll();

        $form = $this->createForm(VirementFormType::class, null, ['accounts' => $accounts]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $accountRepository = $entityManager->getRepository(Account::class);
            $beneficiaire = $accountRepository->findOneBy(['id' => $data['beneficiaryaccount']]);
            
            if($currentAccount->getBalance()< $data['amount'] && $currentAccount->getOverdraft() < $data['amount'] ){
                $this->addFlash('error', "Votre solde est Insuffisant");

            }
            else {
                if($currentAccount != $beneficiaire) {
                    $beneficiaire->setBalance($beneficiaire->getBalance() + $data['amount']);
                    $currentAccount->setBalance($currentAccount->getBalance() - $data['amount']);


                    $virement = new Transaction();
            
                    $virement->setAmount($data['amount']);
                    $virement->setType('Virement');
                    $virement->setDescription($data['description']);
                    $virement->setBeneficiaryaccount($beneficiaire);
                    $virement->setEmeteurAccount($currentAccount);

            
                    $entityManager->persist($beneficiaire);
                    $entityManager->persist($currentAccount);
                    $entityManager->persist($virement);
                    $entityManager->flush();
                    // $this->addFlash('success','Votre virement a bien été effectué.');
                }
                else {
                    $this->addFlash('error' , "Veuillez entrer un compte beneficiaire différent du compte emetteur");
                }
            }

        }

        $form2 = $this->createForm(RetraitDepotFormType::class, null, ['accounts' => $accounts]);
        $form2->handleRequest($request);

        if ($form2->isSubmitted() && $form2->isValid()) {
            $data = $form2->getData();
        //     //recupere le compte passé dans le formulaire

            if($data['type'] === "depot"){
                $accountRepository = $entityManager->getRepository(Account::class);
                //effectue l'opération
                $currentAccount->setBalance($currentAccount->getBalance() + $data['amount']);

                //crée l'objet Transaction
                $depot = new Transaction();
                $depot->setType('Dépôt');
                $depot->setAmount($data['amount']);
                $depot->setEmeteurAccount($currentAccount);
            
                //preparation des envois vers les bdd
                $entityManager->persist($currentAccount);
                $entityManager->persist($depot);
                $entityManager->flush();
            }

            if($data['type'] === "retrait"){
                $accountRepository = $entityManager->getRepository(Account::class);
                //effectue l'opération

                if($currentAccount->getBalance()< $data['amount'] && $currentAccount->getOverdraft() < $data['amount']){
                    $this->addFlash('error', "Votre solde est Insuffisant");
                }
                else {
                    $currentAccount->setBalance($currentAccount->getBalance() - $data['amount']);

                    //crée l'objet Transaction
                    $retrait = new Transaction();
                    $retrait->setType('Retrait');
                    $retrait->setAmount($data['amount']);
                    $retrait->setEmeteurAccount($currentAccount);
            
                    //preparation des envois vers les bdd
                    $entityManager->persist($currentAccount);
                    $entityManager->persist($retrait);
                    $entityManager->flush();
                }
            }

        }

        $form3 = $this->createForm(BuyOnlineFormType::class, null, ['accounts' => $accounts]);
        $form3->handleRequest($request);

        if ($form3->isSubmitted() && $form3->isValid()) {
            $data = $form3->getData();
            //recupere le compte passé dans le formulaire
            $accountRepository = $entityManager->getRepository(Account::class);

            if($currentAccount->getBalance()< $data['amount'] && $currentAccount->getOverdraft() < $data['amount']){
                $this->addFlash('error', "Votre solde est Insuffisant");
            }
            else {

                //effectue l'opération
                $currentAccount->setBalance($currentAccount->getBalance() - $data['amount']);

                //crée l'objet Transaction
                $buyonline = new Transaction();
                $buyonline->setType('Achat en ligne');
                $buyonline->setAmount($data['amount']);
                $buyonline->setDescription('Paiment en ligne Amazone');
                $buyonline->setEmeteurAccount($currentAccount);
                            
                
                
                //preparation des envois vers les bdd
                $entityManager->persist($currentAccount);
                $entityManager->persist($buyonline);
                $entityManager->flush();
            }

        }

        return $this->render('home/transaction.html.twig', [
            'VirementFormType' => $form->createView(),
            'RetraitDepotFormType' => $form2->createView(),
            'BuyOnlineFormType' => $form3->createView(),
        ]);
    }

}

   // if ($data['quantity'] >= 5) {
            //     $newSharePrice = $sharePrice * 0.95; 
            //     $company->setSharePrice($newSharePrice);
            //     $entityManager->persist($company);
            // }
    