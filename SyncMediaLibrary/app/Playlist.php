<?php


namespace app;


class Playlist
{
    private $name;
    private $isEnabled;
    private $numberOfItems;

    public static function findAll(): array
    {
        $mediaLib = MediaLibParser::getInstance()->getResult();
        $disabledPlaylists = DisabledPlaylistsSource::get();
        $result = [];
        foreach ($mediaLib as $playlistName => $tracks) {
            $playlist = new static();
            $playlist->name = $playlistName;
            $playlist->isEnabled = !in_array($playlistName, $disabledPlaylists);
            $playlist->numberOfItems = count($tracks);
            $result[] = $playlist;
        }
        return $result;
    }

    public static function findByName(string $name, array $playlists): self
    {
        foreach ($playlists as $playlist) {
            if (strcmp($playlist->name, $name) === 0) {
                return $playlist;
            }
        }
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }

    public function save()
    {
        if ($this->isEnabled) {
            DisabledPlaylistsSource::remove($this->name);
        } else {
            DisabledPlaylistsSource::add($this->name);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNumberOfItems()
    {
        return $this->numberOfItems;
    }
}