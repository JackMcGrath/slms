<?php
namespace Zerebral\CommonBundle\Component\FileStorage;

use Zerebral\CommonBundle\Component\FileStorage\FileStorageInterface;
use Zerebral\CommonBundle\Component\FileStorage\FileStorageException;

class DummyFileStorage implements FileStorageInterface {

    /** @var string */
    protected $webPath;


    public function __construct($webPath) {
        $this->webPath = $webPath;
    }

    public function getWebPath() {
        return $this->webPath;
    }

    public function getName() {
        return 'dummy';
    }

    /**
     * @param string $sourceFilePath
     * @param string $fileName
     *
     * @return bool
     * @throws FileStorageException
     */
    public function save($sourceFilePath, $fileName) {
        throw new FileStorageException('You can\'t save file with dummy storage!');
    }
}
