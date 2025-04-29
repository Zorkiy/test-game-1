<?php

/**
 * Головний вхідний файл програми.
 *
 * Ініціалізує сесію, реєструє автозавантажувач класів, підключає основні функції та
 * маршрутизатор, після чого запускає процес маршрутизації запитів.
 *
 * @package test-game-1
 * @author vladimirovichser@gmail.com
 * @version 1.0.0
 * @since 2025-04-27
 */

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
