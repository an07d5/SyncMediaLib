<?php


namespace cli;


class Playlist extends \app\Playlist
{
    private $id;

    public static function findAll(): array
    {
        $playlists = parent::findAll();
        foreach ($playlists as $i => $playlist) {
            $playlist->id = $i + 1;
        }
        return $playlists;
    }

    public function getId()
    {
        return $this->id;
    }

}