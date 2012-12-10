<?php

namespace Zerebral\CommonBundle\Security;

use \Symfony\Component\DependencyInjection\ContainerInterface;
use \Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use \Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

abstract class ModelAccessVoter implements VoterInterface
{

    /**
     * {@inherit}
     */
    public function supportsAttribute($attribute)
    {
        return in_array(strtoupper($attribute), $this->getSupportedAttributes());
    }

    /**
     * {@inherit}
     */
    public function supportsClass($class)
    {
        return true;
    }

    protected function getSupportedAttributes()
    {
        return array('VIEW', 'EDIT', 'ADD', 'DELETE');
    }

    abstract protected function getModelClass();

    /**
     * {@inherit}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {

        if (is_null($object)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (!is_a($object, $this->getModelClass())) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (!$token->isAuthenticated()) {
            return VoterInterface::ACCESS_DENIED;
        }

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            if (!$this->isGranted($token, $object, $attribute)) {
                return VoterInterface::ACCESS_DENIED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }

    abstract public function isGranted(TokenInterface $token, $object, $attribute);
}
