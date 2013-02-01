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

use Zerebral\BusinessBundle\Calendar\EventProviders\AssignmentEventsProvider;
use Zerebral\BusinessBundle\Calendar\EventProviders\CourseAssignmentEventsProvider;

use Zerebral\CommonBundle\Component\Calendar\Calendar;
use Zerebral\BusinessBundle\Model\User\StudentQuery;
use Zerebral\BusinessBundle\Model\Feed\FeedItemQuery;
use Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery;

/**
 * @Route("/courses")
 */
class CourseController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="courses")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        $dateFilter = $session->get('assigmentDateFilter', array());

        $provider = new CourseAssignmentEventsProvider($assignments = AssignmentQuery::create()->filterByUserAndDueDate($this->getUser(), null, false)->find());
        $currentMonth = new Calendar(time(), $provider);

//        $today = time();
//        $nextMonth = strtotime("+2 month");
//        $diff = $nextMonth - $today;
//        var_dump($diff / 60 / 60 / 24);
//        die();
//        var_dump(date('Y-m-d', strtotime("+1 month")));
//        die();
        $nextMonth = new Calendar(strtotime("+1 month"), $provider);

        $upcomingAssignments = $this->getRoleUser()->getUpcomingAssignments();
        $courses = \Zerebral\BusinessBundle\Model\Course\CourseQuery::create()->filterByRoleUser($this->getRoleUser())->find();

        return array(
            'currentMonth' => $currentMonth,
            'nextMonth' => $nextMonth,
            'upcomingAssignments' => $upcomingAssignments,
            'courses' => $courses,
            'target' => 'courses',
            'courseJoinForm' => $this->createForm(new FormType\CourseJoinType())->createView(),
            'dateFilter' => array('startDate' => $dateFilter ? $dateFilter['startDate'] : null, 'endDate' => $dateFilter ? $dateFilter['endDate'] : null),
        );
    }

    /**
     * @Route("/view/{id}", name="course_view")
     * @ParamConverter("course")
     * @SecureParam(name="course", permissions="VIEW")
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function viewAction(Model\Course\Course $course)
    {
        $upcomingAssignments = $this->getRoleUser()->getUpcomingAssignments($course);
        $recentMaterials = \Zerebral\BusinessBundle\Model\Material\CourseMaterialQuery::create()->findRecentCourseMaterials($course)->find();

        return array(
            'course' => $course,
            'upcomingAssignments' => $upcomingAssignments,
            'recentMaterials' => $recentMaterials,
            'target' => 'courses',
            'showWelcomeMessage' => $this->getRequest()->get('showWelcomeMessage', false)
        );
    }

    /**
     * @Route("/feed/{id}", name="course_feed")
     * @ParamConverter("course")
     * @SecureParam(name="course", permissions="VIEW")
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function feedAction(Model\Course\Course $course)
    {
         // TODO: move to partial similar to CourseMaterialFolder::form
        $feedItemFormType = new FormType\FeedItemType();
        $feedItemForm = $this->createForm($feedItemFormType, null);

        $upcomingAssignments = $this->getRoleUser()->getUpcomingAssignments($course);
        $recentMaterials = \Zerebral\BusinessBundle\Model\Material\CourseMaterialQuery::create()->findRecentCourseMaterials($course)->find();

        $feedItems = FeedItemQuery::create()->getCourseFeed($course, $this->getUser())->limit(10)->find();
        $feedItemsCount = FeedItemQuery::create()->getCourseFeed($course, $this->getUser())->count();

        return array(
            'course' => $course,
            'feedItems' => $feedItems,
            'feedItemsCount' => $feedItemsCount,
            'upcomingAssignments' => $upcomingAssignments,
            'recentMaterials' => $recentMaterials,
            'feedItemForm' => $feedItemForm->createView(),
            'target' => 'feed',
        );
    }

    /**
     * @Route("/add", name="course_add")
     * @Route("/edit/{id}", name="course_edit")
     * @ParamConverter("course")
     *
     * @SecureParam(name="course", permissions="EDIT")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function addAction(Model\Course\Course $course = null)
    {
        $courseType = new FormType\CourseType();
        $courseType->setTeacher($this->getRoleUser());
        $form = $this->createForm($courseType, $course);

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /** @var $course Model\Course\Course */
                $course = $form->getData();
                $course->setCreatedBy($this->getRoleUser()->getId());
                $course->addTeacher($this->getRoleUser());
                $course->save();

                return $this->redirect($this->generateUrl('course_view', array('id' => $course->getId())));
            }
        }


        return array(
            'form' => $form->createView(),
            'isFirstCourse' => $this->getRoleUser()->countCourses() == 0,
            'target' => 'courses'
        );
    }

    /**
     * @Route("/delete/{id}", name="course_delete")
     * @ParamConverter("course")
     *
     * @SecureParam(name="course", permissions="DELETE")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @Template()
     */
    public function deleteAction(Model\Course\Course $course)
    {
        $course->delete();
        $this->setFlash('delete_course_success', 'Course <b>' . $course->getName() . '</b> has been successfully deleted.');
        return $this->redirect($this->generateUrl('courses'));
    }


    /**
     * @Route("/assignments/{id}", name="course_assignments")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @SecureParam(name="course", permissions="VIEW")
     * @ParamConverter("course")
     * @Template()
     */
    public function assignmentsAction(Model\Course\Course $course)
    {
        $session = $this->getRequest()->getSession();
        $dateFilter = $session->get('assigmentDateFilter', array());

        $assignments = AssignmentQuery::create()->filterByUserAndDueDate($this->getUser(), $course, false)->find();
        $assignmentsNoDueDate = AssignmentQuery::create()->filterByUserAndDueDate($this->getUser(), $course, true)->find();
        $draftAssignment = $this->getUser()->isTeacher() ? AssignmentQuery::create()->findDraftByTeacher($this->getRoleUser(), $course) : null;

        $provider = new AssignmentEventsProvider($assignments);
        $currentMonth = new \Zerebral\CommonBundle\Component\Calendar\Calendar(time(), $provider);
        $nextMonth = new \Zerebral\CommonBundle\Component\Calendar\Calendar(strtotime("+1 month"), $provider);

        return array(
            'currentMonth' => $currentMonth,
            'nextMonth' => $nextMonth,
            'assignments' => $assignments,
            'assignmentsNoDueDate' => $assignmentsNoDueDate,
            'draftAssignment' => $draftAssignment,
            'course' => $course,
            'target' => 'courses',
            'dateFilter' => array('startDate' => $dateFilter ? $dateFilter['startDate'] : null, 'endDate' => $dateFilter ? $dateFilter['endDate'] : null),
        );
    }

    /**
     * @Route("/syllabus/{courseId}", name="course_materials")
     * @Route("/syllabus/{courseId}/{folderId}", name="course_materials_folder")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @SecureParam(name="course", permissions="VIEW")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     * @ParamConverter("folder", options={"mapping": {"folderId": "id"}})
     * @Template()
     */
    public function materialsAction(Model\Course\Course $course, Model\Material\CourseFolder $folder = null)
    {
        $session = $this->getRequest()->getSession();

        $materialGroupingType = $this->getRequest()->get('MaterialGrouping') ?: ($session->has('MaterialGrouping') ? $session->get('MaterialGrouping') : 'date');
        $session->set('MaterialGrouping', $materialGroupingType);

        $materials = \Zerebral\BusinessBundle\Model\Material\CourseMaterialPeer::getGrouped($course, $materialGroupingType, $folder);

        return array(
            'dayMaterials' => $materials,

            'materialGrouping' => $materialGroupingType,
            'folder' => $folder,
            'course' => $course,
            'target' => 'courses'
        );
    }

    /**
     * @Route("/members/{id}", name="course_members")
     * @PreAuthorize("hasRole('ROLE_STUDENT') or hasRole('ROLE_TEACHER')")
     * @ParamConverter("course")
     * @SecureParam(name="course", permissions="VIEW")
     * @Template()
     */
    public function membersAction(Model\Course\Course $course)
    {
        return array(
            'students' => $course->getStudents(),
            'teachers' => $course->getTeachers(),
            'course' => $course,
            'courseInviteForm' => $this->createForm(new FormType\CourseInviteType())->createView(),
            'target' => 'members'
        );
    }

    /**
     * @Route("/remove-student/{courseId}/{studentId}", name="course_remove_student")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @ParamConverter("course", options={"mapping": {"courseId": "course_id"}})
     * @ParamConverter("student", options={"mapping": {"studentId": "student_id"}})
     */
    public function removeStudent(Model\Course\CourseStudent $courseStudent)
    {
        $courseStudent->delete();
        $this->setFlash('delete_course_student_success', 'Student <b>' . $courseStudent->getStudent()->getFullName() . '</b> has been successfully deleted from course.');
        return $this->redirect($this->generateUrl('course_members', array('id' => $courseStudent->getCourseId())));
    }

    /**
     * @Route("/reset/{id}", name="course_reset_code")
     * @ParamConverter("course")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     */
    public function resetAccessCodeAction(Model\Course\Course $course = null)
    {
        $course->resetAccessCode();
        $course->save();

        return $this->redirect(
            $this->generateUrl(
                'course_members',
                array(
                    'id' => $course->getId()
                )
            )
        );
    }

    /**
     * @Route("/attendance/{id}", name="course_attendance")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @SecureParam(name="course", permissions="VIEW")
     * @ParamConverter("course")
     * @Template()
     */
    public function attendanceAction(Model\Course\Course $course)
    {
        // TODO: raw date should be pre-formatted in Y-m-d
        $dateRaw = $this->getRequest()->get('date', time());
        $date = date('Y-m-d', $dateRaw);
        $dateTime = new \DateTime($date);

        // TODO: create proper method in course to find attendance by date
        $c = new \Criteria();
        $c->add('date', $date);
        /** @var $attendance \Zerebral\BusinessBundle\Model\Attendance\Attendance */
        $attendance = $course->getAttendances($c)->getFirst();
        if (empty($attendance)) {
            $attendance = new \Zerebral\BusinessBundle\Model\Attendance\Attendance();
            $attendance->initStudents(StudentQuery::create()->findByCourse($course)->find());
        }

        $form = $this->createForm(new FormType\AttendanceType(), $attendance);
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                /** @var $attendance Model\Attendance\Attendance */
                $attendance = $form->getData();
                $attendance->setCourseId($course->getId());
                $attendance->setTeacherId($this->getRoleUser()->getId());
                $attendance->setDate($date);
                $attendance->save();

                $this->setFlash('attendance_save_success', 'Attendance for ' . date('m/d/Y', $dateRaw) . ' was successfully saved.');
                return $this->redirect($this->generateUrl('course_attendance', array('id' => $course->getId(), 'date' => $dateRaw)));
            }
        }

        return array(
            'course' => $course,
            'attendance' => $attendance,
//            'students' => $students,
            'form' => $form->createView(),
            'date' => $dateTime,
            'dateRaw' => $dateRaw,
            'target' => 'course'
        );
    }

    /**
     * @Route("/grading/{id}", name="course_grading")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @SecureParam(name="course", permissions="VIEW")
     * @ParamConverter("course")
     * @Template()
     */
    public function gradingAction(Model\Course\Course $course)
    {
        $assignments = \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery::create()->findSortedByCourse($course)->filterByDueAt(null, \Criteria::ISNOTNULL)->find();

        $grading = array();
        foreach ($assignments as $assignment) {
            foreach ($assignment->getStudentAssignments() as $studentAssignment) {
                $grading[$studentAssignment->getStudentId()][$studentAssignment->getAssignmentId()] = $studentAssignment;
            }
        }
        $students = StudentQuery::create()->findByCourse($course)->find();

        return array(
            'grading' => $grading,
            'students' => $students,
            'course' => $course,
            'assignments' => $assignments,
            'target' => 'course'
        );
    }
}
