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

use Zerebral\BusinessBundle\Model\User\User;
use Zerebral\BusinessBundle\Model\User\UserQuery;

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
        return $this->redirect($this->generateUrl('superadmin_users'));
    }

    /**
     * @Route("/users/{page}", name="superadmin_users", requirements={"page" = "\d+"}, defaults={"page" = "0"})
     * @PreAuthorize("hasRole('ROLE_SUPERADMIN')")
     * @Template
     */
    public function usersAction($page)
    {

        $query = UserQuery::create();

        if(!is_null($this->getRequest()->get('filter', null))) {
            $query->filterByIsActive($this->getRequest()->get('filter') == 'active');
        }


        if(!is_null($this->getRequest()->get('search', null))) {
            $query->_and()
                  ->where('LOWER(CONCAT(users.first_name, " ", users.last_name)) LIKE ?', '%' . strtolower($this->getRequest()->get('search')) . '%', \PDO::PARAM_STR)
                  ->_or()->where('LOWER(users.email) LIKE ?', '%' . strtolower($this->getRequest()->get('search')) . '%', \PDO::PARAM_STR);
        }


        $paginator = $query->paginate($page);
        return array(
            'searchCriteria' => $this->getRequest()->get('search', ''),
            'filterCriteria' => $this->getRequest()->get('filter', 'all'),
            'paginator' => $paginator,
            'target' => 'home'
        );
    }

    /**
     * @Route("/users/{userId}/block", name="ajax_superadmin_users_block")
     * @PreAuthorize("hasRole('ROLE_SUPERADMIN')")
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     * @Template
     */
    public function usersBlockAction(User $user)
    {
        if ($user->getIsActive()) {
            $user->setIsActive(false);
            $message = 'blocked';
        } else {
            $user->setIsActive(true);
            $message = 'unblocked';
        }

        $user->save();

        $this->setFlash('user_blocking', 'User "' . $user->getFullName() . ' was succesfully '. $message);
        return new \Symfony\Component\HttpFoundation\JsonResponse(array('status' => 'ok'));
    }

}
