<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 12:50
 */

namespace App\Form;

use App\Entity\Link;
use App\Validator\Constraints\ValidLessnLink;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class LinkReviewType extends AbstractType
{
    const REVIEW_FORM_ID = 'linkreview_form';

    private $router;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate('app_link_review_link'))
            ->add('URL', TextType::class,
                [
                    'required' => true,
                    'error_bubbling' => true,
                    'attr' => ['placeholder'=>'app.linkchecker.form.placeholder.link', 'class'=>'stylish-input', 'autocomplete' => "off", ],
                    'label_attr' => ['style'=>'display : none;', ],
                    'constraints' => [
                        new NotBlank(),
                        new ValidLessnLink(),
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' =>
                [
                    'id' => self::REVIEW_FORM_ID,
                    'novalidate' => 'novalidate',
                    'class' => 'ajax-form'
                ]
        ]);
    }

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
}