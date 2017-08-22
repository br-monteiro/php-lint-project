<?php
namespace KR04\Checkers;

use KR04\Checkers\Checker;
use KR04\Config\Config;
use KR04\Files\Loader;

class ChaordicPatternChecker extends Checker
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
        $this->arrPattern['/dump\(/'] = '[bg-red][white]Não pode haver [bg-blue]*dump(...)[/]';
        $this->arrPattern['/\s{5,}/'] = '[bg-red][white]There are much white spaces[/]';

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
