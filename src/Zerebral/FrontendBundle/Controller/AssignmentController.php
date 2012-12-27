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
        $provider = new CourseAssignmentEventsProvider($this->getRoleUser()->getAssignments());
        $currentMonth = new Calendar(time(), $provider);
        $nextMonth = new Calendar(strtotime("+1 month"), $provider);

        return array(
            'currentMonth' => $currentMonth,
            'nextMonth' => $nextMonth,
            'assignments' => $this->getRoleUser()->getAssignments(),
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
            $assignmentSolutionType = new FormType\AssignmentSolutionType();
            $assignmentSolutionType->setFileStorage($this->container->get('zerebral.file_storage')->getFileStorage('local'));

            $criteria = new \Criteria();
            $criteria->add('assignment_id', $assignment->getId());
            $studentAssignment = $user->getStudentAssignments($criteria)->getFirst();

            $form = $this->createForm($assignmentSolutionType, $studentAssignment);

            $optionalReturn['solutionForm'] = $form->createView();
            $optionalReturn['studentAssignment'] = $studentAssignment;
        }


        $return = array(
            'course' => $assignment->getCourse(),
            'assignment' => $assignment,
            'user' => $this->getRoleUser(),
            'target' => 'assignments'
        );

        return array_merge($return, $optionalReturn);
    }

    /**
     * @Route("/upload-solutions/{id}", name="ajax_student_assignment_solutions_upload")
     * @ParamConverter("studentAssignment")
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT')")
     * @Template()
     */
    public function uploadSolutionAction(Model\Assignment\StudentAssignment $studentAssignment) {

        $assignmentSolutionType = new FormType\AssignmentSolutionType();
        $assignmentSolutionType->setFileStorage($this->container->get('zerebral.file_storage')->getFileStorage('local'));

        $form = $this->createForm($assignmentSolutionType, $studentAssignment);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $studentAssignment->save();
            return new JsonResponse(array(
                'redirect' => $this->generateUrl(
                    'assignment_view',
                    array(
                        'id' => $studentAssignment->getAssignment()->getId()
                    )
                )
            ));
        }

        return new FormJsonResponse($form);
    }

    /**
     * @Route("/remove-solutions/{id}/{fileId}", name="ajax_student_assignment_solutions_remove")
     * @ParamConverter("studentAssignment", options={"mapping": {"id": "id"}})
     * @ParamConverter("file", options={"mapping": {"fileId": "id"}})
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT')")
     * @Template()
     */
    public function removeSolutionAction(Model\Assignment\StudentAssignment $studentAssignment, \Zerebral\BusinessBundle\Model\File\File $file) {
        $fileStudentAssignment = $file->getstudentAssignmentReferenceIds();
        if ((count($fileStudentAssignment) === 1) && ($fileStudentAssignment[0]->getId() === $studentAssignment->getId())) {
            $file->delete();
            return new JsonResponse(array('success' => true));
        }

        throw new \Symfony\Component\HttpKernel\Exception\HttpException(404, 'File doesn\'t belong to student assignment');
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
        $assignmentType->setFileStorage($this->container->get('zerebral.file_storage')->getFileStorage('local'));
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

                // @todo redo with collection type
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
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function deleteAction(Model\Assignment\Assignment $assignment = null)
    {
        $assignment->delete();
        $this->setFlash('delete_assignment_success', 'Assignment <b>' . $assignment->getName() . '</b> has been successfully deleted from course ' . $assignment->getCourse()->getName() . '.');
        return $this->redirect($this->generateUrl('assignments'));
    }
}
