<?php
namespace KR04;

use KR04\Config\Config;
use KR04\Checkers\Checker;
use KR04\Files\Loader;
use KR04\Checkers\{
    SyntaxChecker,
    ChaordicPatternChecker,
    PsrChecker
};

class Linter
{

    private $loader;
    private $arrCheckers = [];

    public function __construct($path = null)
    {
        $this->loader = new Loader($path ?: Config::ROOT_DIRECTORY);

        $this->configure()
            ->runChecker();
    }

    /**
     * Run the checkers
     */
    private function runChecker()
    {
        foreach ($this->arrCheckers as $checker) {
            $checker->run();
        }
    }

    /**
     * Configure the linter with Checkers
     * 
     * @return \KR04\Linter
     */
    private function configure(): Linter
    {
        $this->registerChecker(new SyntaxChecker($this->loader))
            ->registerChecker(new PsrChecker($this->loader))
            ->registerChecker(new ChaordicPatternChecker($this->loader));

        return $this;
    }

    /**
     * Register a new checker to be executed
     * 
     * @param Checker $cheker
     * @return \KR04\Linter
     */
    private function registerChecker(Checker $cheker): Linter
    {
        $this->arrCheckers[] = $cheker;

        return $this;
    }
}
