<?php

namespace Zerebral\CommonBundle\File\Storage;

abstract class Storage
{
    /**
     * Storage string alias
     * @var null|string
     */
    private $alias = null;

    /**
     * Upload file to storage
     *
     * @param string $sourceFilePath source file path
     * @param string $folder target folder
     * @param string|null $fileName original file name
     * @return string uploaded file path
     */
    abstract public function upload($sourceFilePath, $folder = '', $fileName = null);

    /**
     * Get file url
     *
     * @param string $path file path
     * @return string url
     */
    abstract public function getUrl($path);

    /**
     * Create temporary file
     *
     * @param string $filePath source file path
     * @return string temporary file path
     */
    abstract public function createTemporaryFile($filePath);

    /**
     * Remove temporary file
     *
     * @param string $temporaryFilePath file path
     * @return mixed
     */
    abstract public function removeTemporaryFile($temporaryFilePath);

    /**
     * Get absolute path for temporary file
     *
     * @param $temporaryFilePath
     * @return string
     */
    abstract public function getTemporaryFile($temporaryFilePath);

    /**
     * @param null|string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return null|string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Get absolute file path
     *
     * null would be returned if storage did not mapped to local file system
     *
     * @param $path
     * @return string|null
     */
    public function getAbsolutePath($path)
    {
        return null;
    }
}
