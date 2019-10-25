<?php
namespace AppBundle\EventListener;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing\Router;

class SecurityListener
{
    protected $router;
    protected $security;
    protected $dispatcher;

    public function __construct(Router $router, AuthorizationChecker $authorizationChecker,EventDispatcher $dispatcher)
    {
        $this->router = $router;
        $this->security = $authorizationChecker;
        $this->dispatcher = $dispatcher;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $response = new RedirectResponse($this->router->generate('users_list'));
        } elseif ($this->security->isGranted('ROLE_VENDOR')) {
            $response = new RedirectResponse($this->router->generate('users_add'));
        } else {
            $response = new RedirectResponse($this->router->generate('users_list'));
        }
        $event->setResponse($response);
    }
}