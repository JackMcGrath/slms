<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;

/**
 * @Route("/assignments")
 */
class AssignmentController extends \Zerebral\CommonBundle\Component\Controller
{

   /**
     * @Route("/add", name="assignment_add")
     * @Template()
     */
    public function addAction(Model\Assignment\Assignment $assignment = null)
    {
        $form = $this->createForm(new FormType\AssignmentType(), $assignment);
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $courseId = $this->getUser()->getTeacher()->getCourses()->getFirst()->getId();
                /**
                 * @var \Zerebral\BusinessBundle\Model\Assignment\Assignment $assignment
                 */
                $assignment = $form->getData();
                $assignment->setTeacherId($this->getUser()->getTeacher()->getId());
                $assignment->setCourseId($courseId);
                $assignment->save();

                return $this->redirect($this->generateUrl('course_view', array('id' => $courseId)));
            }
        }

        return array(
            'form' => $form->createView(),
            'target' => 'courses'
        );
    }
}
