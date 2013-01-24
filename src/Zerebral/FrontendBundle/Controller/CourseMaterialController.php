<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Zerebral\CommonBundle\HttpFoundation\FormJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;

/**
 * @Route("/course/materials")
 */
class CourseMaterialController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/save/{courseId}", name="ajax_course_material_save")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     *
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @SecureParam(name="course", permissions="EDIT")
     * @Template()
     *
     * TODO: make sure user has access to course
     */
    public function saveAction(Model\Course\Course $course)
    {
        $courseMaterialsType = new FormType\CourseMaterialsType();
        $courseMaterialsType->setCourse($course);

        $form = $this->createForm($courseMaterialsType);
        $form->bind($this->getRequest());

        if ($form->isValid()) {

            $materials = $form->getData();

            if (!empty($materials['folder']) && $materials['folder']->isNew()) {
                $materials['folder']->save();
            }

            foreach ($materials['materials'] as $material) {
                $material->setCreatedBy($this->getRoleUser()->getId());
                $material->setCourseFolder($materials['folder']);
                $material->save();
            }

            return new JsonResponse(array(
                'redirect' => $this->getRequest()->headers->get('referer')
            ));
        }

        return new FormJsonResponse($form);
    }

    /**
     * @Route("/materials/delete/{id}", name="course_material_delete")
     * @ParamConverter("material")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @SecureParam(name="material", permissions="DELETE")
     *
     */
    public function deleteAction(Model\Material\CourseMaterial $material)
    {
        $file = $material->getFile();
        $material->delete();
        $file->delete();
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Route("/form/{courseId}", name="course_material_form")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template
     */
    public function formAction(Model\Course\Course $course)
    {
        $courseMaterialType = new FormType\CourseMaterialsType();
        $courseMaterialType->setCourse($course);
        $form = $this->createForm($courseMaterialType);

        return array(
            'form' => $form->createView(),
            'course' => $course,
        );
    }
}
