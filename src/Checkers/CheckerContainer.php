<?php
namespace KR04\Checkers;

use KR04\Exceptions\InvalidClassException;

class CheckerContainer
{

    private $arrChecker = [];

    /**
     * Register a new checker to be executed
     * 
     * @return \KR04\Checkers\CheckerContainer
     */
    final public function setChecker(string $checker): CheckerContainer
    {
        if (!class_exists($checker)) {
            throw new InvalidClassException("The Class {$checker} is not valid.");
        }

        $this->arrChecker[] = $checker;

        return $this;
    }

    /**
     * Array of Checkers
     * 
     * @return array
     */
    final public function getChecker(): array
    {
        return $this->arrChecker;
    }
}
