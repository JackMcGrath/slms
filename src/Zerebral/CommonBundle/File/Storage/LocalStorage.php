<?php

namespace Zerebral\CommonBundle\File\Storage;

use Symfony\Component\Filesystem\Filesystem;

class LocalStorage extends Storage
{
    /**
     * Temporary files folder
     * @var string
     */
    private $temporaryFilesFolder;

    /**
     * Files folder
     * @var string
     */
    private $filesFolder;

    /**
     * Web path for files folder
     * @var string
     */
    private $webPath;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * Create new local file storage
     *
     * @param string $filesFolder files folder
     * @param string $temporaryFilesFolder temporary files folder
     * @param string $webPath web path for files folder
     */
    public function __construct($filesFolder = '', $temporaryFilesFolder = '/tmp', $webPath = '/data/')
    {
        $this->setFilesFolder($filesFolder);
        $this->setTemporaryFilesFolder($temporaryFilesFolder);
        $this->setWebPath($webPath);
    }

    /**
     * {@inheritDoc}
     */
    public function upload($filePath, $folder = '', $originalFileName = null)
    {
        $path = $this->getFilesFolder() . '/' . ($folder ? $folder . '/' : '');
        $fileName = $this->generateFileName($originalFileName);
        $this->getFileSystem()->copy($filePath, $path . $fileName);
        return $folder . '/' . $fileName;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl($path)
    {
        return $this->getWebPath() . '/' . $path;
    }

    /**
     * {@inheritDoc}
     */
    public function getAbsolutePath($path)
    {
        return $this->getFilesFolder() . '/' . $path;
    }

    /**
     * {@inheritDoc}
     */
    public function createTemporaryFile($filePath)
    {
        $temporaryFile = $this->generateFileName();
        $this->getFileSystem()->copy($filePath, $this->getTemporaryFilesFolder() . '/' . $temporaryFile);
        return $temporaryFile;
    }

    /**
     * {@inheritDoc}
     */
    public function getTemporaryFile($temporaryFileName)
    {
        if (!$this->getFileSystem()->exists($this->getTemporaryFilesFolder() . '/' . $temporaryFileName)) {
            throw new \Exception("Temporary file {$temporaryFileName} is not exists");
        }

        return $this->getTemporaryFilesFolder() . '/' . $temporaryFileName;
    }

    /**
     * {@inheritDoc}
     */
    public function removeTemporaryFile($temporaryFileName)
    {
        $this->getFileSystem()->remove($this->getTemporaryFile($temporaryFileName));
    }

    /**
     * Set temporary files folder
     *
     * @param string$temporaryFilesFolder
     */
    public function setTemporaryFilesFolder($temporaryFilesFolder)
    {
        $this->temporaryFilesFolder = rtrim($temporaryFilesFolder, DIRECTORY_SEPARATOR);
    }

    /**
     * Get temporary files folder
     *
     * @return string
     */
    public function getTemporaryFilesFolder()
    {
        return $this->temporaryFilesFolder;
    }

    /**
     * Set files folder
     *
     * @param string $filesFolder
     */
    public function setFilesFolder($filesFolder)
    {
        $this->filesFolder = rtrim($filesFolder, DIRECTORY_SEPARATOR);
    }

    /**
     * Get files folder
     *
     * @return string
     */
    public function getFilesFolder()
    {
        return $this->filesFolder;
    }

    /**
     * Set web path of files folder
     *
     * @param string $webPath
     */
    public function setWebPath($webPath)
    {
        $this->webPath = rtrim($webPath, '/');
    }

    /**
     * Get web path of files folder
     *
     * @return string
     */
    public function getWebPath()
    {
        return $this->webPath;
    }

    /**
     * Generate random file name
     *
     * If original file name specified, random file name will have the same extension
     *
     * @param string $originalFileName
     * @return string
     */
    private function generateFileName($originalFileName = null)
    {
        $filename = time() . '_' . rand(10000, 99999);
        if (!empty($originalFileName)) {
            $filename .= '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);
        }
        return $filename;
    }

    /**
     * Get symfony file system instance
     *
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    private function getFileSystem()
    {
        if (is_null($this->fileSystem)) {
            $this->fileSystem = new Filesystem();
        }
        return $this->fileSystem;
    }

}
