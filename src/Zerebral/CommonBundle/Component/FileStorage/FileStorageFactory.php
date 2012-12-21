<?php
namespace Zerebral\CommonBundle\Component\FileStorage;

use Zerebral\CommonBundle\Component\FileStorage\FileStorageException;

use Zerebral\CommonBundle\Component\FileStorage\LocalFileStorage;

class FileStorageFactory {

    /** @var array */
    protected $storages;

    /** @var array */
    protected $config;



    public function __construct($config) {
        $this->config = $config;
    }


    private function createLocalStorage() {
        if (!isset($this->config['local'])) {
            throw new FileStorageException('Please set storage.local section in config file');
        }

        $config = $this->config['local'];
        $this->storages['local'] = new LocalFileStorage($config['path'], $config['webpath']);
    }


    /**
     * @param string $type String with FileStorage type
     * @return \Zerebral\CommonBundle\Component\FileStorage\LocalFileStorage
     * @throws FileStorageException Throw in case of unknown FileStorage type
     */
    public function getFileStorage($type) {
        if (!isset($this->storages[$type])) {
            switch ($type) {
                case 'local': { $this->createLocalStorage(); break; }
                default: throw new FileStorageException('Unknown FileStorage type: "' . $type . '"');
            }
        }

        return $this->storages[$type];
    }
}