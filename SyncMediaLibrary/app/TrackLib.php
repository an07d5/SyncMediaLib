<?php


namespace app;


class TrackLib extends BaseTrack
{
    /**
     * @var Playlist
     */
    private $playlist;
    private $name;
    private $number;

    public static function findEnabled(): array
    {
        $mediaLib = MediaLibParser::getInstance()->getResult();
        $playlists = Playlist::findAll();
        $result = [];
        foreach ($mediaLib as $playlistName => $listItem) {
            $playlist = Playlist::findByName($playlistName, $playlists);
            if (!$playlist->isEnabled()) {
                continue;
            }
            foreach ($listItem as $item) {
                $track = new self();
                $track->path = $item['path'];
                $track->name = $item['name'];
                $track->number = $item['number'];
                $track->playlist = $playlist;
                $result[] = $track;
            }
        }
        return $result;
    }

    public function createDstPath(): string
    {
        $root = rtrim(Conf::getFolderPath(), DIRECTORY_SEPARATOR);
        $folder = $this->playlist->getName();
        $extension = pathinfo($this->path, PATHINFO_EXTENSION);
        $file = sprintf("%'02d %s.%s", $this->number, $this->name, $extension);
        return join(DIRECTORY_SEPARATOR, [$root, $folder, $file]);
    }
}