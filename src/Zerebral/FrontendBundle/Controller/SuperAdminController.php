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
  * @Route("/superadmin-area")
 */
class SuperAdminController extends \Zerebral\CommonBundle\Component\Controller
{
    /**
     * @Route("/", name="superadmin_index")
     * @PreAuthorize("hasRole('ROLE_SUPERADMIN')")
     * @Template
     */
    public function indexAction()
    {
        return array(
            'target' => 'home'
        );
    }

}
