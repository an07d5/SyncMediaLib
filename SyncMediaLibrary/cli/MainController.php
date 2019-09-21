<?php


namespace cli;


use app\EventProgress;
use app\Sync;

class MainController
{
    private $view;

    /**
     * MainController constructor.
     */
    public function __construct()
    {
        $this->view = new MainView();
        set_exception_handler([$this->view, 'exception']);
    }

    public function actionEdit(string $input)
    {
        $playlistForm = new PlaylistForm();
        if ($playlistForm->load($input) && $playlistForm->validate()) {
            $playlistForm->save();
        }
        $this->view->edit($playlistForm, $this->getProgress());
    }

    private function getProgress(): EventProgress
    {
        $sync = new Sync();
        $sync->init();
        return $sync->getProgress();
    }

    public function actionSync()
    {
        $sync = new Sync();
        $sync->on(Sync::EVENT_PROGRESS, [$this->view, 'sync']);
        $sync->run();
        $this->view->syncDone();
    }
}