<?php


namespace app;


class Conf
{
    private static $mediaLibPath = '';
    private static $folderPath = '';

    /**
     * @return string
     */
    public static function getMediaLibPath(): string
    {
        return self::$mediaLibPath;
    }

    /**
     * @param string $mediaLibPath
     */
    public static function setMediaLibPath(string $mediaLibPath): void
    {
        self::$mediaLibPath = $mediaLibPath;
    }

    /**
     * @return string
     */
    public static function getFolderPath(): string
    {
        return self::$folderPath;
    }

    /**
     * @param string $folderPath
     */
    public static function setFolderPath(string $folderPath): void
    {
        self::$folderPath = $folderPath;
    }
}