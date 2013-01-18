<?php
namespace Zerebral\CommonBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Zerebral\BusinessBundle\Model\Feed\FeedItem;
use Zerebral\BusinessBundle\Model\Feed\FeedContent;
use Zerebral\BusinessBundle\Model\Feed\FeedItemQuery;


class FeedCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('feed:create')
            ->setDescription('Creating feeds items for assignments');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assignmentQuery = new \Zerebral\BusinessBundle\Model\Assignment\AssignmentQuery();

        $output->write('Creating feed items for "Assignments"... ');

        $assignments = $assignmentQuery->find();
        $output->writeln('Found ' . count($assignments) . ' entities');


        $createdFeedsCount = 0;

        /** @var $assignment \Zerebral\BusinessBundle\Model\Assignment\Assignment */
        foreach ($assignments as $assignment) {
            $feedItemQuery = new FeedItemQuery();
            $output->write('Adding feed item for assignment "' . $assignment->getName() . '"... ');
            if (is_null($feedItemQuery->findOneBy('assignmentId', $assignment->getId()))) {
                $feedContent = new FeedContent();
                $feedContent->setType('assignment');

                $feedItem = new FeedItem();
                $feedItem->setAssignment($assignment);
                $feedItem->setCourse($assignment->getCourse());
                $feedItem->setCreatedBy($assignment->getTeacher()->getUser()->getId());
                $feedItem->setFeedContent($feedContent);

                if ($feedItem->save()) {
                    $output->writeln('Success');
                    $createdFeedsCount++;
                } else {
                    $output->writeln('Fail');
                }
            } else {
                $output->writeln('Feed already exists');
            }
        }

        $output->writeln('Total feeds created: ' . $createdFeedsCount);


    }
}