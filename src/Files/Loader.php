<?php
namespace KR04\Files;

use KR04\Config\Config;
use KR04\Exceptions\LoaderFileException;

class Loader
{

    private $notRead = ['.', '..'];
    private $output;
    private $iterator;

    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new LoaderFileException("O Diretório {$path} não pode ser encontrado.");
        }

        $directory = new \RecursiveDirectoryIterator($path);
        $this->iterator = new \RecursiveIteratorIterator($directory);

        $this->scoutDirectory();
    }

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

    public function getOutput(string $only = null)
    {
        if ($only && array_key_exists($only, $this->output)) {

            return $this->output[$only];
        }

        return $this->output;
    }

    private function extractPatternArrayKeys(string $path)
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

        return "['" . $pathFormated . "']";
    }

    private function loadFile($filename)
    {
        if (!file_exists($filename)) {
            throw new LoaderFileException("O path {$filename} não pode ser acessado.");
        }

        if (!is_file($filename)) {
            return false;
        }

        return var_export([
            'string' => file_get_contents($filename),
            'array' => file($filename)
            ], true);
    }

    private function isReadable(string $name): bool
    {
        return (bool) !array_search($name, $this->notRead);
    }

    private function isValidExtension(string $name): bool
    {
        $extension = explode('.', $name);
        return array_search(trim(end($extension)), Config::ALLOWED_EXTENSION) !== false;
    }

    private function isIgnoringDirectory(string $path): bool
    {
        if (count(Config::IGNORE_DIRECTORY) == 0) {
            return false;
        }

        foreach (Config::IGNORE_DIRECTORY as $dirname) {

            if (strstr($path, $dirname)) {
                return true;
            }
        }

        return false;
    }

    private function isIgnoringFile(string $path): bool
    {
        if (count(Config::IGNORE_FILE) == 0 || !is_file($path)) {
            return false;
        }

        foreach (Config::IGNORE_FILE as $filename) {

            if (strstr($path, $filename)) {
                return true;
            }
        }

        return false;
    }
}
