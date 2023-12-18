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

class RetraitDepotFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $accounts = $options['accounts'];
        $choices = [];
        // $operations = $options['operations'];
        // $types = [];
        
        foreach ($accounts as $account){

            $choices["compte numéro : " .$account->getId()] = $account->getId();
        }
        
        $builder
            ->add('type', ChoiceType::class, array(
                'label' => False,
                'choices' => array('Dépôt' => 'depot', 'Retrait' => 'retrait'),
                'attr' => [
                    'class' => 'form-control mb-1 "',
                ]
            ))
            ->add('amount', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Choississez un montant',
                ]
            ])
            ->add('envoyer', Types\SubmitType::class, [
                'attr' => [
                    'class' => 'form-control mt-4  cursor-pointer',
                ]]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => Transaction::class,
        ]);
        $resolver->setRequired('accounts');
    }
}