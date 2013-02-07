<?php

namespace Zerebral\CommonBundle\KissMetrics\EventHandler;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

use Zerebral\CommonBundle\KissMetrics\KissMetrics;
use Symfony\Component\Security\Core\SecurityContext;

class IdentityEventHandler
{
    /**
     * @var KissMetrics
     */
    private $kissMetrics = null;

    /**
     * @var SecurityContext
     */
    private $securityContext = null;

    public function __construct(KissMetrics $kissMetrics, SecurityContext $securityContext)
    {
        $this->setKissMetrics($kissMetrics);
        $this->setSecurityContext($securityContext);
    }

    public function onRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            // don't do anything if it's not the master request
            return;
        }

        $token = $this->getSecurityContext()->getToken();
        if (!empty($token)) {
            $this->getKissMetrics()->identify($token->getUsername());
        }
    }

    /**
     * @param \Zerebral\CommonBundle\KissMetrics\KissMetrics $kissMetrics
     */
    public function setKissMetrics($kissMetrics)
    {
        $this->kissMetrics = $kissMetrics;
    }

    /**
     * @return \Zerebral\CommonBundle\KissMetrics\KissMetrics
     */
    public function getKissMetrics()
    {
        return $this->kissMetrics;
    }

    /**
     * @param \Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    public function setSecurityContext($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    public function getSecurityContext()
    {
        return $this->securityContext;
    }


}
