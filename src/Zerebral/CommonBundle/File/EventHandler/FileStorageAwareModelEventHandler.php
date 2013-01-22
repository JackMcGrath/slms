<?php
namespace Zerebral\CommonBundle\File\EventHandler;

use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;
use Zerebral\CommonBundle\File\Model\FileStorageAware;
use Zerebral\CommonBundle\File\Storage\StorageManager;


class FileStorageAwareModelEventHandler implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /**
     * @var StorageManager
     */
    private $storageManager;

    public function __construct(StorageManager $storageManager)
    {
        $this->setStorageManager($storageManager);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'model.file_storage.update' => 'updateFileStorage',
            'model.file_storage.set_default' => 'setDefaultFileStorage',
        );
    }

    /**
     * Set default file storage for right after we create new file
     *
     * @param \Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent $event
     */
    public function setDefaultFileStorage(ModelEvent $event)
    {
        /** @var $model FileStorageAware */
        $model = $event->getModel();
        if ($model instanceof FileStorageAware) {
            $model->setFileStorage($this->getStorageManager()->getDefault());
            $model->setFileStorageAlias($this->getStorageManager()->getDefault()->getAlias());
        }
    }

    /**
     * Change file storage if storage alias is changed
     *
     * @param \Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent $event
     */
    public function updateFileStorage(ModelEvent $event)
    {
        /** @var $model FileStorageAware */
        $model = $event->getModel();
        if ($model instanceof FileStorageAware) {
            $fileStorageAlias = $model->getFileStorageAlias();

            if (empty($fileStorageAlias)) {
                $model->setFileStorage(null);
            } elseif ($model->getFileStorage()->getAlias() != $fileStorageAlias) {
                $fileStorage = $this->getStorageManager()->get($fileStorageAlias);
                $model->setFileStorage($fileStorage);
            }
        }
    }

    /**
     * @param \Zerebral\CommonBundle\File\Storage\StorageManager $storageManager
     */
    public function setStorageManager($storageManager)
    {
        $this->storageManager = $storageManager;
    }

    /**
     * @return \Zerebral\CommonBundle\File\Storage\StorageManager
     */
    public function getStorageManager()
    {
        return $this->storageManager;
    }
}
