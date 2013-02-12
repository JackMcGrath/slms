<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use Zerebral\BusinessBundle\Model\User\User;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/my-profile", name="myprofile")
     * @PreAuthorize("hasRole('ROLE_USER')")
     * @Template()
     */
    public function profileAction()
    {
        $profileType = null;

        $userRole = $this->getUser()->getRole();
        switch ($userRole) {
            case (User::ROLE_TEACHER):
                $profileType = new FormType\TeacherProfileType();
                break;
            case (User::ROLE_STUDENT):
                $profileType = new FormType\StudentProfileType();
                break;
            case (User::ROLE_GUARDIAN):
                $profileType = new FormType\GuardianProfileType();
                break;
        }


        $user = $this->getRoleUser();
        $form = $this->createForm($profileType, $user);

        $avatar = $user->getAvatar();
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $formUser = $form->getData();
                $formUser->getUser()->save();
                $formUser->save();

                $this->setFlash('profile_save_success', 'Profile has been saved!');
                return $this->redirect($this->generateUrl('myprofile'));
            } else {
                // Restoring previous avatar because we can't draw unvalid avatar
                $user->setAvatar($avatar);
            }
        }

        return array(
            'form' => $form->createView(),
            'parentsInviteForm' => $this->createForm(new FormType\CourseInviteType())->createView(),
            'user' => $user,
            'target' => 'my-profile'
        );
    }

    /**
     * @Route("/user/suggest", name="ajax_user_suggest")
     *
     * @PreAuthorize("hasRole('ROLE_USER')")
     * @Template()
     */
    public function suggestUserAction()
    {
        $name = $this->getRequest()->get('username');
        if ($this->getUser()->isGuardian()) {
            /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
            $guardian = $this->getRoleUser();
            $forUser = $guardian->getSelectedChild($this->get('session')->get('selectedChildId'))->getUser();
        } else {
            $forUser = $this->getUser();
        }
        if ($name) {
            $userList = \Zerebral\BusinessBundle\Model\User\UserQuery::create()->findForSuggestByNameForUser($name, $forUser);
            $response = array();
            if (count($userList)) {
                foreach ($userList as $user) {
                    $response[] = array(
                        'id' => $user->getId(),
                        'name' => $user->getFullName(),
                    );
                }
            }
            return new JsonResponse(array('success' => true, 'users' => $response));
        }

        throw new \Symfony\Component\HttpKernel\Exception\HttpException(404, 'User name is empty.');
    }

}
