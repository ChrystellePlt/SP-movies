<?php
/**
 * Created by PhpStorm.
 * User: chrystelle
 * Date: 2020-03-03
 * Time: 17:20
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class IndexController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('pages/index.html.twig');
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('pages/login.html.twig', [
            'error' => $error
        ]);
    }
}
