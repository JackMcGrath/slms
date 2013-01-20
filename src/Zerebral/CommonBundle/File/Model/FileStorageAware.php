<?php

namespace Zerebral\CommonBundle\File\Model;

use Zerebral\CommonBundle\File\Storage\Storage;

interface FileStorageAware
{
    /**
     * Set file storage
     *
     * @param \Zerebral\CommonBundle\File\Storage\Storage $storage
     *
     */
    public function setFileStorage(Storage $storage = null);

    /**
     * Get file storage
     *
     * @return \Zerebral\CommonBundle\File\Storage\Storage|null $storage
     */
    public function getFileStorage();

    /**
     * Get file storage alias
     *
     * @return string
     */
    public function getFileStorageAlias();

    /**
     * Set files storage alias
     * @param string $alias
     *
     */
    public function setFileStorageAlias($alias);
}
