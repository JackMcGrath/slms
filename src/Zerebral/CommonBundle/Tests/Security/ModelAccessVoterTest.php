<?php

namespace Zerebral\CommonBundle\Tests\Security;

use Zerebral\CommonBundle\Security\ModelAccessVoter;
use Zerebral\BusinessBundle\Model\User\User;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ModelAccessVoterTest extends \Zerebral\CommonBundle\Tests\TestCase
{

    protected function mockSecurityToken($user = null)
    {
        if (is_null($user)) {
            $user = new User();
        }
        $token = new PreAuthenticatedToken($user, array(), 'secret_key', $user->getRoles());
        return $token;
    }

    /**
     * @return ModelAccessVoter
     */
    protected function mockVoter($isGranted = true, $targetClass = 'MyClass')
    {
        $voter = $this->getMockForAbstractClass('Zerebral\CommonBundle\Security\ModelAccessVoter');
        $voter
            ->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue($isGranted));

        $voter->expects($this->any())
            ->method('getModelClass')
            ->will($this->returnValue($targetClass));

        return $voter;
    }

    protected function mockTargetObject()
    {
        $class = $this->getMockClass("MyClass", array());
        return new $class();
    }

    public function testVoteOnEmptyOrDifferentObject()
    {
        $voter = $this->mockVoter();
        $token = $this->mockSecurityToken();

        $this->assertNotEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, null, array('VIEW')));
        $this->assertNotEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, new User(), array('VIEW')));
    }

    public function testVoteOnUnsupportedAttribute()
    {
        $voter = $this->mockVoter();
        $token = $this->mockSecurityToken();

        $this->assertNotEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $this->mockTargetObject(), array('UNSUPPORTED_ATTRIBUTE')));
    }

    public function testVoteByGuest()
    {
        $voter = $this->mockVoter();
        $token = $this->mockSecurityToken();
        $token->setAuthenticated(false);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $this->mockTargetObject(), array('VIEW')));
    }

    public function testIsGrantedCalled()
    {
        $voter = $this->getMockForAbstractClass('Zerebral\CommonBundle\Security\ModelAccessVoter');
        $voter
            ->expects($this->once())
            ->method('isGranted')
            ->will($this->returnValue(true));

        $voter->expects($this->any())
            ->method('getModelClass')
            ->will($this->returnValue('MyClass'));
        $token = $this->mockSecurityToken();

        $voter->vote($token, $this->mockTargetObject(), array('VIEW'));
    }

    public function testIsGranted()
    {
        $token = $this->mockSecurityToken();

        $voter = $this->mockVoter(true);
        $this->assertNotEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $this->mockTargetObject(), array('VIEW')));

        $voter = $this->mockVoter(false);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, $this->mockTargetObject(), array('VIEW')));
    }
}
