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
     * @ParamConverter("folder")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     */
    public function deleteFolderAction(Model\Material\CourseFolder $folder)
    {
        $folder->delete();
        #TODO do not redirect to does not exist folder!!
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Route("/folders/add/{courseId}", name="ajax_add_folder")
     * @Route("/folders/edit/{courseId}/{id}", name="ajax_edit_folder")
     *
     * @ParamConverter("folder", options={"mapping": {"id": "id"}})
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     *
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     */
    public function editFolderAction(Model\Material\CourseFolder $folder = null, Model\Course\Course $course = null)
    {

        if (empty($folder)) {
            $folder = new Model\Material\CourseFolder();
            $folder->setCourse($course);
        }

        $form = $this->createForm(new FormType\FolderType(), $folder);

        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

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
     * @Route("/materials/upload/{id}", name="ajax_course_material_upload")
     * @ParamConverter("course")
     *
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function uploadMaterialAction(Model\Course\Course $course) {
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

    public function deleteAction()
    {

    }
}
