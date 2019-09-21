<?php


namespace app;


class EventProgress extends Event
{
    public $totalBytesToCopy = 0;
    public $totalBytesCopied = 0;

    public $totalFilesToCopy = 0;
    public $totalFilesCopied = 0;

    public $totalFilesToRemove = 0;
    public $totalFilesRemoved = 0;

    public $fileBytesToCopy = 0;
    public $fileBytesCopied = 0;

    public $totalCopyPercent = 0;
    public $fileCopyPercent = 0;

    public $notEnoughSpace = 0;

    public function update()
    {
        $this->updateTotalCopyPercent();
        $this->updateFileCopyPercent();
    }

    private function updateTotalCopyPercent()
    {
        if ($this->totalBytesToCopy) {
            $this->totalCopyPercent = round($this->totalBytesCopied / $this->totalBytesToCopy * 100);
        } else {
            $this->totalCopyPercent = 0;
        }
    }

    private function updateFileCopyPercent()
    {
        if ($this->fileBytesToCopy) {
            $this->fileCopyPercent = round($this->fileBytesCopied / $this->fileBytesToCopy * 100);
        } else {
            $this->fileCopyPercent = 0;
        }
    }
}