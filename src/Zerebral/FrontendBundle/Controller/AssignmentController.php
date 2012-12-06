<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Zerebral\BusinessBundle\Model as Model;

/**
 * @Route("/assignments")
 */
class AssignmentController extends \Zerebral\CommonBundle\Component\Controller
{

   /**
     * @Route("/add", name="assignment_add")
     * @Template()
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $assignment = new Model\Assignment\Assignment();
        $categories = Model\Assignment\AssignmentCategoryQuery::create()->find();
        /**
         * @var \Zerebral\BusinessBundle\Model\User\User $user
         */
        $user = $this->getUser();

        if ($request->isMethod('post')) {

        }

        return array(
            'target' => 'assigments',
            'assignment' => $assignment,
            'categories' => $categories
        );
    }
}
