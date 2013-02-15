<?php

namespace Zerebral\BusinessBundle\Model\File;

use Zerebral\BusinessBundle\Model\File\om\BaseFile;
use Glorpen\PropelEvent\PropelEventBundle\Events\ModelEvent;

use Zerebral\CommonBundle\Component\FileStorage\FileStorageInterface;
use Zerebral\CommonBundle\File\Storage\Storage;
use \Symfony\Component\HttpFoundation\File\UploadedFile;
use Glorpen\PropelEvent\PropelEventBundle\Dispatcher\EventDispatcherProxy;

class File extends BaseFile implements \Zerebral\CommonBundle\File\Model\FileStorageAware
{
    /**
     * File storage
     *
     * @var Storage|null
     */
    protected $fileStorage = null;

    /**
     * Uploaded file reference
     *
     * @var UploadedFile|null
     */
    protected $uploadedFile = null;

    /**
     * Temporary uploaded file name
     *
     * @var string|null
     */
    protected $temporaryFile = null;

    /**
     * File folder
     *
     * @var string
     */
    protected $folder = "";

    public function __construct()
    {
        parent::__construct();
        EventDispatcherProxy::trigger('model.file_storage.set_default', new ModelEvent($this));
    }

    /**
     * @deprecated
     * @return string
     */
    public function getLink()
    {
        return $this->getUrl();
    }

    public function getUrl()
    {
        return $this->getFileStorage()->getUrl($this->getPath());
    }

    /**
     * this method required for files archiving
     * TODO: we should handle cases when files are not locally placed
     *
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->getFileStorage()->getAbsolutePath($this->getPath());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     * @throws \Exception
     */
    public function setUploadedFile(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        if ($uploadedFile->isValid()) {
            $this->setName($uploadedFile->getClientOriginalName());
            $this->setMimeType($uploadedFile->getClientMimeType());
            $this->setSize($uploadedFile->getFileInfo()->getSize());

            $this->temporaryFile = $this->getFileStorage()->createTemporaryFile($uploadedFile);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }


    /**
     * @param \PropelPDO $con
     * @return bool
     */
    public function preSave(\PropelPDO $con = null)
    {
        if (!is_null($this->getFileStorage()) && !empty($this->temporaryFile)) {
            $temporaryFile = $this->getFileStorage()->getTemporaryFile($this->temporaryFile);
            $this->setPath($this->getFileStorage()->upload($temporaryFile, $this->getFolder(), $this->getName()));
            $this->setSize(filesize($temporaryFile));
        }
        return parent::preSave($con);
    }

    public function postSave(\PropelPDO $con = null)
    {
        if (!empty($this->temporaryFile)) {
            $this->getFileStorage()->removeTemporaryFile($this->temporaryFile);
        }
        parent::postSave();

    }

    public function __sleep()
    {
        $properties = parent::__sleep();
        $editedProperties = array();
        foreach ($properties as $field) {
            if ($field != 'uploadedFile') {
                $editedProperties[] = $field;
            }
        }
        return $editedProperties;
    }

    public function postHydrate($row, $startcol = 0, $rehydrate = false)
    {
        parent::postHydrate($row, $startcol, $rehydrate);
        EventDispatcherProxy::trigger('model.file_storage.update', new ModelEvent($this));
    }

    public function setStorage($v)
    {
        parent::setStorage($v);

        if ($this->isColumnModified(FilePeer::STORAGE)) {
            EventDispatcherProxy::trigger('model.file_storage.update', new ModelEvent($this));
        }
        return $this;
    }

    /**
     * Set file storage
     *
     * @param \Zerebral\CommonBundle\File\Storage\Storage $storage
     *
     */
    public function setFileStorage(\Zerebral\CommonBundle\File\Storage\Storage $storage = null)
    {
        $this->fileStorage = $storage;

        $storageAlias = !empty($storage) ? $storage->getAlias() : null;
        if ($this->getStorage() != $storageAlias) {
            $this->setStorage($storageAlias);
        }
    }

    /**
     * Get file storage
     *
     * @return \Zerebral\CommonBundle\File\Storage\Storage|null $storage
     */
    public function getFileStorage()
    {
        return $this->fileStorage;
    }

    /**
     * Get file storage alias
     *
     * @return string
     */
    public function getFileStorageAlias()
    {
        return $this->getStorage();
    }

    /**
     * Set files storage alias
     * @param string $alias
     *
     */
    public function setFileStorageAlias($alias)
    {
        $this->setStorage($alias);
    }

    /**
     * @param null|string $temporaryFile
     */
    public function setTemporaryFile($temporaryFile)
    {
        $this->temporaryFile = $temporaryFile;
    }

    /**
     * @return null|string
     */
    public function getTemporaryFile()
    {
        return $this->temporaryFile;
    }

    /**
     * Set file folder
     *
     * @param string $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * Get file folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     *
     * @param File $copyObj
     * @param bool $deepCopy
     * @param bool $makeNew
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        parent::copyInto($copyObj, $deepCopy, $makeNew);
        $copyObj->setFileStorage($this->getFileStorage());
        $copyObj->setTemporaryFile($this->copyTemporaryFile());
    }

    private function copyTemporaryFile()
    {
        $file = $this->getFileStorage()->getTemporaryFile($this->getTemporaryFile());
        return $this->getFileStorage()->createTemporaryFile($file);
    }
}
