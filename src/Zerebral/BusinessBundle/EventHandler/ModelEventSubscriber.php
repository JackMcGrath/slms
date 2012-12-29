<?php

namespace Zerebral\BusinessBundle\EventHandler;

use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;

class ModelEventSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'model.insert.post' => 'proxy',
            'model.update.post' => 'proxy',
            'model.save.post' => 'proxy',
            'model.delete.post' => 'proxy',

            'model.insert.pre' => 'proxy',
            'model.update.pre' => 'proxy',
            'model.save.pre' => 'proxy',
            'model.delete.pre' => 'proxy',
        );
    }

    /**
     * Trigger model-specific event.
     * For example: it will trigger article_comments.save.post event on model.save.post for ArticleComment model
     *
     * @param \Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent $event
     */
    public function proxy(ModelEvent $event)
    {
        $model = $event->getModel();
        $modelEventName = str_replace('model', $this->getModelTableName($model), $event->getName());
        $event->getDispatcher()->dispatch($modelEventName, new ModelEvent($model));
    }

    /**
     * @param \BaseObject $model
     * @return string
     */
    private function getModelTableName(\BaseObject $model)
    {
        $peerClass = get_class($model) . 'Peer';
        return $peerClass::TABLE_NAME;
    }

}
