<?php

namespace Zerebral\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Zerebral\BusinessBundle\Model as Model;

/**
 * @Route("/courses")
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
        $assigment = new Model\Assignment\Assignment();
        /**
         * @var \Zerebral\BusinessBundle\Model\User\User $user
         */
        $user = $this->getUser();

        if ($request->isMethod('post')) {

        }

        return array(
            'target' => 'assigments'
        );
    }
}
