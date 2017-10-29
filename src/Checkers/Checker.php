<?php
namespace KR04\Checkers;

use KR04\Files\Loader;
use KR04\Config\Config;
use KR04\Cli\Commands;

abstract class Checker
{

    use CheckersCommonTrait;

    protected $loader;
    protected $commands;
    protected $ignore = false;

    public function __construct(Loader $loader, Commands $commands)
    {
        $this->loader = $loader;
        $this->commands = $commands;
        $this->configure();
    }

    abstract protected function configure();

    /**
     * This method can be used as Middleware before run check
     */
    public function run()
    {
        $this->check();
    }

    protected function check()
    {
        foreach ($this->loader->getOutput() as $path => $content) {

            $this->arrErrorsDetected = [];

            foreach ($content['array'] as $referenceLine => $contentLine) {

                if ($this->ignoreLine($contentLine)) {
                    continue;
                }

                $errorsDetected = $this->checkPattern($contentLine);

                if ($errorsDetected) {
                    $this->arrErrorsDetected[$referenceLine] = $errorsDetected;
                }
            }

            if (!empty($this->arrErrorsDetected)) {
                $this->displayNormal('[bg-blue][white]Verifying file [bg-white][blue]' . $path);
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
    protected function checkPattern($str)
    {
        $arrErros = [];

        foreach ($this->arrPattern as $pattern => $details) {
            $matches = null;
            $reternCallback = null;
            $regex = (bool) preg_match($pattern, $str, $matches);

            if ($regex && is_array($details) && isset($details['function']) && is_callable($details['function'])) {
                $reternCallback = $details['function']($matches, $details, $str);
            }

            if ($regex && is_array($details) && $reternCallback && isset($reternCallback['message'])) {
                //The callback return the message
                $arrErros[] = $reternCallback['message'];
            }

            if ($regex && is_string($details)) {
                $arrErros[] = $details;
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

    /**
     * Verifying if the line contains the IGNORE tag
     *
     * @param string $str Line of file
     * @return bool
     */
    protected function ignoreLine($str)
    {
        if (strstr($str, Config::IGNORE_LINE) !== false) {
            return true;
        }

        if (!$this->ignore && strstr($str, Config::IGNORE) !== false) {
            $this->ignore = true;
            return true;
        }

        if ($this->ignore && strstr($str, Config::END_IGNORE) !== false) {
            $this->ignore = false;
            return false;
        }

        return $this->ignore;
    }
}
