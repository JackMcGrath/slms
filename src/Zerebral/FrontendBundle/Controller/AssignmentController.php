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
     * @Route("/", name="assignments")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'assignments' => Model\Assignment\AssignmentQuery::create()->find(),
            'target' => 'assignments'
        );
    }

    /**
     * @Route("/view/{id}", name="assignment_view")
     * @ParamConverter("assignment")
     * @Template()
     */
    public function viewAction(Model\Assignment\Assignment $assignment = null)
    {
        return array(
            'assignment' => $assignment,
            'target' => 'assignments'
        );
    }

   /**
     * @Route("/add", name="assignment_add")
     * @Route("/edit/{id}", name="assignment_edit")
     * @ParamConverter("assignment")
     * @Template()
     */
    public function addAction(Model\Assignment\Assignment $assignment = null)
    {
        $form = $this->createForm(new FormType\AssignmentType(), $assignment);
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /**
                 * @var \Zerebral\BusinessBundle\Model\Assignment\Assignment $assignment
                 */
                $assignment = $form->getData();
                $assignment->setTeacherId($this->getUser()->getTeacher()->getId());
                $assignment->save();

                return $this->redirect($this->generateUrl('course_view', array('id' => $assignment->getCourseId())));
            }
        }

        return array(
            'form' => $form->createView(),
            'target' => 'assignments'
        );
    }

    /**
     * @Route("/delete/{id}", name="assignment_delete")
     * @ParamConverter("assignment")
     * @Template()
     */
    public function deleteAction(Model\Assignment\Assignment $assignment = null)
    {
        $assignment->delete();
        return $this->redirect($this->generateUrl('assignments'));
    }
}
