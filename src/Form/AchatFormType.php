<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\ChoiceList\ChoiceList;

class AchatFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', EntityType::class, [
                'class' => Company::class,
                'attr' => [
                    'class' => 'form-control mb-2'
                ]
            ])
            // ->add('domain')
            // ->add('sharePrice')
            ->add('quantity', TextType::class, [
                'attr' => [
                'class' => 'form-control mb-2'
                ]])
            ->add('envoyer', Types\SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4',
                ]]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
        ]);
    }
}