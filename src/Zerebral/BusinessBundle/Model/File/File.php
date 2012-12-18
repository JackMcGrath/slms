<?php

namespace Zerebral\BusinessBundle\Model\File;

use Zerebral\BusinessBundle\Model\File\om\BaseFile;

use Zerebral\CommonBundle\Component\FileStorage\AbstractFileStorage;
use \Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends BaseFile
{
    /** @var null|AbstractFileStorage */
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
        if ($uploadedFile->getError() === 1) {
            throw new \Exception('File "' . $uploadedFile->getClientOriginalName() . '" was not uploaded due to uploadedFile error');
        }

        $this->setSourcePath($uploadedFile->getRealPath());
        $this->setName($uploadedFile->getClientOriginalName());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getUploadedFile() {
        return $this->uploadedFile;
    }


    /**
     * @param \Zerebral\CommonBundle\Component\FileStorage\AbstractFileStorage $fileStorage
     */
    public function setFileStorage(AbstractFileStorage $fileStorage) {
        $this->fileStorage = $fileStorage;
    }

    /**
     * @return \Zerebral\CommonBundle\Component\FileStorage\AbstractFileStorage
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



}
