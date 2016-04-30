<?php

namespace LMO\Hook\Checker;

use LMO\Hook\File\File;
use LMO\Hook\File\Files;

abstract class CheckerAbstract
{
    protected $name;
    protected $projectPath = '';
    protected $vendorBinPaths;
    protected $extensions = [];
    protected $config = [];

    public function __construct()
    {
        if (empty($this->name)) {
            $namespace = explode('\\', get_class($this));
            $this->name = end($namespace);
        }

        if (empty($this->extensions)) {
            throw new \Exception(
                'A checker must care about file extensions'
            );
        }
    }

    /**
     * @param Files  $files
     * @return array An array of error messages
     */
    abstract protected function getErrors($files);

    /**
     * @param Files $files
     * @return array
     * @throws \Exception
     */
    public function checkFiles($files)
    {
        if (empty($this->projectPath)) {
            throw new \Exception('Repository must be set before checking files');
        }

        $filesToCheck = $files->filterByExtensions($this->extensions);
        if ($filesToCheck->count() === 0) {
            return [];
        }
        return $this->getErrors($filesToCheck);
    }

    /**
     * @param string $fileName (Absolute path)
     * @param Files  $files
     * @return File|bool
     */
    protected function findEditedFile($fileName, $files)
    {
        $fileName = str_replace('\\', '/', $fileName);
        foreach ($files as $file) {
            $name = $file->getName();
            $isFileFound = strpos($fileName, $name) + strlen($name) === strlen($fileName);
            if ($isFileFound) {
                return $file;
            }
        }
        return false;
    }

    /**
     * @param string $projectPath
     * @return static
     */
    public function setProjectPath($projectPath)
    {
        $this->projectPath = $projectPath;
        return $this;
    }

    /**
     * @param array $config
     * @return static
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param array $vendorBinPaths
     */
    public function setVendorBinPaths($vendorBinPaths)
    {
        $this->vendorBinPaths = $vendorBinPaths;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
