<?php


namespace app;


abstract class BaseTrack
{
    /**
     * @var string
     */
    protected $path;

    public static function compare(self $a, self $b): int
    {
        /** Case in file paths may differ */
        $aPath = mb_strtolower($a->createDstPath());
        $bPath = mb_strtolower($b->createDstPath());
        return strcmp($aPath, $bPath);
    }

    abstract public function createDstPath(): string;

    public function getPath(): string
    {
        return $this->path;
    }
}