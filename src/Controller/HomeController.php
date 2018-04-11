<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 09/04/2018
 * Time: 13:07
 */

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkType;
use App\Service\LinkManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @param LinkManager $lm
     * @return Response
     */
    public function home()
    {
        $linkForm = $this->createForm(LinkType::class)->createView();
        return $this->render('home/homepage.html.twig', ['linkForm'=>$linkForm]);
    }

    public function handleHomeForm(Request $request, LinkManager $lm)
    {
        $link = new Link();
        $linkForm = $this->createForm(LinkType::class, $link)->handleRequest($request);

        if ($linkForm->isSubmitted() && $linkForm->isValid()) {
            /** @var Link $link */
            $link = $linkForm->getData();
            $link->setUuid($lm->getUuid());

             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($link);
             $entityManager->flush();

            return new JsonResponse('<span>https://lessn.io/'.$link->getUuid().'</span>');
        }

        return new JsonResponse($this->render('home/homeForm.html.twig',
            [
                'linkForm'=>$linkForm->createView(),
            ]
        )->getContent());

    }
}
