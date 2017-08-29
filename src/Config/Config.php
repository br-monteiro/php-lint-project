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
    const ROOT_DIRECTORY = 'filesTest' . self::DS;

    /**
     * List of skipped directories
     *
     * Fill with relative path
     */
    const IGNORE_DIRECTORY = [];

    /**
     * List of skipped files
     *
     * Fill with relative path
     */
    const IGNORE_FILE = [];

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
     * Tag used to start the ignore block code
     */
    const IGNORE = '@ignore';

    /**
     * Tag used to ending the ignore block code
     */
    const END_IGNORE = '@endignore';

}
