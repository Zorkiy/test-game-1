<?php
// public/index.php

session_start();

// Autoloading (basic example, consider using Composer)
spl_autoload_register(function ($className) {
    $path = $_SERVER['DOCUMENT_ROOT'] .'/'. str_replace('\\', '/', $className) . '.php';

    if (file_exists($path)) {
        require_once $path;
    }
	else {
		dd($path);
	}
});

require_once '../functions/main.php';
require_once '../core/Router.php';

$router = new Router();
$router->route();
