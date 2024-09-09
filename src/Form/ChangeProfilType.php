<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints as Assert;

// Définition du formulaire de modification de profil
class ChangeProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Ajout des champs du formulaire avec leurs contraintes de validation respectives

        // Champ "username" avec contraintes NotBlank, Length et Regex
        $builder->add('username', null, [
            'label' => 'Nom d\'utilisateur',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom d\'utilisateur ne doit pas être vide.'
                ]),
                new Assert\Length([
                    'max' => 50,
                    'maxMessage' => 'Le nom d\'utilisateur ne doit pas dépasser {{ limit }} caractères.'
                ]),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9_]+$/',
                    'message' => 'Le nom d\'utilisateur ne doit contenir que des lettres, des chiffres et des underscores.'
                ])
            ]
        ]);

        // Champ "lastname" avec contraintes NotBlank, Length et Regex
        $builder->add('lastname', null, [
            'label' => 'Nom de famille',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom de famille ne doit pas être vide.'
                ]),
                new Assert\Length([
                    'max' => 50,
                    'maxMessage' => 'Le nom de famille ne doit pas dépasser {{ limit }} caractères.'
                ]),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z]+$/',
                    'message' => 'Le nom de famille ne doit contenir que des lettres.'
                ])
            ]
        ]);

        // Champ "firstname" avec contraintes NotBlank, Length et Regex
        $builder->add('firstname', null, [
            'label' => 'Prénom',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le prénom ne doit pas être vide.'
                ]),
                new Assert\Length([
                    'max' => 50,
                    'maxMessage' => 'Le prénom ne doit pas dépasser {{ limit }} caractères.'
                ]),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z]+$/',
                    'message' => 'Le prénom ne doit contenir que des lettres.'
                ])
            ]
        ]);

        // Champ "phoneNumber" avec contraintes Regex et Length
        $builder->add('phoneNumber', null, [
            'label' => 'Numéro de téléphone',
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^[0-9]*$/',
                    'message' => 'Le numéro de téléphone ne doit contenir que des chiffres.'
                ]),
                new Assert\Length([
                    'max' => 10,
                    'maxMessage' => 'Le numéro de téléphone ne doit pas dépasser {{ limit }} chiffres.'
                ])
            ]
        ]);

        // Champ "email" de type EmailType
        $builder->add('email', EmailType::class, [
            'label' => 'Adresse e-mail'
        ]);

        // Champ "imageFile" de type VichImageType pour la gestion de l'image de profil
        $builder->add('imageFile', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'label' => 'Photo de profil',
            'label_attr' => [
                'class' => 'form-label mt-4'
            ],
            'attr' => [
                'class' => 'form-control',
            ],
        ]);

        // Champ "campus" de type EntityType pour la sélection du campus
        $builder->add('campus', EntityType::class, [
            'class' => Campus::class,
            'choice_label' => 'campusName',
            'label' => 'Campus'
        ]);
    }

    // Configuration des options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
