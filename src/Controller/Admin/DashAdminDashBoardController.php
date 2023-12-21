<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Entity\Company;
use App\Entity\Events;
use App\Entity\Portfolio;
use App\Entity\ShareTransaction;

use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashAdminDashBoardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $url = $adminUrlGenerator->setController(ClientCrudController::class)->generateUrl();
        return $this->redirect($url);

       

        
        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('my/dashbord.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('KotkotBank - Administration')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-box-archive');
        yield MenuItem::linktoRoute("Page d'accueil", 'fas fa-home', 'app_feed');
        yield MenuItem::linktoRoute("Page Bourse", 'fas fa-dollar-sign', 'action');

        
        yield MenuItem::subMenu('Portefeuille-Client', 'fas fa-list')->setSubItems([
            MenuItem::linkToCrud('Client', 'fas fa-users', Client::class),
            MenuItem::linkToCrud('Account', 'fas fa-wallet', Account::class),
            MenuItem::linkToCrud('Transaction', 'fas fa-eye', Transaction::class),
        ]);
        
        
        yield MenuItem::subMenu('BookMark', 'fas fa-list')->setSubItems([
            MenuItem::linkToCrud('Compagny', 'fas fa-eye', Company::class),
            MenuItem::linkToCrud('Portefolio', 'fas fa-wallet', Portfolio::class),
            MenuItem::linkToCrud('Share Transaction', 'fas fa-eye', ShareTransaction::class),
            MenuItem::linkToCrud('Events', 'fas fa-bell', Events::class),
        ]);
    }
}