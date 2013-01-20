<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Zerebral\CommonBundle\HttpFoundation\FormJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

use Zerebral\FrontendBundle\Form\Type as FormType;
use Zerebral\BusinessBundle\Model as Model;

/**
 * @Route("/student/assignment/solution")
 */
class StudentAssignmentSolutionController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/save/{id}", name="ajax_student_assignment_solution_save")
     * @ParamConverter("studentAssignment")
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT')")
     * @SecureParam(name="studentAssignment", permissions="UPLOAD")
     * @Template()
     */
    public function saveAction(Model\Assignment\StudentAssignment $studentAssignment)
    {
        $assignmentSolutionType = new FormType\AssignmentSolutionType();

        $form = $this->createForm($assignmentSolutionType, $studentAssignment);
        $form->bind($this->getRequest());

        if ($form->isValid()) {
            $form->getData()->save();
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
     * @Route("/remove/{id}/{fileId}", name="student_assignment_solution_remove")
     * @ParamConverter("studentAssignment", options={"mapping": {"id": "id"}})
     * @ParamConverter("file", options={"mapping": {"fileId": "id"}})
     *
     * @PreAuthorize("hasRole('ROLE_STUDENT')")
     * @SecureParam(name="studentAssignment", permissions="REMOVE")
     * @Template()
     */
    public function removeSolutionAction(Model\Assignment\StudentAssignment $studentAssignment, \Zerebral\BusinessBundle\Model\File\File $file)
    {
        if (!$file->getStudentAssignments()->contains($studentAssignment)) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(404, 'File doesn\'t belong to student assignment');
        }

        $file->delete();

        return new JsonResponse(array('success' => true));
    }

    /**
     * @Route("/form/{id}", name="student_assignment_solution_form")
     * @ParamConverter("studentAssignment")
     * @PreAuthorize("hasRole('ROLE_STUDENT')")
     * @Template
     */
    public function formAction(Model\Assignment\StudentAssignment $studentAssignment)
    {
        $form = $this->createForm(new FormType\AssignmentSolutionType(), $studentAssignment);

        return array(
            'form' => $form->createView(),
            'studentAssignment' => $studentAssignment
        );
    }
}
