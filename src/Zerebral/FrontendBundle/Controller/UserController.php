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
        $studentProfileType = new FormType\StudentProfileType();
        $studentProfileType->setFileStorage($this->container->get('zerebral.file_storage')->getFileStorage('local'));



        $user = $this->getRoleUser();
        $form = $this->createForm($studentProfileType, $user);

        $avatar = $user->getAvatar();
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $formUser = $form->getData();
                $formUser->getUser()->save();
                $formUser->save();

                $this->getRequest()->getSession()->setFlash('profile_save_success', 'Profile has been saved!');
                return $this->redirect($this->generateUrl('myprofile'));
            } else {
                // Restoring previous avatar because we can't draw unvalid avatar
                $user->setAvatar($avatar);
            }
        }

        return array(
            'form' => $form->createView(),
            'user' => $user,
            'target' => 'my-profile'
        );
    }

}
