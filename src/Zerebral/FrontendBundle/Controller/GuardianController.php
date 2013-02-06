<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use Zerebral\BusinessBundle\Model as Model;
use Zerebral\BusinessBundle\Model\User\Student;

use Zerebral\BusinessBundle\Calendar\EventProviders\CourseAssignmentEventsProvider;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;
use Zerebral\CommonBundle\Component\Calendar\Calendar;

/**
  * @Route("/parent-area")
 */
class GuardianController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="guardian_summary")
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @Template
     */
    public function indexAction()
    {
        return array(
            'target' => 'home'
        );
    }

    /**
     * @Route("/set-child/{childId}", name="guardian_set_child")
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @param \Zerebral\BusinessBundle\Model\User\Student $student
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("student", options={"mapping": {"childId": "id"}})
     */
    public function setSelectedChild(\Zerebral\BusinessBundle\Model\User\Student $student)
    {
        $referrer = $this->getRequest()->headers->get('referer');
        if (($this->getRoleUser()->isGuardianFor($student)) && (!is_null($referrer))) {
            $this->get('session')->set('selectedChildId', $student->getId());
            return $this->redirect($this->generateUrl('guardian_summary'));
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('You can\'t access this URL directly');
        }

    }

    /**
     * @Route("/attendance/", name="guardian_attendance")
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @Template
     */
    public function attendanceAction()
    {
        /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
        $guardian = $this->getRoleUser();
        $selectedChild = $guardian->getSelectedChild($this->get('session')->get('selectedChildId'));
        $dateRange = array(
            'start' => $this->getRequest()->get('startDate', date('Y-m-d', strtotime('Monday this week'))),
            'end' => $this->getRequest()->get('endDate', date('Y-m-d', strtotime('Sunday this week')))
        );

        $studentAttendances = \Zerebral\BusinessBundle\Model\Attendance\StudentAttendanceQuery::create()->filterByDateAndStudent($dateRange, $selectedChild)->find();

        $studentAttendanceFormatted = array();
        $courses = array();

        /**
         * @var $attendance \Zerebral\BusinessBundle\Model\Attendance\StudentAttendance
         */
        foreach ($studentAttendances as $attendance) {
            $studentAttendanceFormatted[$attendance->getAttendance()->getCourseId()][$attendance->getAttendance()->getDate('Y-m-d')] = $attendance;
            $courses[$attendance->getAttendance()->getCourseId()] = $attendance->getAttendance()->getCourse();
        }

        return array(
            'attendancies' => $studentAttendanceFormatted,
            'courses' => $courses,
            'isMonthRange' => (strtotime($dateRange['end']) - strtotime($dateRange['start']) > 3600*24*26),
            'startDate' => $dateRange['start'],
            'endDate' => $dateRange['end'],
            'target' => 'attendance',

        );
    }

    /**
     * @Route("/classes", name="guardian_classes")
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @Template
     */
    public function classesAction()
    {
        /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
        $guardian = $this->getRoleUser();
        /** @var Student $selectedChild  */
        $selectedChild = $guardian->getSelectedChild($this->get('session')->get('selectedChildId'));


        $session = $this->getRequest()->getSession();
        $dateFilter = $session->get('assigmentDateFilter', array());

        $provider = new CourseAssignmentEventsProvider($assignments = AssignmentQuery::create()->filterByUserAndDueDate($selectedChild->getUser(), null, false)->find());
        $currentMonth = new Calendar(time(), $provider);

        $nextMonth = new Calendar(strtotime("+1 month"), $provider);


        $courses = \Zerebral\BusinessBundle\Model\Course\CourseQuery::create()->filterByRoleUser($selectedChild)->find();

        return array(
            'target' => 'home',
            'guardian' => $guardian,
            'selectedChild' => $selectedChild,
            'courses' => $courses,
            'currentMonth' => $currentMonth,
            'nextMonth' => $nextMonth,
            'dateFilter' => array('startDate' => $dateFilter ? $dateFilter['startDate'] : null, 'endDate' => $dateFilter ? $dateFilter['endDate'] : null)
        );
    }

    /**
     * @Route("/classes/{courseId}", name="guardian_classes_course")
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @param \Zerebral\BusinessBundle\Model\Course\Course $course
     * @return array
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     * @Template
     */
    public function courseAction(\Zerebral\BusinessBundle\Model\Course\Course $course)
    {
        /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
        $guardian = $this->getRoleUser();
        $selectedChild = $guardian->getSelectedChild($this->get('session')->get('selectedChildId'));

        if (!$selectedChild->hasCourse($course)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Course #' . $course->getId() . ' not found');
        }

        /** @var \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery $assigmentsQuery  */
        $assigmentsQuery = \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery::create()->filterByUserAndDueDate($selectedChild->getUser(), $course);


        return array(
            'target' => 'home',
            'guardian' => $guardian,
            'selectedChild' => $selectedChild,
            'course' => $course,
            'assignments' => $assigmentsQuery->clearOrderByColumns()->addDescendingOrderByColumn(\Zerebral\BusinessBundle\Model\Assignment\AssignmentPeer::DUE_AT)->find()
        );
    }

    /**
     * @Route("/grading", name="guardian_grading")
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @Template
     */
    public function gradingAction()
    {
        /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
        $guardian = $this->getRoleUser();
        $selectedChild = $guardian->getSelectedChild($this->get('session')->get('selectedChildId'));

        $coursesGrading = \Zerebral\BusinessBundle\Model\Course\CourseQuery::create()->gradingByStudent($selectedChild)->find();
        $courseAssignmentsSize = array();
        foreach ($coursesGrading as $course) {
            $courseAssignmentsSize[$course->getId()] = $course->getAssignments()->count();
        }

        return array(
            'target' => 'grading',
            'courseAssignmentsSize' => $courseAssignmentsSize,
            'coursesGrading' => $coursesGrading,
            'guardian' => $guardian,
            'selectedChild' => $selectedChild
        );
    }
}
