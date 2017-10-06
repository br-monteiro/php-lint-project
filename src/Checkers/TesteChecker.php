<?php
namespace KR04\Checkers;

use KR04\Checkers\Checker;
use KR04\Files\Loader;
use KR04\System\Colorize;

class TesteChecker extends Checker
{

    public function __construct(Loader $loader)
    {
        parent::__construct($loader);
    }

    protected function check()
    {
        Colorize::show('[bg-blue][white]This is a new Checker test[/]');

        return $this;
    }

    protected function configure()
    {
        /**
         * Don't need implementation =)
         */
        return $this;
    }
}
