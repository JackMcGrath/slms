<?php

namespace Zerebral\BusinessBundle\Model\File;

use Zerebral\BusinessBundle\Model\File\om\BaseFile;

use Zerebral\CommonBundle\Component\FileStorage\FileStorageInterface;
use \Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends BaseFile
{
    /** @var null|FileStorageInterface */
    protected $fileStorage = null;

    /** @var null|string */
    protected $sourcePath = null;

    /** @var UploadedFile null */
    protected $uploadedFile= null;

    /**
     * Autofilling filename
     *
     * @param null|string $sourcePath
     *
     * @throws \Exception
     * @return void
     */
    public function setSourcePath($sourcePath) {
        $this->sourcePath = $sourcePath;

        if (!file_exists($this->sourcePath)) {
            throw new \Exception('File "' . $this->sourcePath . '" was not found');
        }

        if (is_null($this->getName())) {
            $fileInfo = pathinfo($this->sourcePath);
            $this->setName($fileInfo['basename']);
        }
        $this->setSize(filesize($this->sourcePath));
        $this->setMimeType(mime_content_type($this->sourcePath));
    }

    /**
     * @return null|string
     */
    public function getSourcePath() {
        return $this->sourcePath;
    }

    public function getLink() {
        return $this->getFileStorage()->getWebPath() . $this->getName();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     * @throws \Exception
     */
    public function setUploadedFile(UploadedFile $uploadedFile) {
        $this->uploadedFile = $uploadedFile;
        if ($uploadedFile->getError() == UPLOAD_ERR_OK) {
            $this->setSourcePath($uploadedFile->getRealPath());
            $this->setName($uploadedFile->getClientOriginalName());
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getUploadedFile() {
        return $this->uploadedFile;
    }


    /**
     * @param \Zerebral\CommonBundle\Component\FileStorage\FileStorageInterface $fileStorage
     */
    public function setFileStorage(FileStorageInterface $fileStorage) {
        $this->fileStorage = $fileStorage;
        $this->setStorage($fileStorage->getName());
    }

    /**
     * @return \Zerebral\CommonBundle\Component\FileStorage\FileStorageInterface
     */
    public function getFileStorage() {
        return $this->fileStorage;
    }

    /**
     * @param \PropelPDO $con
     * @return bool
     */
    public function preSave(\PropelPDO $con = null) {
        if (!is_null($this->getFileStorage())) {
            if ($this->getFileStorage()->save($this->getSourcePath(), $this->getName())) {
                parent::preSave($con);
            } else {
                return false;
            }
        }
        return true;
    }

    public function __sleep() {
        $properties = parent::__sleep();
        $editedProperties = array();
        foreach ($properties as $field) {
            if ($field != 'uploadedFile') {
                $editedProperties[] = $field;
            }
        }
        return $editedProperties;
    }



}
