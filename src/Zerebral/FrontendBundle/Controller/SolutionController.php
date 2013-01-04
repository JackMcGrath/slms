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

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;

use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery;

class SolutionController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/files", name="assignment_solutions")
     * @Route("/files/course/{courseId}", name="course_assignment_solutions")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function indexAction(Model\Course\Course $course = null)
    {
        $assignments = AssignmentQuery::create()->getCourseAssignmentsDueDate($course, null, $this->getRoleUser())->find();
        $courses = $this->getRoleUser()->getCourses();

        return array(
            'assignments' => $assignments,
            'courses' => $courses,
            'course' => $course,
            'fileGrouping' => 'date',
            'target' => 'files'
        );
    }

    /**
     * @Route("/files/users/{assignmentId}", name="assignment_solutions_students")
     * @ParamConverter("assignment", options={"mapping": {"assignmentId": "id"}})
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function studentsAction(Model\Assignment\Assignment $assignment)
    {
        $solutions = StudentAssignmentQuery::create()->findStudentsByAssignmentId($assignment->getId())->find();

        return array(
            'assignment' => $assignment,
            'solutions' => $solutions,
            'fileGrouping' => 'date',
            'course' => $assignment->getCourse(),
            'target' => 'files'
        );
    }
}
