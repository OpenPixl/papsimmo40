<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class SessionService
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
        // Accessing the session in the constructor is *NOT* recommended, since
        // it might not be accessible yet or lead to unwanted side-effects
        // $this->session = $requestStack->getSession();
    }

    public function Timeless()
    {
        $session = $this->requestStack->getSession();

        $sessionCreated = $session->getMetadataBag()->getCreated();
        $sessionLastused = $session->getMetadataBag()->getLastUsed();
        $sessionLifetime = ($session->getMetadataBag()->getLifetime());

        $timeless = ($sessionCreated + $sessionLifetime) - $sessionLastused;

        return $timeless;
    }
}