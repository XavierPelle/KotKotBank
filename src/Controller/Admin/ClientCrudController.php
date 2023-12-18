<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\Account;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ClientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    // public static function getAccountFqcn(): string
    // {
    //     return Account::class;
    // }

//     public function configureCrud (Crud $crud): Crud
// {

//     return $crud;
// }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('firstname'),
            TextField::new('lastname'),
            TextField::new('email'),
            DateTimeField::new('registration_date'),
        ];
    }
    
}