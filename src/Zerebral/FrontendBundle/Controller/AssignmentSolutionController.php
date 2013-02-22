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

/**
 * @Route("/files")
 */
class AssignmentSolutionController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="assignment_solutions")
     * @Route("/course/{courseId}", name="course_assignment_solutions")
     * @ParamConverter("course", options={"mapping": {"courseId": "id"}})
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @SecureParam(name="course", permissions="VIEW")
     * @Template()
     *
     */
    public function indexAction(Model\Course\Course $course = null)
    {
        // TODO: need to create common way to store page states
        $session = $this->getRequest()->getSession();
        $fileGroupingType = $this->getRequest()->get('SolutionFileGrouping') ?: ($session->has('SolutionFileGrouping') ? $session->get('SolutionFileGrouping') : 'date');
        $session->set('SolutionFileGrouping', $fileGroupingType);

        /**
         * @var $assignments AssignmentQuery
         */
        $assignments = AssignmentQuery::create()->filterByUserAndDueDate($this->getUser(), $course, null);

        $assignments->clearOrderByColumns();
        switch ($fileGroupingType) {
            case "date": $assignments->orderBy('due_at', \Criteria::DESC); break;
            case "assignment": $assignments->addAscendingOrderByColumn("LOWER(assignments.name)"); break;
            case "course": $assignments->addAscendingOrderByColumn("LOWER(courses.name)"); break;
        }

        $assignments = $assignments->find();
        $courses = $this->getRoleUser()->getCourses();

        return array(
            'assignments' => $assignments,
            'courses' => $courses,
            'course' => $course,
            'fileGrouping' => $fileGroupingType,
            'target' => 'files'
        );
    }

    /**
     * @Route("/users/{assignmentId}", name="assignment_solutions_students")
     * @ParamConverter("assignment", options={"mapping": {"assignmentId": "id"}})
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @SecureParam(name="assignment", permissions="VIEW")
     * @Template()
     *
     */
    public function studentsAction(Model\Assignment\Assignment $assignment)
    {
        // TODO: need to create common way to store page states
        $session = $this->getRequest()->getSession();
        $fileGroupingType = $this->getRequest()->get('StudentFileGrouping') ?: ($session->has('StudentFileGrouping') ? $session->get('StudentFileGrouping') : 'date');
        $session->set('StudentFileGrouping', $fileGroupingType);

        $solutions = StudentAssignmentQuery::create()->findStudentsByAssignmentId($assignment->getId());

        switch ($fileGroupingType) {
            case "date": $solutions->orderBy('created_at', \Criteria::DESC); break;
            case "name": $solutions->addAscendingOrderByColumn("LOWER(User.first_name)"); break;
        }
        $solutions = $solutions->find();

        return array(
            'assignment' => $assignment,
            'solutions' => $solutions,
            'fileGrouping' => $fileGroupingType,
            'course' => $assignment->getCourse(),
            'target' => 'files'
        );
    }

    /**
     * @Route("/download/assignment/{assignmentId}", name="assignment_solutions_download")
     * @Route("/download/student/{assignmentId}/{studentId}", name="student_solutions_download")
     * @ParamConverter("assignment", options={"mapping": {"assignmentId": "id"}})
     * @ParamConverter("student", options={"mapping": {"studentId": "id"}})
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @SecureParam(name="assignment", permissions="VIEW")
     *
     */
    public function downloadZipAction(Model\Assignment\Assignment $assignment, Model\User\Student $student = null)
    {
        // TODO: we definitely should have separate class for archive building
        $zip = new \ZipArchive();

        $filename = preg_replace(array("[\s]", "/[^a-z0-9_]/i"), array("_", ""), ($assignment ? $assignment->getName() : '') . ($student ? '_' . $student->getFullName() : ''));
        $filePath = "/data/zip_files/" . $filename . '_solutions_' . time() . '.zip';

        if ($zip->open('.' . $filePath, \ZIPARCHIVE::CREATE)!==TRUE) {
            throw new \Exception('Cannot open file ' . $filename);
        }

        if ($student && $assignment) {
            $studentAssignments = StudentAssignmentQuery::create()->filterByAssignmentAndStudent($student, $assignment)->find();
        } else {
            $studentAssignments = $assignment->getStudentAssignments();
        }

        if ($studentAssignments) {
            $folderCountFiles = array();
            foreach ($studentAssignments as $studentAssignment) {
                foreach ($studentAssignment->getFiles() as $file) {
                    $folder = $student ? '' : $studentAssignment->getStudent()->getFullName() . '/';
                    isset($folderCountFiles[$folder]) ? $folderCountFiles[$folder] ++ : $folderCountFiles[$folder] = 1;
                    if (is_file($file->getAbsolutePath()))
                        $zip->addFile($file->getAbsolutePath(), $folder . sprintf("%02d", $folderCountFiles[$folder]) . ' - ' . $file->getName());
                    else
                        throw new \Exception('One or more files can not be added because not exists.');
                }
            }
        }

        $zip->close();
        return $this->redirect($filePath);
    }
}
