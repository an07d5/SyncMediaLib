<?php


namespace app;


use ErrorException;

class DisabledPlaylistsSource
{
    const PATH = __DIR__ . DIRECTORY_SEPARATOR . 'DisabledPlaylistsSource.txt';

    public static function add(string $name)
    {
        $names = self::get();
        $names[] = $name;
        $names = array_unique($names);
        self::set($names);
    }

    public static function remove(string $name)
    {
        $names = self::get();
        $key = array_search($name, $names);
        if ($key !== false) {
            unset($names[$key]);
            $names = array_values($names);
        }
        self::set($names);
    }

    public static function get(): array
    {
        if (is_file(self::PATH) && is_readable(self::PATH)) {
            return json_decode(file_get_contents(self::PATH));
        }
        return [];
    }

    private static function set(array $names)
    {
        $json = json_encode($names);
        if (touch(self::PATH)) {
            file_put_contents(self::PATH, $json);
        } else {
            throw new ErrorException('Can not write the file: ' . self::PATH);
        }
    }
}