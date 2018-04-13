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
     * @return Response
     */
    public function home()
    {
        $linkForm = $this->createForm(LinkType::class)->createView();
        return $this->render('home/homepage.html.twig', ['linkForm'=>$linkForm]);
    }

    public function handleHomeForm(Request $request, LinkManager $lm)
    {
        $em = $this->getDoctrine()->getManager();

        if ($lm->spamProtection($request->getClientIp(), $em)) {
            return new JsonResponse('
                <span>You shortened too many links, please wait one minute.</span>
                <button id="reset-home" class="btn btn-link link">
                <i  class="fas fa-undo-alt"></i></button>
             ');
        }

        $link = new Link($request);
        $linkForm = $this->createForm(LinkType::class, $link)->handleRequest($request);

        if ($linkForm->isSubmitted() && $linkForm->isValid()) {
            /** @var Link $link */
            $link = $linkForm->getData();
            $link->setUuid($lm->getUuid());

             $em->persist($link);
             $em->flush();

             return new JsonResponse($this->render("home/link.html.twig", ['uuid' => $link->getUuid()])->getContent());

        }

        return new JsonResponse($this->render('home/homeForm.html.twig',
            [
                'linkForm'=>$linkForm->createView(),
            ]
        )->getContent());

    }
}
