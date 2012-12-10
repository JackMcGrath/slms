<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;

class UserController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/my-profile", name="myprofile")
     * @Template()
     */
    public function myProfileAction()
    {
        $form = $this->createForm(new FormType\ProfileType(), $this->getUser());
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /** @var $course Model\User\User */
                $user = $form->getData();
                $user->save();

                return $this->redirect($this->generateUrl('my-profile'));
            }
        }

        return array(
            'user' => $form->createView(),
            'target' => 'my-profile'
        );
    }

}
