<?php


namespace app;


class TrackFolder extends BaseTrack
{

    public static function findAll(): array
    {
        $folder = (new FolderParser())->parse();
        $result = [];
        foreach ($folder as $playlistName => $listItem) {
            foreach ($listItem as $item) {
                $track = new self();
                $track->path = $item['path'];
                $result[] = $track;
            }
        }
        return $result;
    }

    public function createDstPath(): string
    {
        return $this->path;
    }
}