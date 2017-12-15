<?php
namespace KR04;

use KR04\Config\Config;
use KR04\Checkers\Checker;
use KR04\Files\Loader;
use KR04\Checkers\CheckerContainer;
use KR04\Exceptions\InvalidClassException;
use KR04\Cli\Commands;

class Linter
{

    private $loader;
    private $commands;

    public function __construct(CheckerContainer $checkers, Commands $commands)
    {
        $this->commands = $commands;
        $this->loader = new Loader(new Config($commands));

        $this->runChecker($checkers);
    }

    /**
     * Run the checkers
     * 
     * @param CheckerContainer $checkers
     * @throws InvalidClassException
     */
    private function runChecker(CheckerContainer $checkers)
    {
        $this->commands->listCheckers($checkers->getChecker());

        foreach ($checkers->getChecker() as $checkerStr) {
            if (!$this->commands->executeOnlyChecker($checkerStr) || !$this->commands->executeExceptChecker($checkerStr)) {
                continue;
            }

            $checker = new $checkerStr($this->loader, $this->commands);

            if ($checker instanceof Checker) {

                $checker->run();
                continue;
            }

            throw new InvalidClassException("The Class {$checkerStr} is not a valid Checker.");
        }
    }
}
