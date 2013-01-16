<?php
namespace Zerebral\CommonBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class NotificationHourlyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('notification-hourly:create')
            ->setDescription('Creating hourly notifications');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inCompletedAssignments = \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery::create()->findInCompletedNow()->find();
        \Zerebral\BusinessBundle\Model\Notification\NotificationPeer::createAssignmentInCompleted($inCompletedAssignments);

        //\Zerebral\BusinessBundle\Model\Notification\NotificationPeer::createAssignmentComplete($todayAssignments);

//        $studentAssignmentsCount = \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery::create()->findIncompletedNow()->find();
//        \Zerebral\BusinessBundle\Model\Notification\NotificationPeer::createAssignmentIncomplete($studentAssignmentsCount);
    }
}