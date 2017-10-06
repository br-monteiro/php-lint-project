<?php
namespace KR04\Files;

use KR04\Config\Config;
use KR04\Exceptions\LoaderFileException;

class Loader
{

    /**
     * No readable!
     *
     * @var array
     */
    private $notRead = ['.', '..'];

    /**
     * @var array Array contains the content file
     */
    private $output;

    /**
     * @var \RecursiveIteratorIterator Instance of \RecursiveIteratorIterator
     */
    private $iterator;

    /**
     * @var KR04\Config\Config
     */
    private $config;

    public function __construct($path)
    {
        if (!file_exists($path)) {
            throw new LoaderFileException("The {$path} directory couldn't be loaded.");
        }

        $directory = new \RecursiveDirectoryIterator($path);
        $this->iterator = new \RecursiveIteratorIterator($directory);
        $this->config = new Config();

        $this->scoutDirectory();
    }

    /**
     * Scout the root directory and validate
     * @return void No return!
     */
    private function scoutDirectory()
    {
        $arrTempResults = [];

        foreach ($this->iterator as $info) {

            if ($this->isIgnoringDirectory($info->getPathname())) {
                continue;
            }

            if ($this->isIgnoringFile($info->getPathname())) {
                continue;
            }

            $keys = $this->extractPatternArrayKeys($info->getPathname());

            if (!$keys) {
                continue;
            }

            $fileContent = $this->loadFile($info->getPathname());

            if (!$fileContent) {
                continue;
            }

            eval('$arrTempResults' . $keys . ' = ' . $fileContent . ';');
        }

        $this->output = $arrTempResults;
    }

    /**
     * Return an array contains the files content.
     * Can also be used passing the index of array (string|array)
     *
     * @param string $only
     * @return array Content of files
     */
    public function getOutput($only = null)
    {
        if ($only && array_key_exists($only, $this->output)) {

            return $this->output[$only];
        }

        return $this->output;
    }

    /**
     * Prepare the index of array
     *
     * In case of the file or the directory is not readable, return false
     * In case of the file is an invalid extension, return false
     *
     * @param string $path Path the file
     * @return boolean|string
     */
    private function extractPatternArrayKeys($path)
    {
        $arrPathExploded = $path;

        if (strstr($path, Config::DS) !== false) {

            $arrPathExploded = explode(Config::DS, $path);
        }

        if ($arrPathExploded[0] == "") {

            unset($arrPathExploded[0]);
        }

        if (end($arrPathExploded) == "") {

            unset($arrPathExploded[count($arrPathExploded) - 1]);
        }

        if (!$this->isReadable(end($arrPathExploded))) {
            return false;
        }

        if (!$this->isValidExtension(end($arrPathExploded))) {
            return false;
        }

        $pathFormated = implode($arrPathExploded, Config::DS);

        // it's maked as keys of the array
        return "['" . $pathFormated . "']";
    }

    /**
     * Load the files
     *
     * In case of the path is not a file, return false
     *
     * @param string $filename Path of the file
     * @return boolean|string
     * @throws LoaderFileException
     */
    private function loadFile($filename)
    {
        if (!file_exists($filename)) {
            throw new LoaderFileException("The {$filename} file couldn't be loaded.");
        }

        if (!is_file($filename)) {
            return false;
        }

        return var_export([
            'string' => file_get_contents($filename),
            'array' => file($filename)
            ], true);
    }

    /**
     * Verify if is readable
     *
     * @param string $name Name of the file or directory
     * @return bool
     */
    private function isReadable($name)
    {
        return (bool) !array_search($name, $this->notRead);
    }

    /**
     * Verify if the file having a valid extension
     *
     * @param string $name Name of de file
     * @return bool
     */
    private function isValidExtension($name)
    {
        $extension = explode('.', $name);
        return array_search(trim(end($extension)), $this->config->getAllowedExtension()) !== false;
    }

    /**
     * Verify if the directory is ignored
     *
     * @param string $path
     * @return bool
     */
    private function isIgnoringDirectory($path)
    {
        $arrIgnoreDirectory = $this->config->getIgnoreDirectory();

        if (count($arrIgnoreDirectory) == 0) {
            return false;
        }

        foreach ($arrIgnoreDirectory as $dirname) {

            if (strstr($path, $dirname)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify if the file is ignored
     *
     * @param string $path
     * @return bool
     */
    private function isIgnoringFile($path)
    {
        $arrIgnoreFile = $this->config->getIgnoreFile();

        if (count($arrIgnoreFile) == 0 || !is_file($path)) {
            return false;
        }

        foreach ($arrIgnoreFile as $filename) {

            if (strstr($path, $filename)) {
                return true;
            }
        }

        return false;
    }
}
