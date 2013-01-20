<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use Symfony\Component\HttpFoundation\JsonResponse;
use Zerebral\CommonBundle\HttpFoundation\FormJsonResponse;

use Zerebral\BusinessBundle\Calendar\EventProviders\CourseAssignmentEventsProvider;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;

use \Criteria;

use Zerebral\CommonBundle\Component\Calendar\Calendar;
use \Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery;

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
        $assignments = $this->getRoleUser()->getAssignmentsDueDate(false);
        $assignmentsNoDueDate = $this->getRoleUser()->getAssignmentsDueDate(true);
        $draftAssignment = $this->getUser()->isTeacher() ? $this->getRoleUser()->getAssignmentsDraft() : null;

        $provider = new CourseAssignmentEventsProvider($assignments);
        $currentMonth = new Calendar(time(), $provider);
        $nextMonth = new Calendar(strtotime("+1 month"), $provider);

        return array(
            'currentMonth' => $currentMonth,
            'nextMonth' => $nextMonth,
            'assignments' => $assignments,
            'assignmentsNoDueDate' => $assignmentsNoDueDate,
            'draftAssignment' => $draftAssignment,
            'target' => 'assignments'
        );
    }

    /**
     * @Route("/view/{id}", name="assignment_view")
     * @ParamConverter("assignment")
     *
     * @SecureParam(name="assignment", permissions="VIEW")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function viewAction(Model\Assignment\Assignment $assignment)
    {
        $user = $this->getRoleUser();
        $optionalReturn = array();

        if ($user->isStudent()) {
            $criteria = new \Criteria();
            $criteria->add('assignment_id', $assignment->getId());
            $studentAssignment = $user->getStudentAssignments($criteria)->getFirst();
            $optionalReturn['studentAssignment'] = $studentAssignment;
        }

        // TODO: move to partial similar to CourseMaterialFolder::form
        $feedCommentFormType = new FormType\FeedCommentType();
        $feedCommentForm = $this->createForm($feedCommentFormType, null);

        $return = array(
            'course' => $assignment->getCourse(),
            'feedCommentForm' => $feedCommentForm->createView(),
            'assignment' => $assignment,
            'user' => $this->getRoleUser(),
            'target' => 'assignments'
        );

        return array_merge($return, $optionalReturn);
    }

    /**
     * @Route("/submit-solutions/{id}", name="student_assignment_solutions_submit")
     * @ParamConverter("studentAssignment")
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT')")
     * @SecureParam(name="studentAssignment", permissions="SUBMIT")
     * @Template()
     */
    public function submitSolutionsAction(Model\Assignment\StudentAssignment $studentAssignment)
    {
        $studentAssignment->setIsSubmitted(1);
        $studentAssignment->save();
        $this->setFlash(
            'student_assignment_solutions_submit',
                'Solutions for assignment <b>' . $studentAssignment->getAssignment()->getName() . '</b> were successfully submitted'
        );
        return $this->redirect($this->generateUrl('assignment_view', array('id' => $studentAssignment->getAssignmentId())));
    }

    /**
     * @Route("/add/{courseId}", name="assignment_add")
     * @Route("/edit/{courseId}/{id}", name="assignment_edit")
     * @ParamConverter("assignment", options={"mapping": {"id": "id"}})
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     *
     * @SecureParam(name="assignment", permissions="EDIT")
     * @SecureParam(name="course", permissions="ADD_ASSIGNMENT")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function addAction(Model\Course\Course $course, Model\Assignment\Assignment $assignment = null)
    {
        if (empty($assignment)) {
            $assignment = new Model\Assignment\Assignment();
        }

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

                // TODO: redo with choice type
                $studentAssignments = new \PropelCollection();
                foreach ($this->getRequest()->get('students', array()) as $studentId) {
                    $studentAssignmentModel = StudentAssignmentQuery::create()->filterByAssignmentId($assignment->getId())->filterByStudentId($studentId)->findOne();
                    $studentAssignment = $studentAssignmentModel ?: new \Zerebral\BusinessBundle\Model\Assignment\StudentAssignment();
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
     * @Route("/delete/{id}/{courseId}", name="course_assignment_delete")
     * @ParamConverter("assignment", options={"mapping": {"id": "id"}})
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     *
     * @SecureParam(name="assignment", permissions="DELETE")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function deleteAction(Model\Assignment\Assignment $assignment, Model\Course\Course $course = null)
    {
        $assignment->delete();
        $this->setFlash(
            'delete_assignment_success',
                'Assignment <b>' . $assignment->getName() . '</b> has been successfully deleted from course ' . $assignment->getCourse()->getName() . '.'
        );
        if ($course) {
            return $this->redirect($this->generateUrl('course_assignments', array('id' => $course->getId())));
        } else {
            return $this->redirect($this->generateUrl('assignments'));
        }

    }
}
