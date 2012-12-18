<?php
namespace Zerebral\CommonBundle\Component\FileStorage;

use Zerebral\CommonBundle\Component\FileStorage\AbstractFileStorage;
use Zerebral\CommonBundle\Component\FileStorage\FileStorageException;

class LocalFileStorage extends AbstractFileStorage {

    /** @var string */
    protected $path;
    /** @var string */
    protected $webPath;


    public function __construct($path, $webPath) {
        // Converting slash to a system specific directory separator
        $this->path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $this->webPath = $webPath;
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    public function getWebPath() {
        return $this->webPath;
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
