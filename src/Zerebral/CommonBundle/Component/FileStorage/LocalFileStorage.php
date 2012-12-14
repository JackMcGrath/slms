<?php
namespace Zerebral\CommonBundle\Component\FileStorage;

use Zerebral\CommonBundle\Component\FileStorage\AbstractFileStorage;
use Zerebral\CommonBundle\Component\FileStorage\FileStorageException;

class LocalFileStorage extends AbstractFileStorage {

    /** @var string */
    protected $path = '/home/deespater/Work/zerebral/web/data';


    public function __construct() {
        // Converting slash to a system specific directory separator
        $this->path = str_replace('/', DIRECTORY_SEPARATOR, $this->path);
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }


    /**
     * @param string $sourceFilePath
     * @param string $fileName
     *
     * @return bool
     * @throws FileStorageException
     */
    public function save($sourceFilePath, $fileName) {

        if (!file_exists($sourceFilePath)) {
            throw new FileStorageException('Cannot find file "' . $sourceFilePath . '"');
        }

        $targetFilePath = $this->path . DIRECTORY_SEPARATOR . $fileName;
        if (!copy($sourceFilePath, $targetFilePath)) {
            throw new FileStorageException('Cannot move file "' . $fileName . '" to "' . $targetFilePath . '"');
        }

        return true;
    }
}
