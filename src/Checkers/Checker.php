<?php
namespace KR04\Checkers;

use KR04\Files\Loader;

abstract class Checker
{

    use CheckersCommonTrait;

    protected $loader;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
        $this->configure();
    }

    abstract protected function check(): Checker;

    abstract protected function configure(): Checker;

    /**
     * This method can be used as Middleware before run check
     */
    public function run()
    {
        $this->check();
    }
}
