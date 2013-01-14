<?php
namespace Zerebral\FrontendBundle\Extension;

use Twig_Extension;

use Zerebral\CommonBundle\Component\FileStorage\FileStorageFactory;
use Zerebral\BusinessBundle\Model\File\File;

// TODO: tbd about this class, I guess his involved not only in file storage operations
class FileStorageExtension extends \Twig_Extension
{
    /** @var FileStorageFactory */
    protected $fileStorageFactory;

    protected static $mimeTypeIconsPath = 'icons/files/pack_01/';
    protected static $mimeTypeSmallIconsPath = 'icons/files/pack_small/';

    protected static $mimeTypeIcons = array(
        'text' => 'text.png',
        'application' => array(
            'pdf' => 'pdf.png',
            'msword' => 'word.png',
            'vnd.ms-excel' => 'excel.png',
            'x-rar' => 'archive.png',
            'x-gzip' => 'archive.png',
            'zip' => 'archive.png'
        ),
        'image' => 'image.png',
        'general' => 'file.png'
    );

    /**
     * @param \Zerebral\CommonBundle\Component\FileStorage\FileStorageFactory $fileStorageFactory
     */
    public function __construct(FileStorageFactory $fileStorageFactory)
    {
        $this->fileStorageFactory = $fileStorageFactory;
    }


    public function getFilters()
    {
        return array(
            'bytes_to_human' => new \Twig_Filter_Method($this, 'bytesToHuman')
        );
    }

    public function getFunctions()
    {
        return array(
            'get_file_storage_link' => new \Twig_Function_Method($this, 'getFileStorageLink'),
            'get_file_icon' => new \Twig_Function_Method($this, 'getFileIcon')
        );
    }

    /**
     * @param File $file
     * @return string
     */
    public function getFileStorageLink(File $file)
    {
        if (!is_null($file)) {
            $file->setFileStorage($this->fileStorageFactory->getFileStorage($file->getStorage()));
            return $file->getLink();
        }
        return '404.png';
    }

    /**
     * @param \Zerebral\BusinessBundle\Model\File\File $file
     *
     * @return \Zerebral\BusinessBundle\Model\File\File
     */
    public function getFileIcon(File $file, $size = null)
    {
        $mimeTypeArray = explode('/', $file->getMimeType());
        $icons = self::$mimeTypeIcons;

        $icon = (isset($icons[$mimeTypeArray[0]])) ? $icons[$mimeTypeArray[0]] : $icons['general'];

        if (is_array($icon)) {
            $icon = isset($icon[$mimeTypeArray[1]]) ? $icon[$mimeTypeArray[1]] : $icons['general'];
        }

        $iconFile = new File();
        $mimeTypeIconsPath = $size == 'small' ? self::$mimeTypeSmallIconsPath : self::$mimeTypeIconsPath;
        $iconFile->setName($mimeTypeIconsPath . $icon);
        $iconFile->setStorage('dummy');

        return $iconFile;
    }

    public function bytesToHuman($bytes, $precision = 2)
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        $terabyte = $gigabyte * 1024;

        if (($bytes >= 0) && ($bytes < $kilobyte)) {
            return $bytes . ' B';

        } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
            return round($bytes / $kilobyte, $precision) . ' KB';

        } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
            return round($bytes / $megabyte, $precision) . ' MB';

        } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
            return round($bytes / $gigabyte, $precision) . ' GB';

        } elseif ($bytes >= $terabyte) {
            return round($bytes / $terabyte, $precision) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }

    public function getName()
    {
        return 'file_storage_extension';
    }
}