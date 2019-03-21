<?php

function autoload_base($classname) {
    $files = [
        CORE . '/' . $classname . '.php',
        APP . '/' . $classname . '.php',
    ];
    foreach ($files as $filename) {
        if (is_file($filename)) {
            include_once $filename;
            break;
        }
    }
}

function autoload_core($classname) {
    $paths = [
        '/basic/',
        '/components/',
        '/models/',
    ];

    foreach ($paths as $path) {
        $filename = CORE . $path . $classname . '.php';
        if (is_file($filename)) {
            include_once $filename;
            break;
        }
    }
}

function autoload_core_model($classname) {
    $filename = CORE . '/models/' . $classname . '.php';
    if (is_file($filename)) {
        require_once $filename;
        $classname::init();
    }
}

function autoload_module($classname) {
    $modulesDir = APP . '/modules';
    $modules = scandir($modulesDir);
    $skip = array('.', '..');
    foreach ($modules as $dir) {
        if(!in_array($dir, $skip)) {
            if(is_dir($modulesDir . '/' . $dir)) {
                $filename = $modulesDir . '/' . $dir . '/' . $classname . '.php';
                if(file_exists($filename)) {
                    require_once $filename;
                }
            }
        }
    }
}

spl_autoload_register('autoload_base');
spl_autoload_register('autoload_core');
spl_autoload_register('autoload_core_model');
spl_autoload_register('autoload_module');