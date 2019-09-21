<?php


namespace app;


use app\xml\XmlParser;

class MediaLibParser
{
    private $result;
    private $systemPlaylists = ['Медиатека', 'Загружено', 'Музыка', 'Library', 'Downloaded', 'Music'];
    private static $instance;

    private function __construct()
    {
        $this->result = $this->parse();
    }

    private function parse(): array
    {
        $lib = (new XmlParser())->parse();
        $result = [];
        if (isset($lib['Playlists'])) {
            foreach ($lib['Playlists'] as $playlist) {
                if (isset($playlist['Folder']) && $playlist['Folder']) {
                    continue;
                }
                if (!isset($playlist['Playlist Items'])) {
                    continue;
                }
                if (in_array($playlist['Name'], $this->systemPlaylists)) {
                    continue;
                }
                foreach ($playlist['Playlist Items'] as $i => $playlistItem) {
                    if (strcmp($lib['Tracks'][$playlistItem['Track ID']]['Track Type'], 'File') !== 0) {
                        continue;
                    }
                    $item = [];
                    $item['path'] = urldecode($lib['Tracks'][$playlistItem['Track ID']]['Location']);
                    $item['name'] = $lib['Tracks'][$playlistItem['Track ID']]['Name'];
                    $item['number'] = $i + 1;
                    $result[$playlist['Name']][] = $item;
                }
            }
        }
        return $result;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}