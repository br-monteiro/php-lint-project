<?php
namespace KR04\Checkers;

use KR04\Checkers\Checker;
use KR04\Config\Config;
use KR04\Files\Loader;

class PsrChecker extends Checker
{

    protected $arrPattern = [];
    protected $arrErrorsDetected = [];

    public function __construct(Loader $loader)
    {
        parent::__construct($loader);
    }

    protected function configure(): Checker
    {
        /**
         *  pattern => description
         */
        // tags PHP
        $this->arrPattern['/<[?%]{1}\n/'] = "[white][bg-blue]PSR-1 2.1. PHP Tags\n"
            . "[bg-red]PHP code MUST use the long [bg-blue]<?php ?>[bg-red] tags"
            . " or the short-echo [bg-blue]<?= ?>[bg-red] tags;\n"
            . "it MUST NOT use the other tag variations.[/]";
        // Class constants
        $this->arrPattern['/.*const\s[a-z\d_ ]+/'] = "[white][bg-blue]PSR-1 4.1. Constants\n"
            . "[bg-red]Class constants MUST be declared in all [bg-blue]upper"
            . " case[bg-red] with underscore separators.[/]";

        return $this;
    }

    protected function check(): Checker
    {
        foreach ($this->loader->getOutput() as $path => $content) {

            $this->arrErrorsDetected = [];

            foreach ($content['array'] as $referenceLine => $contentLine) {

                $errorsDetected = $this->checkPattern($contentLine);

                if ($errorsDetected) {
                    $this->arrErrorsDetected[$referenceLine] = $errorsDetected;
                }
            }

            if (!empty($this->arrErrorsDetected)) {
                $this->displayNormal('Verifing file [bg-white]' . $path);
                $this->prepareErrorsForDisplay($this->arrErrorsDetected, $content['array']);
            }
        }

        return $this;
    }

    /**
     * Verify the line with Regular Expression
     * 
     * If errors is not detected, return FALSE
     * 
     * @param string $str Line to be verified with Regular Expression
     * @return false|string
     */
    protected function checkPattern(string $str)
    {
        $arrErros = [];

        foreach ($this->arrPattern as $pattern => $description) {
            if (preg_match($pattern, $str)) {
                $arrErros[] = $description;
            }
        }

        return empty($arrErros) ? false : implode("\n", $arrErros);
    }

    /**
     * Prepare errors for display
     * 
     * @param array $errors
     * @param array $content
     */
    protected function prepareErrorsForDisplay(array $errors, array $content)
    {
        foreach ($errors as $numberLine => $errorsContent) {

            $this->displayNormal(Config::STRING_SEPARATOR . "\n"
                . $errorsContent . "[bg-red] on line " . ($numberLine + 1) . '[/]');
            $this->displayPerimeterOfCode($numberLine, $content);
        }
    }
}
