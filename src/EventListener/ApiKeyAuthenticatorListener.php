<?php

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ApiKeyAuthenticatorListener
{
    private string $apiKey;
    private Security $security;

    public function __construct(string $apiKey, Security $security)
    {
        $this->apiKey = $apiKey;
        $this->security = $security;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (str_starts_with($request->getPathInfo(), '/api/')) {
            if ($this->security->getUser()) {
                return;
            }

            $requestApiKey = $request->headers->get('X-API-KEY');

            if ($requestApiKey !== $this->apiKey) {
                throw new AccessDeniedHttpException('Invalid API Key');
            }
        }
    }
}