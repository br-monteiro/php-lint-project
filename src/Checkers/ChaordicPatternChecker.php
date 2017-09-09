<?php
namespace KR04\Checkers;

use KR04\Files\Loader;
use KR04\Checkers\Checker;

class ChaordicPatternChecker extends Checker
{

    public function __construct(Loader $loader)
    {
        parent::__construct($loader);
    }

    protected function configure(): Checker
    {
        /**
         * reset array of patterns
         */
        $this->arrPattern = [];

        /**
         *  pattern => description
         */
        $this->arrPattern['/dump\(/'] = '[bg-red][white]Não pode haver [bg-blue]*dump(...)[/]';
        $this->arrPattern['/\s{5,}\n/'] = '[bg-red][white]There are much whitespaces[/]';

        return $this;
    }
}
