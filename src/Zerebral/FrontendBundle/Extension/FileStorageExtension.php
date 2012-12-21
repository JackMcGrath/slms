<?php
namespace Zerebral\FrontendBundle\Extension;

use Twig_Extension;

use Zerebral\CommonBundle\Component\FileStorage\FileStorageFactory;
use Zerebral\BusinessBundle\Model\File\File;

class FileStorageExtension extends \Twig_Extension {
    /** @var FileStorageFactory */
    protected $fileStorageFactory;

    /**
     * @param \Zerebral\CommonBundle\Component\FileStorage\FileStorageFactory $fileStorageFactory
     */
    public function __construct(FileStorageFactory $fileStorageFactory) {
        $this->fileStorageFactory = $fileStorageFactory;
    }

    public function getFunctions() {
        return array(
            'get_file_storage_link' => new \Twig_Function_Method($this, 'getFileStorageLink')
        );
    }

    /**
     * @param File $file
     * @return string
     */
    public function getFileStorageLink($file) {
        if (!is_null($file)) {
            $file->setFileStorage($this->fileStorageFactory->getFileStorage($file->getStorage()));
            return $file->getLink();
        }
        return '404.png';
    }

    public function getName() {
        return 'file_storage_extension';
    }
}