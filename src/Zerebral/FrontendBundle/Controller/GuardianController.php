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
        /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
        $guardian = $this->getRoleUser();
        $selectedChild = $guardian->getSelectedChildWithSummary($this->get('session')->get('selectedChildId'));
        $upcomingAssignments = $selectedChild->getUpcomingAssignments();
        return array(
            'target' => 'home',
            'summary' => $selectedChild->getVirtualColumn('summary'),
            'upcomingAssignments' => $upcomingAssignments
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
            return $this->redirect($this->getRequest()->headers->get('referer'));
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
            $course = $attendance->getAttendance()->getCourse();


            $courses[$course->getId()] = $course;

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
     * @Route("/courses", name="guardian_courses")
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @Template
     */
    public function coursesAction()
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
     * @Route("/assignments/{courseId}", name="guardian_course_assignments")
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @param \Zerebral\BusinessBundle\Model\Course\Course $course
     * @return array
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     * @Template
     */
    public function courseAssignmentsAction(Model\Course\Course $course)
    {
        /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
        $guardian = $this->getRoleUser();
        $selectedChild = $guardian->getSelectedChild($this->get('session')->get('selectedChildId'));

        if (!$selectedChild->hasCourse($course)) {
            //throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Course #' . $course->getId() . ' not found');
            return $this->redirect($this->generateUrl('guardian_courses'));
        }

        /** @var \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery $assignmentsQuery  */
        $assignmentsQuery = \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery::create()->filterByUserAndDueDate($selectedChild->getUser(), $course);
        $assignmentsCollection = $assignmentsQuery->clearOrderByColumns()->addDescendingOrderByColumn(\Zerebral\BusinessBundle\Model\Assignment\AssignmentPeer::DUE_AT)->find();

        $assignmentsUpcoming = array();
        $assignmentsOther = array();
        $currentDate = new \DateTime();

        foreach ($assignmentsCollection as $assignment) {

            if ((!is_null($assignment->getDueAt())) && ($currentDate <= $assignment->getDueAt())) {
                $assignmentsUpcoming[] = $assignment;
            } else {
                $assignmentsOther[] = $assignment;
            }
        }

        $assignmentsUpcoming = array_reverse($assignmentsUpcoming);
        $assignments = array_merge($assignmentsUpcoming, $assignmentsOther);


        return array(
            'target' => 'home',
            'guardian' => $guardian,
            'selectedChild' => $selectedChild,
            'course' => $course,
            'assignments' => $assignments
        );
    }

    /**
     * @Route("/course/{id}", name="guardian_course_view")
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @ParamConverter("course", options={"mapping": {"id": "id"}})
     * @Template
     */
    public function courseAction(Model\Course\Course $course)
    {
        /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
        $guardian = $this->getRoleUser();
        $selectedChild = $guardian->getSelectedChild($this->get('session')->get('selectedChildId'));

        if (!$selectedChild->hasCourse($course)) {
            return $this->redirect($this->generateUrl('guardian_courses'));
        }

        $upcomingAssignments = AssignmentQuery::create()->getUpcomingByUserAndCourse($selectedChild->getUser(), $course)->find();
        $recentMaterials = \Zerebral\BusinessBundle\Model\Material\CourseMaterialQuery::create()->findRecentCourseMaterials($course)->find();

        return array(
            'course' => \Zerebral\BusinessBundle\Model\Course\CourseQuery::create()->filterByIdForUser($course->getId(), $selectedChild)->findOne(),
            'upcomingAssignments' => $upcomingAssignments,
            'recentMaterials' => $recentMaterials,
            'target' => 'courses',
        );
    }

    /**
     * @Route("/assignment/{id}", name="guardian_assignment_view")
     * @ParamConverter("assignment")
     *
     * @SecureParam(name="assignment", permissions="VIEW")
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @Template()
     */
    public function assignmentAction(Model\Assignment\Assignment $assignment)
    {
        /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
        $guardian = $this->getRoleUser();
        $selectedChild = $guardian->getSelectedChild($this->get('session')->get('selectedChildId'));

        return array(
            'course' => $assignment->getCourse(),
            'assignment' => $assignment,
            'student' => $selectedChild,
            'user' => $this->getRoleUser(),
            'target' => 'assignments'
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
        $dateRange = array(
            'start' => $this->getRequest()->get('startDate', date('Y-m-d', strtotime('Monday this week'))),
            'end' => $this->getRequest()->get('endDate', date('Y-m-d', strtotime('Sunday this week')))
        );

        $coursesGrading = \Zerebral\BusinessBundle\Model\Course\CourseQuery::create()->gradingByStudent($selectedChild, $dateRange)->find();
        $courseAssignmentsSize = array();
        foreach ($coursesGrading as $course) {
            $courseAssignmentsSize[$course->getId()] = $course->getAssignments()->count();
        }

        return array(
            'isMonthRange' => true,
            'startDate' => $dateRange['start'],
            'endDate' => $dateRange['end'],
            'isMonthRange' => (strtotime($dateRange['end']) - strtotime($dateRange['start']) > 3600*24*26),
            'target' => 'grading',
            'courseAssignmentsSize' => $courseAssignmentsSize,
            'coursesGrading' => $coursesGrading,
            'guardian' => $guardian,
            'selectedChild' => $selectedChild
        );
    }

    /**
     * @Route("/members", name="guardian_members_view")
     * @Route("/members/course/{courseId}", name="guardian_course_members_view")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     * @PreAuthorize("hasRole('ROLE_GUARDIAN')")
     * @Template
     */
    public function membersAction(Model\Course\Course $course = null)
    {
        $relatedUsersByRole = array();
        $courses = array();
        /** @var \Zerebral\BusinessBundle\Model\User\Guardian $guardian  */
        $guardian = $this->getRoleUser();
        $selectedChild = $guardian->getSelectedChild($this->get('session')->get('selectedChildId'));

        $relatedUsersQuery = \Zerebral\BusinessBundle\Model\User\UserQuery::create()->getRelatedUsers($guardian->getUser(), true);
        if ($course) {
            $relatedUsersQuery->where('userToCourses.course_id = ' . $course->getId());
        }
        $relatedUsers = $relatedUsersQuery->addAscendingOrderByColumn('LOWER(users.last_name)')->find();

        /** @var $user \Zerebral\BusinessBundle\Model\User\User */
        foreach ($relatedUsers as $user) {
            $relatedUsersByRole[$user->getRole()][] = $user;
        }

        $courses = \Zerebral\BusinessBundle\Model\Course\CourseQuery::create()->filterByRoleUser($selectedChild)->addAscendingOrderByColumn('LOWER(courses.name)')->find();

        return array(
            'relatedUsers' => $relatedUsersByRole,
            'courses' => $courses,
            'course' => $course,
            'target' => 'members',
        );
    }
}
