<?php
namespace KR04;

use KR04\Config\Config;
use KR04\Checkers\Checker;
use KR04\Files\Loader;
use KR04\Checkers\CheckerContainer;
use KR04\Exceptions\InvalidClassException;

class Linter
{

    private $loader;

    public function __construct(CheckerContainer $checkers)
    {
        $this->loader = new Loader(Config::ROOT_DIRECTORY);

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
        foreach ($checkers->getChecker() as $checkerStr) {

            $checker = new $checkerStr($this->loader);

            if ($checker instanceof Checker) {

                $checker->run();
                continue;
            }

            throw new InvalidClassException("The Class {$checkerStr} is not a valid Checker.");
        }
    }
}
