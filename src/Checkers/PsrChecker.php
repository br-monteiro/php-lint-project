<?php
namespace KR04\Checkers;

use KR04\Checkers\Checker;
use KR04\Files\Loader;

class PsrChecker extends Checker
{

    protected $arrPattern = [];
    protected $arrErrorsDetected = [];
    protected $ignore = false;

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
        //$this->arrPattern['/.*\$([^a-z_].+?(-\>|\s=\s)|.{2,}_.*?(-\>|\s=\s))/'] = "[white][bg-blue]PSR-1 4.2. Properties[/]\n"
        //    . "[bg-red]This guide intentionally avoids any recommendation"
        //    . " regarding the use of \$StudlyCaps, \$camelCase, or \$under_score"
        //    . " property names.\n"
        //    . "Whatever naming convention is used SHOULD be applied consistently"
        //    . " within a reasonable scope. That scope may be vendor-level,"
        //    . " package-level, class-level, or method-level.[/]";
        // Methods
        $this->arrPattern['/function\s(A-Z|[^a-z][^_]\w+)|function\s([a-z]+_\w+)/'] = [
            'message' => "[white][bg-blue]PSR-1 4.3. Methods[/]\n"
            . "[bg-red]Method names MUST be declared in [bg-blue]camelCase().[/]",
            // callback
            'function' => function($matches, $details, $str) {
                // verify if the function is native
                if (is_array($matches) && isset($matches[2]) && !function_exists($matches[2])) {
                    return $details;
                }

                // return empty array
                return [];
            }
        ];

        // Limit on line
        //$this->arrPattern['/.{121,}\n?/'] = "[white][bg-blue]PSR-2 2.3. Lines[/]\n"
        //    . "[bg-red]The soft limit on line length MUST be 120 characters;"
        //    . " automated style checkers MUST warn but MUST NOT error at the soft limit.[/]";
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
        // Method and Function Calls
        $this->arrPattern['/\$?(\w+\([ \t]+.*?[);]?|\w+\(.*?[ ]\);)/'] = ""
            . "[white][bg-blue]PSR-2 4.6. Method and Function Calls[/]\n"
            . "[bg-red]When making a method or function call, there MUST NOT be"
            . " a space between the method or function name and the opening"
            . " parenthesis, there MUST NOT be a space after the opening"
            . " parenthesis, and there MUST NOT be a space before the closing"
            . " parenthesis. In the argument list, there MUST NOT be a space"
            . " before each comma, and there MUST be one space after each comma.[/]";
        // Method and Function Calls
        $this->arrPattern['/.*\w+\(.*\s,\s.*/'] = ""
            . "[white][bg-blue]PSR-2 4.6. Method and Function Calls[/]\n"
            . "[bg-red]... In the argument list, there MUST NOT be a space"
            . " before each comma, and there MUST be one space after each comma.[/]";
        // if, elseif, else
        $this->arrPattern['/(if|elseif)(\s\(.+?\)\{|\(.+?\)\s?\{)|(\}\s?else\{|\}else\s?\{)/'] = ""
            . "[white][bg-blue]PSR-2 5.1. if, elseif, else[/]\n"
            . "[bg-red]An if structure looks like the following. Note the"
            . " placement of parentheses, spaces, and braces; and that else and"
            . " elseif are on the same line as the closing brace from the"
            . " earlier body.[/]";
        // if, elseif, else
        $this->arrPattern['/\}?\s?else\sif\s?\{?/'] = ""
            . "[white][bg-blue]PSR-2 5.1. if, elseif, else[/]\n"
            . "[bg-red]The keyword elseif SHOULD be used instead of else if so"
            . " that all control keywords look like single words.[/]";
        // switch, case
        $this->arrPattern['/switch\(.+?\)|switch\s\(.+?\)(\{|\n)|case\s.+?\:\s(case|default).*/'] = ""
            . "[white][bg-blue]PSR-2 5.2. switch, case[/]\n"
            . "[bg-red]A switch structure looks like the following. Note the"
            . " placement of parentheses, spaces, and braces. The case statement"
            . " MUST be indented once from switch, and the break keyword"
            . " (or other terminating keyword) MUST be indented at the same"
            . " level as the case body. There MUST be a comment such as"
            . " [bg-blue]// no break[bg-red] when fall-through is intentional"
            . " in a non-empty case body.[/]";
        // while, do while
        $this->arrPattern['/while(\s?\(.+?\)(\{|\n)|\(.+?\)\s\{)/'] = ""
            . "[white][bg-blue]PSR-2 5.3. while, do while[/]\n"
            . "[bg-red]Note the placement of parentheses, spaces, and braces.[/]";
        // while, do while
        $this->arrPattern['/\}while\s?\(.+?\)\;|\swhile\(.+?\)\;/'] = ""
            . "[white][bg-blue]PSR-2 5.3. while, do while[/]\n"
            . "[bg-red]Note the placement of parentheses, spaces, and braces.[/]";
        // for
        $this->arrPattern['/\bfor\(.+?\)\s?\{|\bfor\s\(.+?\)(\n|\{)/'] = ""
            . "[white][bg-blue]PSR-2 5.4. for[/]\n"
            . "[bg-red]Note the placement of parentheses, spaces, and braces.[/]";
        // foreach
        $this->arrPattern['/\bforeach\(.+?\)\s?\{|\bforeach\s\(.+?\)(\n|\{)/'] = ""
            . "[white][bg-blue]PSR-2 5.5. foreach[/]\n"
            . "[bg-red]Note the placement of parentheses, spaces, and braces.[/]";
        // try, catch
        $this->arrPattern['/(\}?catch\(.+?\)\s?(\{|\n))|(\}?catch\s?\(.+?\)(\{|\n))|(\}catch\s\(.+?\)\s(\{|\n))/'] = ""
            . "[white][bg-blue]PSR-2 5.6. try, catch[/]\n"
            . "[bg-red]Note the placement of parentheses, spaces, and braces.[/]";

        return $this;
    }
}
