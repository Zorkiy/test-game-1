<?php
// public/index.php

// Старт сесії для поточного запиту.
session_start();

// require_once $_SERVER['DOCUMENT_ROOT'] .'/vendor/autoload.php';

/**
 * Реєстрація автозавантажувача класів.
 * @param string $className Назва класу для завантаження
 */
spl_autoload_register(function ($className) {
    $path = $_SERVER['DOCUMENT_ROOT'] .'/'. str_replace('\\', '/', $className) . '.php';

    if (file_exists($path)) {
        require_once $path;
    }
	else {
		dd($path);
	}
});

// Підключення основних функцій
require_once '../functions/main.php';

/**
 * Підключення та ініціалізація маршрутизатора
 */
require_once '../core/Router.php';

/**
 * Створення об'єкта маршрутизатора та виклик методу маршрутизації
 * @var Router $router
 */
$router = new Router();
$router->route();
