<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;

class UserController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/my-profile", name="myprofile")
     * @PreAuthorize("hasRole('ROLE_STUDENT')")
     * @Template()
     */
    public function myProfileAction()
    {
        $form = $this->createForm(new FormType\StudentProfileType(), $this->getRoleUser());
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                /** @var $course Model\User\User */
                $user = $form->getData();
                $user->save();

                $this->getRequest()->getSession()->setFlash('profile_save_success', 'Profile has been saved!');
                return $this->redirect($this->generateUrl('myprofile'));
            }
        }

        return array(
            'form' => $form->createView(),
            'target' => 'my-profile'
        );
    }

}
