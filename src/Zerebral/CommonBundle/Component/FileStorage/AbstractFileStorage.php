<?php
namespace Zerebral\CommonBundle\Component\FileStorage;

use Zerebral\CommonBundle\Component\FileStorage\FileStorageException;

use Zerebral\CommonBundle\Component\FileStorage\LocalFileStorage;

abstract class AbstractFileStorage {

    /**
     * @param string $type String with FileStorage type
     * @return \Zerebral\CommonBundle\Component\FileStorage\LocalFileStorage
     * @throws FileStorageException Throw in case of unknown FileStorage type
     */
    public static function getFileStorage($type) {
        switch ($type) {
            case 'local': return new LocalFileStorage();
            default: throw new FileStorageException('Unknown FileStorage type: "' . $type . '"');
        }
    }


    /**
     * @param string $sourceFilePath Source file path
     * @param string $fileName       File name
     */
    abstract public function save($sourceFilePath, $fileName);
}