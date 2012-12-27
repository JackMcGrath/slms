<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Zerebral\CommonBundle\HttpFoundation\FormJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Zerebral\BusinessBundle\Calendar\EventProviders\CourseAssignmentEventsProvider;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;


class MaterialController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/folders/delete/{id}", name="delete_folder")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     */
    public function deleteFolderAction(Model\Material\CourseFolder $folder)
    {
        $folder->delete();
        #TODO do not redirect to does not exist folder!!
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Route("/folders/edit", name="ajax_course_join")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     */
    public function editAction()
    {
        $form = $this->createForm(new FormType\FolderType());

        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $form->bind($this->getRequest());
        if ($form->isValid()) {
            $folder = \Zerebral\BusinessBundle\Model\Material\CourseFolderQuery::create()->findPk($form['id']->getData());
            if ($folder) {
                $folder->setName($form['name']->getData());
                $folder->setCourseId($form['course_id']->getData());
                $folder->save();
            }

            return new JsonResponse(array(
                'redirect' => $this->getRequest()->headers->get('referer')
            ));
        }

        return new FormJsonResponse($form);
    }
}
