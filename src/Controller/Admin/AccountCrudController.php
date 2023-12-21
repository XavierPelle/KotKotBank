<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\Account;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [

            MoneyField::new('total')->setCustomOption('storedAsCents', false)->setCurrency('EUR'),
            MoneyField::new('balance')->setCustomOption('storedAsCents', false)->setCurrency('EUR'),
            MoneyField::new('overdraft')->setCustomOption('storedAsCents', false)->setCurrency('EUR'),
            TextField::new('type'),
            DateTimeField::new('date'),


        ];
    }


    
}
