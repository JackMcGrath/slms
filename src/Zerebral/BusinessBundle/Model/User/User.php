<?php

namespace Zerebral\BusinessBundle\Model\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

use Zerebral\BusinessBundle\Model\User\om\BaseUser;

class User extends BaseUser implements UserInterface, \Serializable, EquatableInterface
{
    const ROLE_STUDENT = 'student';
    const ROLE_TEACHER = 'teacher';

    /**
     * Plain password
     * @var string
     */
    private $plainPassword;

    /**
     * Password encoder
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct()
    {
        $this->setIsActive(true);
        $this->setSalt(md5(uniqid(null, true)));
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id,
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }

    /**
     * {@inheritDoc}
     */
    public function isEqualTo(UserInterface $user)
    {
        return $this->getUsername() == $user->getUsername();
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $passwordEncoder
     */
    public function setPasswordEncoder(PasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @return \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    public function getPasswordEncoder()
    {
        return $this->passwordEncoder;
    }

    /**
     * {@inheritDoc}
     */
    public function preSave(\PropelPDO $con = null)
    {
        //@todo fix it
        $this->setUpdatedAt(date("Y-m-d H:i:s", time()));
        $this->encodePassword();
        return parent::preSave();
    }

    public function preInsert(\PropelPDO $con = null)
    {
        //@todo fix it
        $this->setCreatedAt(date("Y-m-d H:i:s", time()));
        return parent::preInsert($con);
    }




    /**
     * Encode plain-text password using encoder
     */
    public function encodePassword()
    {

        if ($this->getPasswordEncoder() && $this->getPlainPassword()) {
            $password = $this->getPasswordEncoder()->encodePassword($this->getPlainPassword(), $this->getSalt());
            $this->setPassword($password);
        }
    }
} 