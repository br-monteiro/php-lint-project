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
    protected function getErrorNumberLine($line)
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
    protected function displayError($str, $content = [])
    {
        Colorize::show("[cyan]" . Config::STRING_SEPARATOR . "\n"
            . '[bg-red][white]' . $str . '[/]');
        $this->displayPerimeterOfCode($this->getErrorNumberLine($str) - 1, $content);
    }

    /**
     * Print the success message
     * 
     * @param string $str The phrasal used to be describe the success
     */
    protected function displaySuccess($str)
    {
        Colorize::show('[green]' . $str . '[/]');
    }

    /**
     * Print the normal message
     * 
     * @param string $str The phrasal used to be describe a normal message
     */
    protected function displayNormal($str)
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
    protected function displayPerimeterOfCode($reference, array $content)
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

            /**
             * Remove new line
             */
            $content[$i] = str_replace("\n", '', $content[$i]);
            $content[$i] = str_replace('$', '\$', $content[$i]);

            if ($i == $reference) {
                Colorize::show("[cyan]" . ($i + 1) . ' [bg-yellow][red]' . $content[$i] . '[/]');
                continue;
            }

            Colorize::show('[cyan]' . ($i + 1) . ' [yellow]' . $content[$i] . '[/]');
        }
    }
}
