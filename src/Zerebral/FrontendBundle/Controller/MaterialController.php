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
     * @Route("/materials/upload/{id}", name="ajax_course_material_upload")
     * @ParamConverter("course")
     *
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     *
     * TODO: rename to uploadAction
     * TODO: make sure user has access to course
     */
    public function uploadMaterialAction(Model\Course\Course $course)
    {
        $courseMaterialsType = new FormType\CourseMaterialsType();
        $courseMaterialsType->setFileStorage($this->container->get('zerebral.file_storage')->getFileStorage('local'));
        $courseMaterialsType->setCourse($course);

        $form = $this->createForm($courseMaterialsType);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $materials = $form->getData();

            foreach ($materials['courseMaterials'] as $material) {
                $material->setCreatedBy($this->getRoleUser()->getId());
                $material->setCourseFolder($materials['courseMaterialFolder']);
                $material->save();
            }

            return new JsonResponse(array(
                'redirect' => $this->getRequest()->headers->get('referer')
            ));
        }

        return new FormJsonResponse($form);
    }

    /**
     * @Route("/materials/delete/{id}", name="delete_course_material")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     *
     * TODO: make sure user can delete material!!!
     */
    public function deleteAction(Model\Material\CourseMaterial $material)
    {
        $file = $material->getFile();
        $material->delete();
        $file->delete();
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
}
