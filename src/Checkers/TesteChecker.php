<?php
namespace KR04\Checkers;

use KR04\Checkers\Checker;
use KR04\Files\Loader;
use KR04\Cli\Commands;
use KR04\System\Colorize;

class TesteChecker extends Checker
{

    public function __construct(Loader $loader, Commands $commands)
    {
        parent::__construct($loader, $commands);
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
