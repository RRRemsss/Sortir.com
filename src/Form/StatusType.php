<?php

namespace App\Form;

use App\Entity\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statusStatement',ChoiceType::class, [
                'choices'=> [
                    'Annulée'=> 'Annulée',
                    'Passée' => 'Passée',
                    'En cours'=>'En cours',
                    'En création'=>'En création',
                    'Ouverte'=> 'Ouverte',
                    'Fermée'=> 'Fermée',
                    'Complète'=> 'Complète',

                ],
                'multiple'=> false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Status::class,
        ]);
    }
}
