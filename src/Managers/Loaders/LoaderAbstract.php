<?php

namespace BigF\Managers\Loaders;

abstract class LoaderAbstract
{
    protected $path = null;

    public function __construct($path)
    {
        $this->setPath($path);
    }

    /**
     * @param null $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return null
     * @throws \ErrorException
     */
    public function getPath()
    {
        if (empty($this->path)) {
            throw new \ErrorException("Path was not set");
        }

        if (!file_exists($this->path)) {
            throw new \ErrorException("Path was not found - " . $this->path);
        }

        return $this->path;
    }

    public abstract function load();
}