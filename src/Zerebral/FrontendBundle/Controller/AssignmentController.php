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

/**
 * @Route("/assignments")
 */
class AssignmentController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="assignments")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'assignments' => $this->getRoleUser()->getAssignments(),
            'target' => 'assignments'
        );
    }

    /**
     * @Route("/view/{id}", name="assignment_view")
     * @ParamConverter("assignment")
     *
     * @SecureParam(name="assignment", permissions="VIEW")
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
     * @Route("/add/{courseId}", name="assignment_add")
     * @Route("/edit/{courseId}/{id}", name="assignment_edit")
     * @ParamConverter("assignment", options={"mapping": {"id": "id"}})
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     *
     * @SecureParam(name="assignment", permissions="EDIT")
     * @SecureParam(name="course", permissions="ADD_ASSIGNMENT
")
     * @Template()
     */
    public function addAction(Model\Course\Course $course, Model\Assignment\Assignment $assignment = null)
    {
        $assignmentType = new FormType\AssignmentType();
        $assignmentType->setTeacher($this->getRoleUser());
        $form = $this->createForm($assignmentType, $assignment);


        $assignedStudents = $assignment ? $assignment->getStudents()->getPrimaryKeys() : array();

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /**
                 * @var \Zerebral\BusinessBundle\Model\Assignment\Assignment $assignment
                 */
                $assignment = $form->getData();
                $assignment->setCourse($course);
                $assignment->setTeacherId($this->getRoleUser()->getId());

                $studentAssignments = new \PropelCollection();
                foreach($this->getRequest()->get('students', array()) as $studentId){
                    $studentAssignment = new \Zerebral\BusinessBundle\Model\Assignment\StudentAssignment();
                    $studentAssignment->setStudentId($studentId);
                    $studentAssignment->setAssignment($assignment);
                    $studentAssignments[] = $studentAssignment;
                }
                $assignment->setStudentAssignments($studentAssignments);

                $assignment->save();

                return $this->redirect($this->generateUrl('assignment_view', array('id' => $assignment->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
            'course' => $course,
            'students' => $course->getStudents(),
            'assignedStudents' => $assignedStudents,
            'target' => 'assignments'
        );
    }

    /**
     * @Route("/delete/{id}", name="assignment_delete")
     * @ParamConverter("assignment")
     *
     * @SecureParam(name="assignment", permissions="DELETE")
     * @Template()
     */
    public function deleteAction(Model\Assignment\Assignment $assignment = null)
    {
        $assignment->delete();
        return $this->redirect($this->generateUrl('assignments'));
    }
}
