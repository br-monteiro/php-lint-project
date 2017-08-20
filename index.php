<?php
// require the Composer's autoload
require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

try {

    // init the verification into the files
    new KR04\Linter();
} catch (\Exception $ex) {

    \KR04\System\Colorize::show($ex);
}
