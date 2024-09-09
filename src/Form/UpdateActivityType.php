<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Place;
use ContainerFxHegUG\getPlaceControllerService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class UpdateActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);
        $builder

            ->add('activityName',TextType::class, [
                'label' =>'Nom de la sortie',
                'required'=>false])

            ->add('dateTimeStart',DateTimeType::class, [
                'html5'=> true,
                'widget'=> 'single_text',
                'label'=> 'Date et heure de la sortie',
                'data' => new \DateTime('+ 1 day'),
                'required'=>false,
                'attr' => [
                    'min' => (new \DateTime('now'))->format('Y-m-d\TH:i'),
                ],

            ])

            ->add('dateLimitInscription', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription',
                'data' => new \DateTime(),
                'required' => false,
                'attr' => [
                    'min' => (new \DateTime('now'))->format('Y-m-d'),
                ],
            ])

            ->add('nbMaxIncriptions', IntegerType::class, [
                'label'=>'Nombre de place',
                'required'=>false
            ])

            ->add('duration',IntegerType::class, [
                'label'=>'Durée',
                'data' => '90',
                'required'=>false
            ])

            ->add('activityDescription',TextareaType::class,[
                'label'=>'Description et infos',
                'required'=>false
            ])

            ->add('campus', EntityType::class,[
                'class'=>Campus::class,
                'choice_label'=>'campus_Name'
            ])

            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'city_name',
                'label'=>'Ville',
                'placeholder'=>'Selectionner une ville',
                'mapped' => false,
                'attr' => ['onchange' => 'this.form.submit()']
            ])

            ->addDependent('postalCode', 'city', function (DependentField $field, ?City $city) {
                if($city !== null){
                    $field->add(TextType::class, [
                            'label' => 'Code postal',
                            'data' => $city?->getPostCode(),
                            'disabled' => true,
                            'mapped'=>false,
                    ]);
                }
            })

            ->addDependent('place', 'city', function (DependentField $field, ?City $city){
                if($city !== null) {
                    $field->add(EntityType::class, [
                        'class' => Place::class,
                        'choices' => $city?->getPlace(),
                        'placeholder' => 'Selectionner un lieu',
                        'choice_label' => 'place_name',
                        'disabled' => null == $city,
                        'required'=>true,
                        'attr' => ['onchange' => 'this.form.submit()']
                    ]);
                }
            })

            ->addDependent('street', 'place', function (DependentField $field, ?Place $place) {
                if($place !== null){
                    $field->add(TextType::class, [
                        'label' => 'Rue',
                        'data' => $place?->getStreet(),
                        'disabled' => true,
                        'mapped'=>false
                    ]);
                }
            })

            ->addDependent('latitude', 'place', function (DependentField $field, ?Place $place) {
                if($place !== null) {
                    $field->add(TextType::class, [
                        'label' => 'Latitude',
                        'data' => $place?->getLatitude(),
                        'disabled' => true,
                        'mapped' => false
                    ]);
                }
            })

            ->addDependent('longitude', 'place', function (DependentField $field, ?Place $place) {
                if($place !== null) {
                    $field->add(TextType::class, [
                        'label' => 'Longitude',
                        'data' => $place?->getLongitude(),
                        'disabled' => true,
                        'mapped' => false,
                    ]);
                }
            })

            ->add('submit', SubmitType::class, [
                'label'=>'Modifier',
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
