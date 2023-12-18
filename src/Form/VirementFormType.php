<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\Transaction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class VirementFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $accounts = $options['accounts'];
        $choices = [];
        
        foreach ($accounts as $account){

            $choices["compte numéro : " .$account->getId()] = $account;
        }

        $builder
            ->add('amount', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Choississez un montant',
                ]
            ])
            ->add('beneficiaryaccount', ChoiceType::class, [
                'label' => false,
                'choices' => $choices,
                'attr' => [
                    'class' => 'form-control mb-1 "',
                ]
            ])
            ->add('description', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Merci pour la pizza...',
                ]
            ])
            ->add('envoyer', Types\SubmitType::class, [
                'attr' => [
                    'class' => 'form-control mt-4',
                ]]);
 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => Transaction::class,
        ]);
        $resolver->setRequired('accounts');
    }
}