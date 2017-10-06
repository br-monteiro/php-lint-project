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
    private $rootDirectory = '.' . Config::DS;

    /**
     * List of skipped directories
     *
     * Fill with relative path
     */
    private $ignoreDirectory = [];

    /**
     * List of skipped files
     *
     * Fill with relative path
     */
    private $ignoreFile = [];

    /**
     * Extensions considered for the application
     */
    private $allowedExtension = [];

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

    public function __construct()
    {
        $this->configure();
    }

    private function configure()
    {
        $this->ignoreDirectory = [
            $this->rootDirectory . 'api/',
            $this->rootDirectory . 'css/',
            $this->rootDirectory . 'images/',
            $this->rootDirectory . 'js/',
            $this->rootDirectory . 'mobile/',
            $this->rootDirectory . 'scripts/',
            $this->rootDirectory . 'tmp/',
            $this->rootDirectory . 'vendor/',
            $this->rootDirectory . 'views/'
        ];

        $this->ignoreFile = [
            $this->rootDirectory . 'header-desktop.php',
            $this->rootDirectory . 'template.php',
            $this->rootDirectory . 'neemu-pages.php',
            $this->rootDirectory . 'verify.php',
            $this->rootDirectory . 'footer-desktop.php',
            $this->rootDirectory . 'not-found.php'
        ];

        $this->allowedExtension = ['php'];
    }

    public function getRootDirectory()
    {
        return $this->rootDirectory;
    }

    public function getIgnoreDirectory()
    {
        return $this->ignoreDirectory;
    }

    public function getIgnoreFile()
    {
        return $this->ignoreFile;
    }

    public function getAllowedExtension()
    {
        return $this->allowedExtension;
    }
}
