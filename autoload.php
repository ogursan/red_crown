<?php
spl_autoload_register(function($className){
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . $path;
});