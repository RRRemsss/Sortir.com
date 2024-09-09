<?php

namespace App\Form;

use App\DTO\searchActivityDTO;
use App\Entity\Campus;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchActivityDTOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'class'=>Campus::class,
                'choice_label'=>'campusName',
                'required'=>false,
                'data' => $options['default_campus'],
            ])
            ->add('activityName', TextType::class, [
                'label'=> 'Recherche par mot clé : ',
                'required'=> false,
                'empty_data'=>'',
            ])
            ->add('filterDateMin', DateType::class,[
                'label'=>'Entre le : ',
                'required'=>false,
                'html5'=> true,
                'widget' => 'single_text'
            ])
            ->add('filterDateMax', DateType::class,[
                'label'=>'et le : ',
                'required'=>false,
                'html5'=> true,
                'widget' => 'single_text'
            ])
            ->add('checkboxOrganizer', CheckboxType::class,[
                'label'=>'Sorties dont je suis l\'organisateur/trice',
                'required'=>false,
                'data' => false
            ])
            ->add('checkBoxBooked', CheckboxType::class,[
                'label'=>'Sorties auxquelles je suis incrit/e',
                'required'=>false,
                'data' => false
            ])
            ->add('checkBoxNotBooked', CheckboxType::class,[
                'label'=>'Sorties auxquelles je ne suis pas incrit/e',
                'required'=>false,
                'data' => false
            ])
            ->add('activityPassed', CheckboxType::class,[
                'label'=>'Sorties passées',
                'required'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => searchActivityDTO::class,
            'allow_extra_fields' => true,
            'default_campus' => null,
        ]);
        $resolver->setAllowedTypes('default_campus', ['null', Campus::class]);
    }
}
