<?php

namespace Zerebral\BusinessBundle\Model\Message;

use Zerebral\BusinessBundle\Model\Message\om\BaseMessageQuery;

class MessageQuery extends BaseMessageQuery
{
    public function findInboxByUser(\Zerebral\BusinessBundle\Model\User\User $user)
    {
        $this->filterByUserId($user->getId());
        $this->filterByToId($user->getId());

        $this->groupByThreadId();

        $this->addDescendingOrderByColumn('MAX(IF(`to_id` = ' . $user->getId() . ', `created_at`, 0))');

        $this->withColumn('MAX(`created_at`)', 'lastMessageDate');
        $this->withColumn('SUM(IF(`is_read` = 0, 1, 0))', 'unreadCount');

        return $this->find();
    }

    public function findSentByUser(\Zerebral\BusinessBundle\Model\User\User $user)
    {
        $this->filterByUserId($user->getId());
        $this->filterByFromId($user->getId());

        $this->addDescendingOrderByColumn('created_at');

        $this->withColumn('created_at', 'lastMessageDate');
        $this->withColumn('IF(`is_read` = 0, 1, 0)', 'unreadCount');

        return $this->find();
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

        return $this->count();
    }
}
