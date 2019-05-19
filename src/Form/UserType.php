<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 16/04/2018
 * Time: 17:31
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Routing\RouterInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate('app_security_register'))
            ->add(
                'username',
                TextType::class,
                [
                    'label_attr' => ['style'=>'display : none;',],
                    'attr' =>
                        [
                            'class' => 'stylish-input',
                            'placeholder' => 'app.signup.form.placeholer.username',
                        ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label_attr' => ['style'=>'display : none;',],
                    'attr' =>
                        [
                            'class' => 'stylish-input',
                            'placeholder' => 'app.signup.form.placeholer.email',
                        ],
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' =>
                        [
                            'attr' =>
                                [
                                    'placeholder' => 'app.signup.form.placeholer.password',
                                    'class' => 'stylish-input',
                                ]
                        ],
                    'second_options' =>
                        [
                            'attr' =>
                                [
                                    'placeholder' => 'app.signup.form.placeholer.repeatpassword',
                                    'class' => 'stylish-input',
                                ]
                        ],
                    'options' =>
                        [
                            'label_attr' => ['style' => 'display : none;',],
                        ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'attr' =>
                    [
                        'class' => 'ajax-form',
                        'id' => 'register-form',
                        'novalidate' => 'novalidate',
                    ]
            ]
        );
    }

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
}
