<?php

namespace App\Controller\Admin;

use App\Entity\ShareTransaction;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class ShareTransactionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ShareTransaction::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            // TextField::new('Client'),
            TextField::new('Share Name'),
            MoneyField::new('Share Price')->setCustomOption('storedAsCents', false)->setCurrency('EUR'),
            IntegerField::new('Quantity'),
            MoneyField::new('Total Price')->setCustomOption('storedAsCents', false)->setCurrency('EUR'),
            TextField::new('Type'),

        ];
    }

}