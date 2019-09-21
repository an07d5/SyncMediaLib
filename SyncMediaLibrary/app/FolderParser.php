<?php


namespace app;


use ErrorException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class FolderParser
{

    private $path;

    /**
     * FolderParser constructor.
     */
    public function __construct()
    {
        $this->path = Conf::getFolderPath();
    }

    public function parse(): array
    {
        if (!is_dir($this->path)) {
            throw new ErrorException('Destination folder not specified');
        }
        if (!is_writeable($this->path)) {
            throw new ErrorException('Destination folder is not writable');
        }

        $directory = new RecursiveDirectoryIterator($this->path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory);
        $result = [];
        foreach ($iterator as $info) {
            /** @var SplFileInfo $info */
            $item = [];
            $item['path'] = $info->getRealPath();
            $result[basename($info->getPath())][] = $item;
        }
        return $result;
    }
}