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
use App\Service\UriManager;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    private $uriManager;
    private $userManager;

    /**
     * HomeController constructor.
     * @param UriManager $uriManager
     */
    public function __construct(UriManager $uriManager, UserManager $UserManager)
    {
        $this->uriManager = $uriManager;
        $this->userManager = $UserManager;
    }

    public function changeLocale(Request $request)
    {
        $locale = $locale = $request->getLocale();
        if ($this->getUser()) {
            $this->userManager->updateUserLocale($this->getUser(), $locale);
        }
        $this->get('session')->set('_locale', $locale);
        $request->setLocale($request->getLocale());

        return $this->redirectToRoute('app_main_route_withlang', ['_locale' => $request->getLocale()]);
    }

    public function homeNoLocale(Request $request)
    {
        $locale = $request->getLocale();
        if ($this->getUser()) {
            $locale = $this->getUser()->getLocale();
        }

        $request->setLocale($locale);

        return new RedirectResponse($this->generateUrl('app_main_route_withlang', ['_locale' => $locale]), 302, ['_locale' => $locale]);

        return $this->redirectToRoute('app_main_route_withlang', ['_locale' => $locale]);

    }

    /**
     * @return Response
     */
    public function home(Request $request)
    {
        if ($this->getUser()) {
            $this->get('session')->set('_locale', $this->getUser()->getLocale());
            $request->setLocale($this->getUser()->getLocale());
        }

        $linkForm = $this->createForm(LinkType::class)->createView();
        return $this->render('home/homepage.html.twig', ['linkForm'=>$linkForm]);
    }

    public function handleHomeForm(Request $request, LinkManager $lm)
    {
        $em = $this->getDoctrine()->getManager();

        $ip = $request->getClientIp();
        $userAgent = $request->headers->get('User-Agent');

        //if spam and if non authenticated then too many links error
        if ($lm->spamProtection($ip, $userAgent, $em) && !$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse($this->render("home/link.html.twig",
                [
                    'status' => 'toomany',
                ]
            )->getContent());
        }

        $link = new Link($request);
        $linkForm = $this->createForm(LinkType::class, $link)->handleRequest($request);

        if ($linkForm->isSubmitted() && $linkForm->isValid()) {
            /** @var Link $link */
            $link = $linkForm->getData();
            $link->setUuid($lm->getUuid())
                 ->setUser($this->getUser());

            $link->setUrl($this->uriManager->format($link->getUrl()));

             $em->persist($link);
             $em->flush();

             return new JsonResponse($this->render("home/link.html.twig",
                 [
                     'status' => 'ok',
                     'uuid' => $link->getUuid(),
                 ]
             )->getContent());
        }

        return new JsonResponse($this->render('home/homeForm.html.twig',
            [
                'linkForm'=>$linkForm->createView(),
            ]
        )->getContent());

    }

    public function conditionsOfUse() {
        return new JsonResponse($this->render('full page/cou.html.twig',
            [
                'version'=> exec('git describe --tags --abbrev=0')
            ]
        )->getContent());
    }

}
