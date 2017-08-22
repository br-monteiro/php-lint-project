<?php
namespace KR04\Checkers;

use KR04\Config\Config;
use KR04\System\Colorize;

trait CheckersCommonTrait
{

    /**
     * Extract and return the line of error
     * 
     * @param string $line
     * @return boolean|int
     */
    protected function getErrorNumberLine(string $line)
    {
        $explodedLine = explode(' on line ', $line);

        if (!isset($explodedLine[1])) {
            return false;
        }

        return (int) $explodedLine[1];
    }

    /**
     * Print the errors message
     * 
     * @param string $str The phrasal used to be describe the error
     * @param array $content Array of lines of the file
     */
    protected function displayError(string $str, array $content = [])
    {
        Colorize::show("[cyan]" . Config::STRING_SEPARATOR . "\n"
            . '[bg-red][white]' . $str . '[/]');
        $this->displayPerimeterOfCoce($this->getErrorNumberLine($str) - 1, $content);
    }

    /**
     * Print the success message
     * 
     * @param string $str The phrasal used to be describe the success
     */
    protected function displaySuccess(string $str)
    {
        Colorize::show('[green]' . $str . '[/]');
    }

    /**
     * Print the normal message
     * 
     * @param string $str The phrasal used to be describe a normal message
     */
    protected function displayNormal(string $str)
    {
        Colorize::show('[blue]' . $str . '[/]');
    }

    /**
     * Print the piece containing code of error
     * 
     * In case of the array length is zero, then return null
     * 
     * @param int $reference Number of line to be focus
     * @param array $content Array of lines of the file
     * @return null null
     */
    protected function displayPerimeterOfCoce(int $reference, array $content)
    {
        $contentLength = count($content);

        if ($contentLength == 0) {
            Colorize::show("[yellow]Nothing to display![/]");
            return;
        }

        $referenceToUp = $reference - Config::REACH_DISPLAY;
        $referenceToDown = $reference + Config::REACH_DISPLAY;

        if ($referenceToUp < 0) {
            $referenceToUp = 0;
        }

        if ($referenceToDown > ($contentLength - 1)) {
            $referenceToDown = $contentLength - 1;
        }

        for ($i = $referenceToUp; $i <= $referenceToDown; $i++) {

            if ($i == $reference) {
                Colorize::show("[cyan]" . ($i + 1) . ' [bg-yellow][red]' . trim($content[$i]) . '[/]');
                continue;
            }

            Colorize::show('[cyan]' . ($i + 1) . ' [yellow]' . trim($content[$i]) . '[/]');
        }
    }
}
