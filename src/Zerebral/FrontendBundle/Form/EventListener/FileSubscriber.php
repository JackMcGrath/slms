<?php
namespace Zerebral\FrontendBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

use Zerebral\CommonBundle\Component\FileStorage\AbstractFileStorage;

class FileSubscriber implements EventSubscriberInterface {
    /** @var \Symfony\Component\Form\FormBuilder */
    private $builder;

    public function __construct(FormBuilderInterface $builder) {
        $this->builder = $builder;
    }

    public static function getSubscribedEvents() {
        return array(FormEvents::POST_BIND => 'postBind');
    }

    public function postBind(DataEvent $event) {
        $options = $this->builder->getOptions();

        /** @var null|\Zerebral\BusinessBundle\Model\File\File $file  */
        $file = $event->getData();

        if (is_null($file)) {
            return;
        }

        $file->setFileStorage($options['storage']);
    }
}