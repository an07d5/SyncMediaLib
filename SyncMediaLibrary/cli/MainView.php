<?php


namespace cli;


use app\Event;
use app\EventProgress;

class MainView
{
    /**
     * MainView constructor.
     */
    public function __construct()
    {
    }

    public function edit(PlaylistForm $playlistForm, EventProgress $progress)
    {
        if ($playlistForm->getErrors()) {
            $errors = array_map(function ($item) {
                return "\"$item\"";
            }, $playlistForm->getErrors());
            $errorsStr = join(', ', $errors);
            echo "Invalid playlist numbers: $errorsStr. Please try again\n";
            return;
        }

        foreach ($playlistForm->getPlaylists() as $playlist) {
            /** @var Playlist $playlist */
            printf("%s %2d. %s (%d)\n",
                $playlist->isEnabled() ? '+' : '-',
                $playlist->getId(),
                $playlist->getName(),
                $playlist->getNumberOfItems()
            );
        }
        $mbToCopy = round($progress->totalBytesToCopy / 1024 / 1024);
        printf("Will be copied %d files %dMB, removed %d files\n",
            $progress->totalFilesToCopy,
            $mbToCopy,
            $progress->totalFilesToRemove
        );

        if ($progress->notEnoughSpace > 0) {
            $mb = ceil($progress->notEnoughSpace / 1024 / 1024);
            printf("!!!Warning!!! Not enough disk space %dMB\n", $mb);
        }
    }

    public function sync(Event $event)
    {
        /** @var EventProgress $progress */
        $progress = $event->sender->getProgress();
        if (!$progress->totalFilesToCopy) {
            return;
        }
        $totalProgressBar = $this->getProgressBar($progress->totalCopyPercent);
        $fileProgressBar = $this->getProgressBar($progress->fileCopyPercent);
        echo sprintf("%s %s\r", $totalProgressBar, $fileProgressBar);
    }

    public function syncDone()
    {
        echo "\nDone!";
    }

    private function getProgressBar($percent)
    {
        $totalLength = 35;
        $length = floor($percent / 100 * $totalLength);
        $spaces = $totalLength - $length;
        $result = '[';
        $result .= str_pad('', $length, '-');
        $result .= '>';
        $result .= str_pad('', $spaces, ' ');
        $result .= ']';
        return $result;
    }

    public function exception($e)
    {
        echo $e->getMessage() . PHP_EOL;
    }
}