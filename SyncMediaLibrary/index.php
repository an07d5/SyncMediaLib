<?php


use cli\App;

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

(new App())->run();