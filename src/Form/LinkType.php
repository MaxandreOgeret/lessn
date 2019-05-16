<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 12:50
 */

namespace App\Form;

use App\Entity\Link;
use App\Service\SafeBrowsing\CanonicalizeManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class LinkType extends AbstractType
{
    const HOME_FORM_ID = 'home_form';

    private $router;
    private $canonicalizeManager;

    public function __construct(RouterInterface $router, CanonicalizeManager $canonicalizeManager)
    {
        $this->router = $router;
        $this->canonicalizeManager = $canonicalizeManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate('app_handle_home_form'))
            ->add(
                'URL',
                TextType::class,
                [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Paste link here',
                        'class' => 'stylish-input',
                        'autocomplete' => "off",
                    ],
                    'label_attr' => ['style' => 'display : none;',],
                ]
            )
            ->addEventListener(FormEvents::SUBMIT, [$this, 'postSubmitListener'])
        ;
    }

    public function postSubmitListener(FormEvent $event)
    {
        /** @var Link $link */
        $link = $event->getData();
        $canonUrl = $this->canonicalizeManager->canonicalize($link->getUrl(), true);
        $link->setUrl($canonUrl);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Link::class,
            'attr' =>
                [
                    'id' => self::HOME_FORM_ID,
                    'novalidate' => 'novalidate',
                    'class' => 'ajax-form'
                ]
        ]);
    }
}
