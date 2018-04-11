<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 12:50
 */

namespace App\Form;

use App\Entity\Link;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkType extends AbstractType
{
    const HOME_FORM_ID = 'home_form';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('URL', TextType::class,
                [
                    'required' => true,
                    'attr' => ['placeholder'=>'Paste here', 'class'=>'stylish-input'],
                    'label_attr' => ['style'=>'display : none;',],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Link::class,
            'attr' =>
                [
                    'id' => self::HOME_FORM_ID,
                    'novalidate' => 'novalidate',
                ]
        ));
    }
}