<?php

namespace Zerebral\BusinessBundle\Model\Message;

use Zerebral\BusinessBundle\Model\Message\om\BaseMessageQuery;

class MessageQuery extends BaseMessageQuery
{
    public function filterInboxByUser(\Zerebral\BusinessBundle\Model\User\User $user)
    {
        //$this->filterByUserId($user->getId());
//        $this->filterByToId($user->getId());

        $lastMessages = MessageQuery::create()->filterByUserId($user->getId())->addDescendingOrderByColumn('created_at');
        $this->addSelectQuery($lastMessages);

        $this->groupByThreadId();
        $this->addDescendingOrderByColumn('IF(`to_id` = ' . $user->getId() . ', `created_at`, 0)');

        $this->withColumn('MAX(`created_at`)', 'lastMessageDate');
        $this->withColumn('SUM(IF(`is_read` = 0, 1, 0))', 'unreadCount');

        $this->having('SUM(IF(`to_id` = ' . $user->getId() . ', 1, 0)) > 0');
        return $this;
    }

    public function filterSentByUser(\Zerebral\BusinessBundle\Model\User\User $user)
    {
        $this->filterByUserId($user->getId());
        $this->filterByFromId($user->getId());

        $this->addDescendingOrderByColumn('created_at');

        $this->withColumn('created_at', 'lastMessageDate');
        $this->withColumn('IF(`is_read` = 0, 1, 0)', 'unreadCount');

        return $this;
    }

    public function findThreadForUser($threadId, \Zerebral\BusinessBundle\Model\User\User $user)
    {
        $this->findByThreadId($threadId);
        $this->filterByUserId($user->getId());

        $this->addAscendingOrderByColumn('created_at');

        return $this->find();
    }

    public function getUnreadCount(\Zerebral\BusinessBundle\Model\User\User $user)
    {
        $this->filterByUserId($user->getId());
        $this->filterByToId($user->getId());
        $this->findByIsRead(false);
        $this->groupByThreadId();

        return $this->count();
    }
}
