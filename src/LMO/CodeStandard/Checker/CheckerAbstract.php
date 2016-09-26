<?php

namespace LMO\CodeStandard\Checker;

use LMO\CodeStandard\File\File;
use LMO\CodeStandard\File\Files;

abstract class CheckerAbstract
{
    protected $vendorDirectories;
    protected $scriptPath;
    protected $extensions = [];
    protected $config = [];

    private $name;

    public function __construct()
    {
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
     * @param array $config
     * @return static
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param array $vendorDirectories
     * @return static
     */
    public function setVendorDirectories($vendorDirectories)
    {
        $this->vendorDirectories = $vendorDirectories;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $scriptPath
     * @return static
     */
    public function setScriptPath($scriptPath)
    {
        $this->scriptPath = $scriptPath;
        return $this;
    }
}