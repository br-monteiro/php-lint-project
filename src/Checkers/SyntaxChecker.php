<?php
namespace KR04\Checkers;

use KR04\Exceptions\RuntimeProcessException;
use KR04\Files\Loader;
use KR04\Checkers\Checker;

class SyntaxChecker extends Checker
{

    protected $descriptOrSpec;

    public function __construct(Loader $loader)
    {
        parent::__construct($loader);
    }

    protected function check(): Checker
    {
        foreach ($this->loader->getOutput() as $path => $content) {

            $pipes = null;
            $process = proc_open('php -l ' . $path, $this->descriptOrSpec, $pipes);

            if (!is_resource($process)) {
                throw new RuntimeProcessException("Opening php binary (/usr/bin/php) for linting failed.");
            }

            fwrite($pipes[0], '/tmp/errors' . time());
            fclose($pipes[0]);

            $stdout = stream_get_contents($pipes[1]);
            $stdout ? $this->displaySuccess($stdout) : null;
            fclose($pipes[1]);

            $stderr = stream_get_contents($pipes[2]);
            $stderr ? $this->displayError($stderr, $content['array']) : null;
            fclose($pipes[2]);

            proc_close($process);
        }

        return $this;
    }

    protected function configure(): Checker
    {
        $this->descriptOrSpec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];

        return $this;
    }
}
