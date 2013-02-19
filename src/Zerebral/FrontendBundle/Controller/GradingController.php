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
use Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery;

/**
 * @Route("/grading")
 */

class GradingController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/student-assignment/{id}", name="ajax_student_assignment")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Zerebral\CommonBundle\HttpFoundation\FormJsonResponse
     */
    public function studentAction(Model\Assignment\StudentAssignment $studentAssignment)
    {
        $form = $this->initGradingForm($studentAssignment);

        $anotherAssignments = StudentAssignmentQuery::create()->filterSortedByAssignmentId($studentAssignment->getAssignmentId())->find();
        $nextPrev = \Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentPeer::getNextPrev($anotherAssignments, $studentAssignment);

        $content = $this->render('ZerebralFrontendBundle:Grading:studentAssignment.html.twig',
            array(
                'studentAssignment' => $studentAssignment,
                'form' => $form->createView())
        )->getContent();

        return new JsonResponse(array('has_errors' => false, 'content' => $content, 'nextPrev' => $nextPrev, 'assignment' => $studentAssignment->getAssignment()->toArray()));
    }

    protected function initGradingForm($studentAssignment = null)
    {
        $form = $this->createForm(new FormType\GradingType(), $studentAssignment);
        return $form;
    }

    /**
     * @Route("/edit/{id}", name="ajax_student_assignment_edit")
     * @ParamConverter("studentAssignment")
     * @PreAuthorize("hasRole('ROLE_TEACHER')")
     */
    public function ajaxEditAction(Model\Assignment\StudentAssignment $studentAssignment)
    {
        $form = $this->initGradingForm($studentAssignment);
        $form->bind($this->getRequest());

        $isContinue = (bool)$this->getRequest()->get('continue', 0);
        $nextPrev = array();
        if ($isContinue) {
            $anotherAssignments = StudentAssignmentQuery::create()->filterSortedByAssignmentId($studentAssignment->getAssignmentId())->find();
            $nextPrev = \Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentPeer::getNextPrev($anotherAssignments, $studentAssignment);
        }

        if ($form->isValid()) {
            $studentAssignment->setGradedAt(date('Y-m-d H:i:s'));
            $studentAssignment->save();
            return new JsonResponse(array(
                'success' => true, 'content' => $studentAssignment->toArray(), 'nextPrev' => $nextPrev
            ));
        }

        return new FormJsonResponse($form);
    }
}