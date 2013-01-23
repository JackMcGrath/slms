<?php
namespace Zerebral\FrontendBundle\Extension;

use Twig_Extension;

use Zerebral\BusinessBundle\Model\File\File;

// TODO: tbd about this class, I guess his involved not only in file storage operations
class FileStorageExtension extends \Twig_Extension
{

    protected static $mimeTypeIconsPath = '/img/icons/files/pack_01/';
    protected static $mimeTypeSmallIconsPath = '/img/icons/files/pack_small/';

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


    public function getFilters()
    {
        return array(
            'bytes_to_human' => new \Twig_Filter_Method($this, 'bytesToHuman')
        );
    }

    public function getFunctions()
    {
        return array(
            'get_file_icon' => new \Twig_Function_Method($this, 'getFileIcon'),
            'user_avatar' => new \Twig_Function_Method($this, 'getUserAvatar'),
        );
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

        $mimeTypeIconsPath = $size == 'small' ? self::$mimeTypeSmallIconsPath : self::$mimeTypeIconsPath;

        return $mimeTypeIconsPath . $icon;
    }

    public function bytesToHuman($bytes, $precision = 2)
    {
        static $unitLabels = array('B', 'KB', 'MB', 'GB', 'TB');

        $unit = floor(log($bytes, 1024));
        $size = round($bytes / pow(1024, $unit), $precision);

        return $size . ' ' . $unitLabels[$unit];
    }

    public function getName()
    {
        return 'file_storage_extension';
    }

    public function getUserAvatar($user)
    {
        $avatar = $user->getAvatar();

        if (empty($avatar)) {
            return '/img/avatar-placeholder.png';
        }

        if (!$avatar->getUrl()) {
            return '/img/avatar-placeholder.png';
        }

        return $avatar->getUrl();
    }
}