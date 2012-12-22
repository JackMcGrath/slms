<?php
namespace Zerebral\CommonBundle\Component\FileStorage;

interface FileStorageInterface {
    /**
     * @param string $sourceFilePath
     * @param string $fileName
     *
     * @return bool
     * @throws FileStorageException
     */
    public function save($sourceFilePath, $fileName);

    /**
     * @return string
     */
    public function getName();
}