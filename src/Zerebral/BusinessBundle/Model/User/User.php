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
     * Password confirmation
     * @var string
     */
    private $passwordConfirmation;

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
        $roles = array('ROLE_USER');
        if ($this->getRole() == self::ROLE_TEACHER) {
            $roles[] = 'ROLE_TEACHER';
        }
        if ($this->getRole() == self::ROLE_STUDENT) {
            $roles[] = 'ROLE_STUDENT';
        }
        return $roles;
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
     * @param string $passwordConfirmation
     */
    public function setPasswordConfirmation($passwordConfirmation)
    {
        $this->passwordConfirmation = $passwordConfirmation;
    }

    /**
     * @return string
     */
    public function getPasswordConfirmation()
    {
        return $this->passwordConfirmation;
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

    public function validate($columns = null)
    {
        parent::validate($columns);

        $this->validatePassword();
        $this->validatePasswordConfirmation();


        return count($this->validationFailures) == 0;
    }

    private function validatePassword()
    {
        if (trim(strlen($this->getPlainPassword())) == 0) {
            $this->validationFailures[UserPeer::PASSWORD] = new \ValidationFailed(UserPeer::PASSWORD, "Please, fill password");
        }
    }

    private function validatePasswordConfirmation()
    {
        if ($this->getPasswordConfirmation() != $this->getPlainPassword()) {
            $this->validationFailures['users.password_confirmation'] = new \ValidationFailed('users.password_confirmation', "Looks like you mistype. The two passwords must match.");
        }
    }

    /**
     * @return Teacher
     */
    public function getTeacher()
    {
        return $this->getTeachers()->getFirst();
    }

    /**
     * @return Student
     */
    public function getStudent()
    {
        return $this->getStudents()->getFirst();
    }

    /**
     * Transit user to role-specific model like Teacher or Student
     *
     * @return null|Student|Teacher
     */
    public function transitToRoleModel()
    {
        $model = null;
        switch ($this->getRole()) {
            case self::ROLE_TEACHER:
                $model = new Teacher();
                break;
            case self::ROLE_STUDENT:
                $model = new Student();
                break;
        }

        if (!is_null($model)) {
            $model->setUser($this);
        }

        return $model;
    }

    public function getRoleModel()
    {
        if ($this->isNew()) {
            return $this->transitToRoleModel();
        }

        switch ($this->getRole()) {
            case self::ROLE_TEACHER:
                return $this->getTeacher();
                break;
            case self::ROLE_STUDENT:
                return $this->getStudent();
                break;
        }

        return null;
    }

    /**
     * Full name of user with salutation prefix
     * @return string
     */
    public function getFullName()
    {
        return $this->getSalutation() . ' ' . $this->getLastName();
    }
} 