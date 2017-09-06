<?php
namespace KR04\Config;

class Config
{

    /**
     * The directory separator
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Directory of entry to scout
     */
    const ROOT_DIRECTORY = '.' . self::DS;

    /**
     * List of skipped directories
     *
     * Fill with relative path
     */
    const IGNORE_DIRECTORY = [
        self::ROOT_DIRECTORY . '_libs/',
        self::ROOT_DIRECTORY . 'api/',
        self::ROOT_DIRECTORY . 'css/',
        self::ROOT_DIRECTORY . 'images/',
        self::ROOT_DIRECTORY . 'js/',
        self::ROOT_DIRECTORY . 'mobile/',
        self::ROOT_DIRECTORY . 'scripts/',
        self::ROOT_DIRECTORY . 'tmp/',
        self::ROOT_DIRECTORY . 'vendor/',
        self::ROOT_DIRECTORY . 'views/'
    ];

    /**
     * List of skipped files
     *
     * Fill with relative path
     */
    const IGNORE_FILE = [
        self::ROOT_DIRECTORY . 'header-desktop.php',
        self::ROOT_DIRECTORY . 'template.php',
        self::ROOT_DIRECTORY . 'neemu-pages.php',
        self::ROOT_DIRECTORY . 'verify.php',
        self::ROOT_DIRECTORY . 'footer-desktop.php',
        self::ROOT_DIRECTORY . 'not-found.php'
    ];

    /**
     * Extensions considered for the application
     */
    const ALLOWED_EXTENSION = ['php'];

    /**
     * Reach to be shown in bugs
     */
    const REACH_DISPLAY = 3;

    /**
     * String used as separator
     */
    const STRING_SEPARATOR = '------------------';

    /**
     * Tag used to ignore only line
     */
    const IGNORE_LINE = '@ignoreline';

    /**
     * Tag used to start the ignore block code
     */
    const IGNORE = '@ignore';

    /**
     * Tag used to ending the ignore block code
     */
    const END_IGNORE = '@endignore';

}
