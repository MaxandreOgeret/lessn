<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 09/04/2018
 * Time: 13:07
 */

namespace App\Controller;

use App\Entity\Link;
use App\Service\UriManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    private $uriManager;

    public function __construct(UriManager $uriManager)
    {
        $this->uriManager = $uriManager;
    }

    public function validateLink(Request $request, ValidatorInterface $validator)
    {
        $link = new Link($request);
        $link->setUuid($request->query->get('uuid'))
            ->setUrl($this->uriManager->format($request->query->get('url')));

        $violationList = $validator->validate($link);

        if (count($violationList) === 0) {
            return new JsonResponse(['status' => 'ok']);
        }
        $messages = [];
        foreach ($violationList as $violation) {
            $messages[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return new JsonResponse(['status' => 'ko', 'messages' => $messages]);
    }
}
