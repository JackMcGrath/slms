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

/**
 * @Route("/course/materials/folder")
 */
class CourseMaterialFolderController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/delete/{id}", name="course_material_folder_delete")
     * @ParamConverter("folder")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     */
    public function deleteAction(Model\Material\CourseFolder $folder)
    {
        $folder->delete();
        // TODO: do not redirect to does not exist folder!!
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Route("/add/{courseId}", name="ajax_course_material_folder_form_add")
     * @Route("/edit/{courseId}/{id}", name="ajax_course_material_folder_edit")
     *
     * @ParamConverter("folder", options={"mapping": {"id": "id"}})
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     *
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     */
    public function editAction(Model\Material\CourseFolder $folder = null, Model\Course\Course $course = null)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        if (empty($folder)) {
            $folder = new Model\Material\CourseFolder();
            $folder->setCourse($course);
        }

        $form = $this->createForm(new FormType\CourseFolderType(), $folder);

        $form->bind($this->getRequest());
        if ($form->isValid()) {
            $folder->save();

            return new JsonResponse(array(
                'redirect' => $this->getRequest()->headers->get('referer')
            ));
        }

        return new FormJsonResponse($form);
    }

    /**
     * @Route("/form/{courseId}", name="course_material_folder_form")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template
     * @return array
     */
    public function formAction(Model\Course\Course $course)
    {
        $form = $this->createForm( new FormType\CourseFolderType());

        return array(
            'form' => $form->createView(),
            'course' => $course,
        );
    }
}
