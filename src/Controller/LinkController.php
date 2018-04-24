<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 09/04/2018
 * Time: 13:07
 */

namespace App\Controller;

use App\Entity\Link;
use App\Entity\LogLink;
use App\Form\LinkType;
use App\Service\LinkManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LinkController extends Controller
{
    /**
     * @param $uuid
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function linkHandler(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Link $link */
        $link = $em->getRepository(Link::class)->findOneByUuid($uuid);

        if (is_null($link)) {
            return $this->redirectToRoute('app_main_route');
        }

        $log = new LogLink($request, $link);

        $em->persist($log);
        $em->flush();

        return $this->redirect($link->getURL());
    }
}
