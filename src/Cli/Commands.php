<?php
namespace KR04\Cli;

use KR04\Cli\Params;
use KR04\System\Colorize;

class Commands
{

    private $params;
    private $listOnlyCheckers = false;
    private $listExceptCheckers = false;

    public function __construct(Params $params)
    {
        $this->params = $params;
        $this->normalizeListCheckers();
    }

    public function getParams($index = null)
    {
        return $this->params->getParams($index);
    }

    public function stopExecution($callback, array $callbackParams = [])
    {
        if (
            $this->getParams('stop') === 'true' && is_callable($callback) && $callback($callbackParams)
        ) {
            exit;
        }
    }

    private function normalizeListCheckers()
    {
        $rawListCheckers = $this->getParams('only-checker');
        if ($rawListCheckers) {
            $rawListCheckers = strtolower($rawListCheckers);
            $this->listOnlyCheckers = explode(',', $rawListCheckers);
        }

        $rawListCheckers = $this->getParams('except-checker');
        if ($rawListCheckers) {
            $rawListCheckers = strtolower($rawListCheckers);
            $this->listExceptCheckers = explode(',', $rawListCheckers);
        }
    }

    private function normalizeCheckerName($checkerName)
    {
        $defaultNameSpace = 'KR04\Checkers\\';
        $checkerName = str_replace($defaultNameSpace, '', $checkerName);
        return strtolower($checkerName);
    }

    public function executeOnlyChecker($currentChecker = null)
    {
        if (!is_array($this->listOnlyCheckers)) {
            return true;
        }

        $currentChecker = $this->normalizeCheckerName($currentChecker);

        return in_array($currentChecker, $this->listOnlyCheckers);
    }

    public function executeExceptChecker($currentChecker = null)
    {
        if (!is_array($this->listExceptCheckers)) {
            return true;
        }

        $currentChecker = $this->normalizeCheckerName($currentChecker);

        return !in_array($currentChecker, $this->listExceptCheckers);
    }

    public function listCheckers(array $checkers)
    {
        if ($this->getParams('list-checkers') === 'true') {
            foreach ($checkers as $index => $checker) {
                $normalizedCheckerName = $this->normalizeCheckerName($checker);
                Colorize::show('[bg-white][blue]' . ($index + 1) . '[/] - [yellow]' . $normalizedCheckerName . '[/]');
            }
            exit;
        }
    }
}
