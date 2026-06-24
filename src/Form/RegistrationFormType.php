<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * Registration form used to create a new user account.
 *
 * This form:
 * - Get the user name
 * - Get the user email
 * - Get the user password
 * - Check password confirmation
 * - Check form validation rules
 *
 * The plainPassword is hashed before saving
 */
class RegistrationFormType extends AbstractType
{
    /**
    * Build the registration form fields.
    */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre nom',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères',
                    ]),
                    ],
                    ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options'  => [
                'label' => 'Mot de passe',
                ],
                'second_options' => [
                'label' => 'Confirmer le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Assert\Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*\d).+$/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule et un chiffre',
                    ]),
                ],
            ]);
    }

    /**
    * Link the form to the User entity.
    */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}