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

}
