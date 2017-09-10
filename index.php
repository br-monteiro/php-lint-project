#!/usr/bin/php
<?php
// require the Composer's autoload
require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

try {

    $checkerContainer = new KR04\Checkers\CheckerContainer();
    // register the Checker here!
    $checkerContainer->setChecker(\KR04\Checkers\SyntaxChecker::class);
    $checkerContainer->setChecker(\KR04\Checkers\PsrChecker::class);
    $checkerContainer->setChecker(\KR04\Checkers\ChaordicPatternChecker::class);

    // init the verification into the files
    new KR04\Linter($checkerContainer);
} catch (\Exception $ex) {

    \KR04\System\Colorize::show($ex);
}
