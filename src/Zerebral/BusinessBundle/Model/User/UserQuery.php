<?php

namespace Zerebral\BusinessBundle\Model\User;

use Zerebral\BusinessBundle\Model\User\om\BaseUserQuery;

class UserQuery extends BaseUserQuery
{
    public function findForSuggestByNameForUser($name, User $user)
    {
        $this->filterById($user->getId(), \Criteria::NOT_EQUAL);
        $this->add('LCASE(first_name)', strtolower($name) . '%', \Criteria::LIKE);
        $this->addOr('LCASE(last_name)', $name . '%', \Criteria::LIKE);

        return $this->find();
    }
}
