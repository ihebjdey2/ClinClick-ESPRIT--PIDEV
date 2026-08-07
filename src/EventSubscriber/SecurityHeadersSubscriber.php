<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    private const PRIVATE_PATH_PREFIXES = ['/medical', '/rendez-vous', '/profile'];

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => 'onKernelResponse'];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $path = $event->getRequest()->getPathInfo();
        foreach (self::PRIVATE_PATH_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $response->headers->addCacheControlDirective('private');
                $response->headers->addCacheControlDirective('no-store');
                $response->headers->set('Pragma', 'no-cache');

                break;
            }
        }
    }
}
