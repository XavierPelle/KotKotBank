<?php

namespace App\Controller\Admin;

use App\Entity\Portfolio;
use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;

class PortfolioCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Portfolio::class;
    }


    
    public function configureFields(string $pageName): iterable
    {
        return [
            
            IdField::new('Id'),
            // TextField::new('name'),
            TextField::new('Share Name'),
            MoneyField::new('Price')->setCustomOption('storedAsCents', false)->setCurrency('EUR'),
            IntegerField::new('Quantity'),
            MoneyField::new('Total Price')->setCustomOption('storedAsCents', false)->setCurrency('EUR'),
        ];
    }
    
}
