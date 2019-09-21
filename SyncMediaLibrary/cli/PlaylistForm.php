<?php


namespace cli;


class PlaylistForm
{
    private $playlists = [];
    private $inputList = [];
    private $errors = [];

    /**
     * EditForm constructor.
     */
    public function __construct()
    {
        $this->playlists = Playlist::findAll();
    }

    public function load(string $input): bool
    {
        $this->inputList = $this->inputToList($input);
        return !empty($this->inputList);
    }

    private function inputToList(string $input): array
    {
        if (preg_match('/^\s*(\d+)-(\d+)\s*$/', $input, $matches)) {
            $start = intval($matches[1]);
            $end = intval($matches[2]);
            $count = abs($start - $end);
            if ($count >= 100) {
                return ['Too large range specified'];
            }
            return range($start, $end);
        }

        $list = array_filter(explode(' ', $input), function ($item) {
            return trim($item) !== '';
        });
        return array_map(function ($item) {
            if (is_numeric($item)) {
                return intval($item);
            } else {
                return $item;
            }
        }, $list);
    }

    public function validate(): bool
    {
        $listAvailable = $this->playlistsToIds($this->playlists);
        $this->errors = array_diff($this->inputList, $listAvailable);
        return empty($this->errors);
    }

    private function playlistsToIds(array $playlists): array
    {
        return array_map(function (Playlist $playlist) {
            return $playlist->getId();
        }, $playlists);
    }

    public function save()
    {
        foreach ($this->playlists as $playlist) {
            /** @var Playlist $playlist */
            if (in_array($playlist->getId(), $this->inputList)) {
                $playlist->setIsEnabled(!$playlist->isEnabled());
                $playlist->save();
            }
        }
    }

    public function getPlaylists(): array
    {
        return $this->playlists;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

}