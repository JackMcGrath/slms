<?php
namespace Zerebral\CommonBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class NotificationDailyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('notification-daily:create')
            ->setDescription('Creating notifications');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $todayAssignments = \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery::create()->findToday()->find();
        \Zerebral\BusinessBundle\Model\Notification\NotificationPeer::createAssignmentDueDateSingleStudent($todayAssignments);
        \Zerebral\BusinessBundle\Model\Notification\NotificationPeer::createAssignmentDueDateSingleTeacher($todayAssignments);

        $studentAssignmentsCount = \Zerebral\BusinessBundle\Model\Assignment\StudentAssignmentQuery::create()->findTodayCount()->find();
        \Zerebral\BusinessBundle\Model\Notification\NotificationPeer::createAssignmentDueDateMultipleStudent($studentAssignmentsCount);

        $teachersAssignmentsCount = \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery::create()->findTodayCountForTeacher()->find();
        \Zerebral\BusinessBundle\Model\Notification\NotificationPeer::createAssignmentDueDateMultipleTeacher($teachersAssignmentsCount);
    }
}