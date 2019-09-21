<?php


namespace app;


use ErrorException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Sync extends Component
{
    const EVENT_PROGRESS = 'progress';
    private $progress;
    private $listAdd;
    private $listRemove;

    public function __construct()
    {
        $this->progress = new EventProgress();
    }

    public function init()
    {
        $listSrc = TrackLib::findEnabled();
        $listDst = TrackFolder::findAll();

        $this->listAdd = array_udiff($listSrc, $listDst, [BaseTrack::class, 'compare']);
        $this->listRemove = array_udiff($listDst, $listSrc, [BaseTrack::class, 'compare']);


        $this->progress->totalFilesToCopy = count($this->listAdd);
        $this->progress->totalBytesToCopy = array_reduce($this->listAdd, function ($carry, $item) {
            /** @var BaseTrack $item */
            $carry += filesize($item->getPath());
            return $carry;
        }, 0);
        $this->progress->totalFilesToRemove = count($this->listRemove);
        $this->progress->notEnoughSpace = ($this->progress->totalBytesToCopy - $this->getFreeSpace());
    }

    public function run()
    {
        if ($this->listAdd === null || $this->listRemove === null) {
            $this->init();
        }
        if ($this->progress->notEnoughSpace > 0) {
            throw new ErrorException('Not enough space on device');
        }

        foreach ($this->listRemove as $dst) {
            /** @var BaseTrack $dst */
            $this->remove($dst->getPath());
        }

        foreach ($this->listAdd as $src) {
            /** @var BaseTrack $src */
            $this->copy($src->getPath(), $src->createDstPath());
        }

        $this->removeEmptyFolders();
    }

    private function copy(string $src, string $dst)
    {
        $this->progress->fileBytesToCopy = filesize($src);
        $this->progress->fileBytesCopied = 0;

        $dstOrig = $dst;
        $dst .= '.sync';

        $srcFile = fopen($src, "rb");
        if ($srcFile === false) {
            throw new ErrorException("Could not open file {$src}");
        }
        if (!file_exists(dirname($dst))) {
            if (!mkdir(dirname($dst), 0777, true)) {
                throw new ErrorException("Could not create the directory {$dst}");
            }
        }
        $dstFile = fopen($dst, "w");
        if ($dstFile === false) {
            throw new ErrorException("Could not open file for writing {$dst}");
        }
        while (!feof($srcFile)) {
            $readString = fread($srcFile, 8192);
            if ($readString === false) {
                throw new ErrorException("Could not read file {$src}");
            }
            if (fwrite($dstFile, $readString) === false) {
                throw new ErrorException("Could not write file {$dst}");
            }
            $this->progress->totalBytesCopied += strlen($readString);
            $this->progress->fileBytesCopied += strlen($readString);
            $this->trigger(self::EVENT_PROGRESS);
        }
        fclose($srcFile);
        fclose($dstFile);

        if (!rename($dst, $dstOrig)) {
            throw new ErrorException("Could not rename file {$dst} to {$dstOrig}");
        }

        $this->progress->totalFilesCopied++;
        $this->trigger(self::EVENT_PROGRESS);
    }

    private function remove(string $path)
    {
        if (!unlink($path)) {
            throw new ErrorException("Could not remove file {$path}");
        }
        $this->progress->totalFilesRemoved++;
        $this->trigger(self::EVENT_PROGRESS);
    }

    private function removeEmptyFolders()
    {
        $path = Conf::getFolderPath();
        $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);
        /** @var SplFileInfo $info */
        foreach ($iterator as $info) {
            if ($info->isDir() && !$info->isLink()) {
                $isEmpty = !count(glob($info->getPathname() . DIRECTORY_SEPARATOR . '*'));
                if ($isEmpty) {
                    $isRemoved = rmdir($info->getPathname());
                    if (!$isRemoved) {
                        throw new ErrorException('Could not remove folder ' . $info->getPathname());
                    }
                }
            }
        }
    }

    public function getProgress(): EventProgress
    {
        $this->progress->update();
        return $this->progress;
    }

    private function getFreeSpace(): int
    {
        $freeSpace = (int)disk_free_space(Conf::getFolderPath());
        $freeUpSpace = array_reduce($this->listRemove, function ($carry, $item) {
            $carry += filesize($item->getPath());
            return $carry;
        }, 0);
        return ($freeSpace + $freeUpSpace);
    }
}