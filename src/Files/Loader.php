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
            'array' => preg_replace("/\n/", '', file($filename))
            ], true);
    }

    private function isReadable($name): bool
    {
        return (bool) !array_search($name, $this->notRead);
    }

    private function isValidExtension($name): bool
    {
        $extension = explode('.', $name);
        return array_search(trim(end($extension)), Config::ALLOWED_EXTENSION) !== false;
    }
}
