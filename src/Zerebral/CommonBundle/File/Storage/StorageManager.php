<?php

namespace Zerebral\CommonBundle\File\Storage;

class StorageManager
{

    /**
     * @var Storage[]
     */
    private $storages = array();

    /**
     * @var null|string
     */
    private $defaultStorageAlias = null;

    /**
     * Override defined storages
     *
     * @param Storage[] $storages
     */
    public function setStorages(array $storages)
    {
        $this->storages = $storages;
    }

    /**
     * Get defined storages
     *
     * @return Storage[]
     */
    public function getStorages()
    {
        return $this->storages;
    }

    /**
     * Add storage
     *
     * @param Storage $storage
     * @param string $alias
     */
    public function add(Storage $storage, $alias)
    {
        $storage->setAlias($alias);
        $this->storages[$alias] = $storage;
    }

    /**
     * Get storage by alias
     * @param string $alias
     * @return Storage
     * @throws \Exception
     */
    public function get($alias)
    {
        if (!$this->has($alias)) {
            throw new \Exception("File storage '{$alias}' is not defined");
        }

        return $this->storages[$alias];
    }

    /**
     * Check if storage exists
     *
     * @param string $alias
     * @return bool
     */
    public function has($alias)
    {
        return isset($this->storages[$alias]);
    }

    /**
     * Count defined storages
     *
     * @return int
     */
    public function count()
    {
        return count($this->storages);
    }

    /**
     * Set default storage alias
     *
     * @param null|string $defaultStorageAlias
     */
    public function setDefaultStorageAlias($defaultStorageAlias)
    {
        $this->defaultStorageAlias = $defaultStorageAlias;
    }

    /**
     * Get default storage alias
     *
     * @return null|string
     */
    public function getDefaultStorageAlias()
    {
        return $this->defaultStorageAlias;
    }

    /**
     * Get default storage
     *
     * @return null|Storage
     */
    public function getDefault()
    {
        if (empty($this->defaultStorageAlias) || !$this->has($this->defaultStorageAlias)) {
            return null;
        }

        return $this->get($this->defaultStorageAlias);
    }
}
