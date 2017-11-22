<?php
namespace KR04\Cli;

class Params
{

    private $params = [];
    private $reservedWord = [
        'index.php',
        'checker-kr04'
    ];

    public function __construct(array $rawInputParams = [])
    {
        $this->prepareParams($rawInputParams);
    }

    public function getParams($index)
    {
        if ($index && array_key_exists($index, $this->params)) {
            return $this->params[$index];
        }

        return in_array($index, $this->params);
    }

    private function prepareParams(array $rawInputParams)
    {
        if (!$this->existsParams($rawInputParams)) {
            return;
        }

        $this->params = $this->extractParams($rawInputParams);
    }

    private function existsParams(array $rawInputParams)
    {
        $pattern = '/<#>--\w+(=\w)?/';
        $joinCommands = implode('<#>', $rawInputParams);

        return (bool) preg_match($pattern, $joinCommands);
    }

    private function isReservedWord($word)
    {
        return in_array($word, $this->reservedWord);
    }

    private function extractValues($value)
    {
        $extractedValue = explode('=', $this->cleanParam($value));

        return $extractedValue;
    }

    private function extractParams(array $rawInputParams)
    {
        $arrParams = [];

        foreach ($rawInputParams as $value) {
            $extractValue = $this->extractValues($value);

            if ($this->isReservedWord($extractValue[0])) {
                continue;
            }

            if (isset($extractValue[1])) {
                $arrParams[$extractValue[0]] = $extractValue[1];
            } else {
                $arrParams[] = $extractValue[0];
            }
        }
        return $arrParams;
    }

    private function cleanParam($value)
    {
        return str_replace('--', '', $value);
    }
}
