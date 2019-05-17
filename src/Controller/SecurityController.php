<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 16/04/2018
 * Time: 13:27
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $helper
     * @return Response
     */
    public function login(AuthenticationUtils $helper): Response
    {
        $error = $helper->getLastAuthenticationError();
        if (is_null($error)) {
            return new JsonResponse([$this->render(
                'security/login.html.twig',
                [
                    // dernier username saisi (si il y en a un)
                    'last_username' => $helper->getLastUsername(),
                    // La derniere erreur de connexion (si il y en a une)
                    'error' => $helper->getLastAuthenticationError(),
                ]
            )->getContent()]);
        }
        return new JsonResponse([false, $error->getMessage()]);
    }

    /**
     * La route pour se deconnecter. Mais celle ci ne doit jamais être executé car symfony l'interceptera avant.
     *
     * @throws \Exception
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * @Route("/register", name="user_registration")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        AuthenticationUtils $helper
    ) {
        $user = new User();

        $form = $this->createForm(UserType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // Par defaut l'utilisateur aura toujours le rôle ROLE_USER
            $user->setRoles(['ROLE_USER']);

            // On enregistre l'utilisateur dans la base
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->forward('App\Controller\SecurityController:login');
        }

        return new JsonResponse($this->render(
            'security/register.html.twig',
            [
                'form'=>$form->createView(),
            ]
        )->getContent());
    }
}
