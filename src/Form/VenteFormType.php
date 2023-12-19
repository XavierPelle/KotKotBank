<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\ShareTransaction;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type as Types;


class VenteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $transactions = $options['transactions'];
        $choices = [];
        
        foreach ($transactions as $transaction){

            $choices[$transaction->getShareName()] = $transaction->getShareName();
        }
        $builder
            ->add('shareName', ChoiceType::class, [
                'label' => 'choisir l\'action',
                'choices' => $choices,
            ])
            ->add('quantity')
            ->add('envoyer', Types\SubmitType::class, [
                'attr' => [
                    'class' => 'form-control mt-4',
                ]]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
        ]);
        $resolver->setRequired('transactions');
    }
}