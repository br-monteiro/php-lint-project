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
        $this->arrPattern['/<[?%]{1}\n/'] = "[white][bg-blue]PSR-1 2.1. PHP Tags[/]\n"
            . "[bg-red]PHP code MUST use the long [bg-blue]<?php ?>[bg-red] tags"
            . " or the short-echo [bg-blue]<?= ?>[bg-red] tags;\n"
            . "it MUST NOT use the other tag variations.[/]";
        // Class constants
        $this->arrPattern['/.*const\s[a-z\d_ ]+/'] = "[white][bg-blue]PSR-1 4.1. Constants[/]\n"
            . "[bg-red]Class constants MUST be declared in all [bg-blue]upper"
            . " case[bg-red] with underscore separators.[/]";
        // Properties
        $this->arrPattern['/.*\$(?:[^a-z].+|.*_.*)\b/'] = "[white][bg-blue]PSR-1 4.2. Properties[/]\n"
            . "[bg-red]This guide intentionally avoids any recommendation"
            . " regarding the use of \$StudlyCaps, \$camelCase, or \$under_score"
            . " property names.\n"
            . "Whatever naming convention is used SHOULD be applied consistently"
            . " within a reasonable scope. That scope may be vendor-level,"
            . " package-level, class-level, or method-level.[/]";
        // Methods
        $this->arrPattern['/.*function\s(?:[^a-z].+|.*_.*)/'] = "[white][bg-blue]PSR-1 4.3. Methods[/]\n"
            . "[bg-red]Method names MUST be declared in [bg-blue]camelCase().[/]";
        // Limit on line
        $this->arrPattern['/.{121,}\n?/'] = "[white][bg-blue]PSR-2 2.3. Lines[/]\n"
            . "[bg-red]The soft limit on line length MUST be 120 characters;"
            . " automated style checkers MUST warn but MUST NOT error at the soft limit.[/]";
        // Indenting
        $this->arrPattern['/.*\t\n?/'] = "[white][bg-blue]PSR-2 2.4. Indenting[/]\n"
            . "[bg-red]Code MUST use an indent of 4 spaces, and MUST NOT use tabs for indenting.[/]";
        // Properties
        $this->arrPattern['/.+(public|protected|private)\s\$\w+[= ]{1,3}.+?;\s(public|protected|private)?\s\$\w+[= ]{1,3}.+/'] = ""
            . "[white][bg-blue]PSR-2 4.2. Properties[/]\n"
            . "[bg-red]There MUST NOT be more than one property declared per statement.[/]";
        // Properties
        $this->arrPattern['/.+?var\s\$\w+.+/'] = ""
            . "[white][bg-blue]PSR-2 4.2. Properties[/]\n"
            . "[bg-red]The var keyword MUST NOT be used to declare a property.[/]";
        // Properties
        $this->arrPattern['/.+(protected|private)\s\$_\w+[= ]{1,3}.+?;/'] = ""
            . "[white][bg-blue]PSR-2 4.2. Properties[/]\n"
            . "[bg-red]Property names SHOULD NOT be prefixed with a"
            . " single underscore to indicate protected or private visibility.[/]";
        // Methods abstract, final, and static
        $this->arrPattern['/.*(abstract|final)\s(static|function)+\s/'] = ""
            . "[white][bg-blue]PSR-2 4.5. abstract, final, and static[/]\n"
            . "[bg-red]When present, the abstract and final declarations"
            . " MUST precede the visibility declaration.[/]";

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
