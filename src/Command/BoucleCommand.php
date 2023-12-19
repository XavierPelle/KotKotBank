<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Company; 
use App\Entity\Events; 
#[AsCommand(
    name: 'boucle',
    description: 'Cycle toutes les 10 secondes'
)]
class BoucleCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
       
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
    
        $io->success('Début de la mise à jour des prix');
    
        while (true) {
            $this->event($io);
            $this->newPrice($io);
    
            $this->entityManager->flush();
            $io->success('Mise à jour terminée.');
            sleep(10);
        }
        return Command::SUCCESS;
    }
    
    private function event(SymfonyStyle $io): void
    {
        $events = $this->entityManager->getRepository(Events::class)->findAll();
        $companies = $this->entityManager->getRepository(Company::class)->findAll();
    
        foreach ($events as $event) {
            if (mt_rand(0, 100) / 100 <= $event->getProbability()) {
                foreach ($companies as $company) {
                    if ($company->getDomain() === $event->getDomain()) {
                        $currentPrice = $company->getSharePrice();
                        $impact = $event->getImpact();
                        $newPrice = round($currentPrice * (1 + $impact / 100), 2);
                        $company->setSharePrice($newPrice);
                        $io->text("Événement '{$event->getName()}' affectant le domaine '{$event->getDomain()}' s'est produit. Impact de {$impact}% appliqué.");
                    }
                }
                $event->setProbability(0.01);
            } else {
                $event->setProbability(min($event->getProbability() + 0.01, 0.99));
            }
        }
    }

    private function newPrice(SymfonyStyle $io): void
    {
        $companies = $this->entityManager->getRepository(Company::class)->findAll();
    
        foreach ($companies as $company) {
            $currentPrice = $company->getSharePrice();
            $change = $currentPrice * 0.02;
            $newPrice = mt_rand(0, 1) ? round($currentPrice + $change, 2) : round($currentPrice - $change, 2);
            $company->setSharePrice($newPrice);
        }
    }
}
