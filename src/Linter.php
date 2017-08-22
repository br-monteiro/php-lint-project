<?php
namespace KR04;

use KR04\Config\Config;
use KR04\Checkers\Checker;
use KR04\Files\Loader;
use KR04\Checkers\SyntaxChecker;
use KR04\Checkers\TesteChecker;

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

    private function runChecker()
    {
        foreach ($this->arrCheckers as $checker) {
            $checker->run();
        }
    }

    private function configure(): Linter
    {
        $this->registerChecker(new SyntaxChecker($this->loader))
            ->registerChecker(new TesteChecker($this->loader));

        return $this;
    }

    private function registerChecker(Checker $cheker): Linter
    {
        $this->arrCheckers[] = $cheker;

        return $this;
    }
}
