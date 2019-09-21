<?php


namespace cli;


use app\Conf;

class App
{
    private $mediaLibPath = 'C:\Users\An\Music\iTunes\iTunes Music Library.xml';
    private $folderPath = 'F:';

    public function __construct()
    {
        Conf::setMediaLibPath($this->mediaLibPath);
        Conf::setFolderPath($this->folderPath);
    }

    public function run()
    {
        $input = '';
        do {
            $controller = new MainController();
            $controller->actionEdit($input);
            $input = readline(':');
        } while (trim($input) !== '');

        $controller->actionSync();
    }
}