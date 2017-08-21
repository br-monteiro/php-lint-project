<?php
namespace KR04\System;

class Colorize
{

    /**
     * Colors table of the OS
     * 
     * Only Linux OS is supported \o/
     * 
     * @var array
     */
    private static $colors = [
        'Linux' => [
            '[/]' => '$(tput sgr 0)',
            '[red]' => '$(tput setaf 1)',
            '[bg-red]' => '$(tput setab 1)',
            '[green]' => '$(tput setaf 2)',
            '[bg-green]' => '$(tput setab 2)',
            '[yellow]' => '$(tput setaf 3)',
            '[bg-yellow]' => '$(tput setab 3)',
            '[blue]' => '$(tput setaf 4)',
            '[bg-blue]' => '$(tput setab 4)',
            '[purple]' => '$(tput setaf 5)',
            '[bg-purple]' => '$(tput setab 5)',
            '[cyan]' => '$(tput setaf 6)',
            '[bg-cyan]' => '$(tput setab 6)',
            '[white]' => '$(tput setaf 7)',
            '[bg-white]' => '$(tput setab 7)'
        ],
        'OTHER' => [
            '[/]' => '',
            '[red]' => '',
            '[bg-red]' => '',
            '[green]' => '',
            '[bg-green]' => '',
            '[yellow]' => '',
            '[bg-yellow]' => '',
            '[blue]' => '',
            '[bg-blue]' => '',
            '[purple]' => '',
            '[bg-purple]' => '',
            '[cyan]' => '',
            '[bg-cyan]' => '',
            '[white]' => '',
            '[bg-white]' => ''
        ]
    ];

    /**
     * Replace the tag colors of entry string
     * 
     * @param string $msg The message to be formated
     * @return string The message formated 
     */
    private static function colorizeMsg(string $msg): string
    {
        $os = array_key_exists(PHP_OS, self::$colors) ? PHP_OS : 'OTHER';

        foreach (self::$colors[$os] as $key => $value) {
            $msg = str_replace($key, $value, $msg);
        }

        return $msg;
    }

    /**
     * Colorize the entry string and remove especial char if the OS is not Linux
     * 
     * @param string $str The string to be formated
     * @return string The string formated
     */
    public static function color(string $str): string
    {
        // remove especial character if the OS is not Linux
        if (PHP_OS != "Linux") {
            $str = self::colorizeMsg($str);
            return self::removeEspecialChar($str);
        }

        return self::colorizeMsg($str);
    }

    /**
     * Remove especial character of entry string
     * 
     * @param string $str The string to be removed removed especial char
     * @return string The string formated
     */
    protected static function removeEspecialChar(string $str): string
    {
        $arrCharacter = [
            '/[áàãâä]/ui' => 'a',
            '/[éèêë]/ui' => 'e',
            '/[íìîï]/ui' => 'i',
            '/[óòõôö]/ui' => 'o',
            '/[úùûü]/ui' => 'u',
            '/[ç]/ui' => 'c',
            '/[ÁÀÃÂÄ]/ui' => 'A',
            '/[ÉÈÊË]/ui' => 'E',
            '/[ÍÌÎÏ]/ui' => 'I',
            '/[ÓÒÕÔÖ]/ui' => 'O',
            '/[ÚÙÛÜ]/ui' => 'U',
            '/[Ç]/ui' => 'C'
        ];

        foreach ($arrCharacter as $pattern => $target) {
            $str = preg_replace($pattern, $target, $str);
        }

        return $str;
    }

    /**
     * Print the entry string in console
     * 
     * @param string $str The string to be print
     */
    static public function show(string $str)
    {
        system("echo \"" . self::color($str) . "\"");
    }
}
