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

}
