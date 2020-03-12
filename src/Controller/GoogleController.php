<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GoogleController extends AbstractController
{
    /**
     * @param ClientRegistry $clientRegistry
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'profile', 'email'
            ]);
    }

    /**
     * @param Request        $request
     * @param ClientRegistry $clientRegistry
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        return $this->redirectToRoute('home');
    }
}
