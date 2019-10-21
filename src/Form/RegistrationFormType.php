<?php

namespace App\Form;

use App\Dto\RegistrationDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType ;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username', TextType::class, [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Nazwa użytkownika'
                    ],
                ])
                ->add('email', EmailType::class, [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Email'
                    ],
                ])
                ->add('country', CountryType::class, [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Kraj'
                    ]
                ])
                ->add('birthday', DateType::class, [
                    'widget' => 'single_text',
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Data urodzenia'
                    ],
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'mapped' => true,
                    'label' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                                ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Your password should be at least {{ limit }} characters long',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                                ]),
                    ],
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options' => ['label' => false, 'attr' => ['placeholder' => 'Hasło']],
                    'second_options' => ['label' => false, 'attr' => ['placeholder' => 'Powtórz hasło']]
                ])
                ->add('avatar', FileType::class, [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Wybierz avatar'
                    ]
                ])
                ->add('termsAccepted', CheckboxType::class, [
                    'mapped' => false,
                    'label' => 'Akceputję warunki korzystania z serwisu',
                    'constraints' => new IsTrue(),
                ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => RegistrationDto::class,
        ]);
    }

}
